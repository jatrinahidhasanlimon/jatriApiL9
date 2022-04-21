<?php

namespace App\Http\Controllers\Utility\Common;
use App\Http\Controllers\Controller;
use App\Model\GamePointStatement;
use App\Model\JTrackingSubscriptionBilling;
use App\Model\JTrackingSubscriptionPackage;
use App\Model\Settings;
use App\User;
use DB;
use FcmPushNotification\FcmPushNotification\PushNotification;

class Promotions extends Controller
{

    public function checkTrackingSubscriptionFixedBonus($user, $unit, $billing){
        try{
            $Offer = Settings::where('key', $unit.'ly_paid_subscription_fixed_bonus')->where('status', true)->first();
            if($Offer){
                $Offer = json_decode($Offer->value);
                if( time() > strtotime($Offer->from_date) && time() < strtotime($Offer->to_date) ){
                    $billing->to_date = date('Y-m-d H:i:s', strtotime("+".$Offer->bonus_day_count.' days', strtotime($billing->to_date)));
                    $billing->save();
                }
            }
        }catch (\Exception $ex){
            ThirdPartyServiceManager::sendErrorReportToCloud($ex);
        }
    }

    public function checkTrackingSubscriptionReferenceBonus($code, $user){
        try{
            if($code == null || $code == '') return;
            $code = strtoupper($code);
            $exist = DB::table('users')->where('my_ref_code', $code)->first();
            if($exist){
                $refOffer = Settings::where('key', 'paid_subscription_referral_bonus')->where('status', true)->first();
                if($refOffer){
                    $refOffer = json_decode($refOffer->value);
                    if($refOffer->subscription_package_id == 1){
                        if($refOffer->subscribed_by_bonus){
                            $this->provideSubscribedByRefBonus($user, $refOffer);
                        }
                        if($refOffer->referred_by_bonus){
                            $this->provideReferredByRefBonus($exist, $refOffer, $code);
                        }
                    }
                }
            }
        }catch (\Exception $ex){
            ThirdPartyServiceManager::sendErrorReportToCloud($ex);
        }
    }

    private function provideSubscribedByRefBonus($user, $refOffer){
        try{
            $package = JTrackingSubscriptionPackage::find($refOffer->subscribed_by_bonus_package_id);
            if($package){
                $hasSubscription = JTrackingSubscriptionBilling::where('user_id', $user->id)->where('to_date', '>=', date('Y-m-d'))->orderBy('id', 'desc')->first();
                if($hasSubscription != null && $package->package_type == 'referral_bonus'){
                    $add_days = round(((strtotime($hasSubscription->to_date) - time()) / (3600 * 24)));
                    $tracking_active_to_date = date('Y-m-d H:i:s', strtotime("+1 ".$package->unit, time()));
                    $hasSubscription->to_date = date('Y-m-d H:i:s', strtotime("+".$add_days.' days', strtotime($tracking_active_to_date)));
                    $hasSubscription->save();

                    //Send notification
                    (new PushNotification())->sendToOne(
                        $user->device_token,
                        'Good News',
                        'You got subscription bonus for using referral code. Thank you.',
                        '',
                        false,
                        ''
                    );
                }
            }
        }catch (\Exception $ex){
            ThirdPartyServiceManager::sendErrorReportToCloud($ex);
        }
    }

    private function provideReferredByRefBonus($user, $refOffer, $code){
        try{
            $package = JTrackingSubscriptionPackage::find($refOffer->referred_by_bonus_package_id);
            if($package && $package->package_type == 'referral_bonus'){
                $hasSubscription = JTrackingSubscriptionBilling::where('user_id', $user->id)->where('to_date', '>=', date('Y-m-d'))->orderBy('id', 'desc')->first();
                $add_days = 0;
                if($hasSubscription != null){
                    $add_days = round(((strtotime($hasSubscription->to_date) - time()) / (3600 * 24)));
                }

                $tracking_active_to_date = date('Y-m-d H:i:s', strtotime("+1 ".$package->unit, time()));
                if($add_days > 0){
                    $tracking_active_to_date = date('Y-m-d H:i:s', strtotime("+".$add_days.' days', strtotime($tracking_active_to_date)));
                }

                $billing = new JTrackingSubscriptionBilling();
                $billing->user_id = $user->id;
                $billing->paid_amount = 0;
                $billing->payment_method = 'ReferralBonus';
                $billing->trx_id = $code;
                $billing->from_date = date('Y-m-d H:i:s');
                $billing->to_date = $tracking_active_to_date;
                $billing->subscription_package_id = $package->id;
                $billing->reference = '';
                $billing->save();
                //Send notification
                (new PushNotification())->sendToOne(
                    $user->device_token,
                    'Good News',
                    'You got subscription bonus for sharing your referral code. Thank you.',
                    '',
                    false,
                    ''
                );
            }
        }catch (\Exception $ex){
            ThirdPartyServiceManager::sendErrorReportToCloud($ex);
        }
    }

    public function checkTrackingSubscriptionDateRangeBonus($user, $billing){
        try{
            $Offer = Settings::where('key', 'paid_subscription_bonus_with_date_range_condition')->where('status', true)->first();
            if($Offer){
                $Offer = json_decode($Offer->value);
                if( time() > strtotime($Offer->from_date) && time() < strtotime($Offer->to_date) ){
                    $package = JTrackingSubscriptionPackage::find($Offer->subscription_package_id);
                    $bonusPackage = JTrackingSubscriptionPackage::find($Offer->subscription_bonus_package_id);
                    if($package && $bonusPackage){
                        $subscriptionCount = JTrackingSubscriptionBilling::where('user_id', $user->id)
                            ->whereBetween('created_at', [$Offer->from_date, $Offer->to_date])
                            ->where('subscription_package_id', $package->id)
                            ->count();
                        if($subscriptionCount == 1){
                            $add_days = round(((strtotime($billing->to_date) - time()) / (3600 * 24)));
                            $tracking_active_to_date = date('Y-m-d H:i:s', strtotime("+1 ".$bonusPackage->unit, time()));
                            $billing->to_date = date('Y-m-d H:i:s', strtotime("+".$add_days.' days', strtotime($tracking_active_to_date)));
                            $billing->save();
                        }
                    }
                }
            }
        }catch (\Exception $ex){
            ThirdPartyServiceManager::sendErrorReportToCloud($ex);
        }
    }

    public function checkMicrobusTicketingDiscount($user_id){
        try{
            $data = [];
            $data['discount_percentage'] = 0; $data['available'] = false; $data['upto'] = 0;
            $Offer = Settings::where('key', 'microbus_ticketing_discount')->where('status', true)->first();
            if($Offer){
                $Offer = json_decode($Offer->value);
                if( time() > strtotime($Offer->from_date) && time() < strtotime($Offer->to_date) ){
                    $bookingTaken = DB::table('micro_ride_bookings')->where('user_id', $user_id)->whereIn('status', ['PAID', 'BOOKED', 'CONFIRMED'])->count();
                    if($bookingTaken < $Offer->booking_count){
                        $data['discount_percentage'] = $Offer->discount_percentage;
                        $data['available'] = true;
                        $data['upto'] = $Offer->upto;
                    }
                }
            }
        }catch (\Exception $ex){
            ThirdPartyServiceManager::sendErrorReportToCloud($ex);
        }finally{
            return $data;
        }
    }

    public function checkSubscriptionBonusPoint($user_id, $subscription_type){
        try{
            $Offer = Settings::where('key', 'paid_subscription_bonus_point_'.$subscription_type)->where('status', true)->first();
            if($Offer){
                $Offer = json_decode($Offer->value);
                if( time() > strtotime($Offer->from_date) && time() < strtotime($Offer->to_date) ){
                    $user = User::find($user_id);
                    $statement = new GamePointStatement();
                    $statement->user_id = $user->id;
                    $statement->type = 'CREDIT';
                    $statement->previous_point = $user->point;
                    $statement->processed_point = $Offer->point;
                    $statement->updated_point = round($Offer->point + $user->point);
                    $statement->description = $Offer->point.' bonus Point added to you account for taking subscription';

                    $user->point = $statement->updated_point;

                    $user->save();
                    $statement->save();
                }
            }
        }catch (\Exception $ex){
            ThirdPartyServiceManager::sendErrorReportToCloud($ex);
        }
    }

}
