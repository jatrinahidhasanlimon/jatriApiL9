<?php

namespace App\Http\Controllers\Utility\Common;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Utility\CacheHelper;
use App\Model\PromocodeApplied;
use App\Model\RentalOwner;
use DB;
use FcmPushNotification\FcmPushNotification\PushNotification;

class RentalHelper extends Controller
{

    public function partnerRegBonusSettlement($rental_owner){
        $getBonusData = (new CacheHelper())->getRentalVehicleTypeWiseRegBonus();
        if (!empty($getBonusData[$rental_owner->division]) && count($getBonusData[$rental_owner->division]) > 0){
            foreach($getBonusData[$rental_owner->division] as $value){
                if ($value['id'] == 0 && $value['bonus'] > 0){
                    //DB::table('rental_owners')->where('id', $rental_owner->id)->increment('bonus_credit', $value['bonus']);
                    DB::table('rental_owners')->where('id', $rental_owner->id)->increment('credit', $value['bonus']);
                    DB::table('rental_owner_credit_transactions')->insert([
                        'owner_id'      => $rental_owner->id,
                        'previous'      => 0,
                        'processed'     => $value['bonus'],
                        'current'       => $value['bonus'],
                        'type'          => 'CREDIT',
                        'category'      => 'REG_BONUS',
                        'remark'        => 'Registration bonus credit',
                        'created_at'    => date('Y-m-d H:i:s'),
                        'updated_at'    => date('Y-m-d H:i:s'),
                    ]);
                }
            }
        }
    }

    public function partnerRefBonusSettlement($owner_id, $service_type_id){
        $service_type = DB::table('rental_service_types')->where('id', $service_type_id)->first();
        if ($service_type->referral_bonus > 0 && $service_type->referral_bonus_status == 1){
            $owner = DB::table('rental_owners')->where('id', $owner_id)->first(); //RentalOwner::find($owner_id);
            if ($owner->ref_by != NULL) {
                $trip_count = DB::table('rental_bookings')->where('owner_id', $owner->id)
                    ->where('status', 'COMPLETED')
                    ->where('service_type_id', $service_type->id)
                    ->count();

                if ($trip_count == 0){
                    $ref_by_owner = RentalOwner::find($owner->ref_by);
                    if($ref_by_owner == null) return;
                    DB::table('rental_owner_credit_transactions')->insert([
                        'owner_id'      => $owner->ref_by,
                        'previous'      => $ref_by_owner->credit + $ref_by_owner->bonus_credit,
                        'processed'     => $service_type->referral_bonus,
                        'current'       => $ref_by_owner->credit + $ref_by_owner->bonus_credit + $service_type->referral_bonus,
                        'type'          => 'CREDIT',
                        'category'      => 'REFERRAL_BONUS',
                        'remark'        => 'Credit added as referral bonus for completion of first trip by '.$owner->name.' with '.$service_type->name,
                        'created_at'    => date('Y-m-d H:i:s'),
                        'updated_at'    => date('Y-m-d H:i:s'),
                    ]);
                    //$ref_by_owner->bonus_credit = $ref_by_owner->bonus_credit + $service_type->referral_bonus;
                    $ref_by_owner->credit = $ref_by_owner->credit + $service_type->referral_bonus;
                    $ref_by_owner->save();
                }
            }
        }
    }

    public function userAppliedPromoSettlementWithPartner($booking, $promo){
        $owner = RentalOwner::find($booking->owner_id);
        if($owner == null) return;
        $check_credit_payment = DB::table('rental_owner_credit_transactions')
            ->where('owner_id', $booking->owner_id)
            ->where('type', 'CREDIT')
            ->where('remark', 'like', '%'.$booking->booking_id.'%')
            //->where('booking_id', $booking->booking_id)
            ->first();
        if($check_credit_payment == null){
            DB::table('rental_owner_credit_transactions')->insert([
                'owner_id'      => $booking->owner_id,
                'previous'      => $owner->credit + $owner->bonus_credit,
                'processed'     => $booking->discount,
                'current'       => $owner->credit + $owner->bonus_credit + $booking->discount,
                'type'          => 'CREDIT',
                'category'      => 'CUSTOMER_BONUS',
                'remark'        => 'Credit added (Customer Bonus) for booking id: '.$booking->booking_id,
                'booking_id'    => $booking->id,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);
            $owner->credit = $owner->credit + $booking->discount;
            $owner->save();
            if($promo){
                DB::table('promocode_applieds')->where('promocode_id', $promo->id)->increment('used_count');

                $this->updateAvailablePromocodes($booking->user_id);
            }
        }
    }

    public function deductCreditForBookingConfirmation($booking){
        $owner = RentalOwner::find($booking->owner_id);
        if($owner == null) return;
        $check_credit_payment = DB::table('rental_owner_credit_transactions')
            ->where('owner_id', $booking->owner_id)
            ->where('type', 'DEBIT')
            ->where('remark', 'like', '%'.$booking->booking_id.'%')
            //->where('booking_id', $booking->booking_id)
            ->first();
        if($check_credit_payment == null){
            $total_credit_need_to_deduct = $booking->owner_commission + $booking->user_commission;
            if($owner->bonus_credit > 0 && $total_credit_need_to_deduct <= $owner->bonus_credit){
                $from_bonus_credit  = $total_credit_need_to_deduct;
                $from_credit        = 0;
            }else if($owner->bonus_credit > 0 && $total_credit_need_to_deduct > $owner->bonus_credit){
                $from_bonus_credit  = $owner->bonus_credit;
                $from_credit        = $total_credit_need_to_deduct - $owner->bonus_credit;
            }else{
                $from_bonus_credit  = 0;
                $from_credit        = $total_credit_need_to_deduct;
            }

            DB::table('rental_owner_credit_transactions')->insert([
                'owner_id'              => $booking->owner_id,
                'previous'              => $owner->credit + $owner->bonus_credit,
                'processed'             => $total_credit_need_to_deduct,
                'current'               => $owner->credit + $owner->bonus_credit - $total_credit_need_to_deduct,
                'type'                  => 'DEBIT',
                'category'              => 'COMMISSION',
                'remark'                => 'Credit deducted for booking id: '.$booking->booking_id,
                'booking_id'            => $booking->id,
                'from_credit'           => $from_credit,
                'from_bonus_credit'     => $from_bonus_credit,
                'owner_commission'      => $booking->owner_commission,
                'user_service_charge'   => $booking->user_commission,
                'created_at'            => date('Y-m-d H:i:s'),
                'updated_at'            => date('Y-m-d H:i:s'),
            ]);
            $owner->credit = $owner->credit - $from_credit;
            $owner->bonus_credit = $owner->bonus_credit - $from_bonus_credit;
            $owner->save();
        }
    }

    public function disburseRechargeCredit($owner_id, $amount, $transId, $payment_method = 'SSLCommerz'){
        $exist = DB::table('rental_owner_credit_transactions')
            ->where('payment_tranx_id', $transId)
            ->where('type', 'CREDIT')
            ->first();
        if($exist) return 0;
        $owner = RentalOwner::find($owner_id);
        if($owner == null ) return false;
        DB::table('rental_owner_credit_transactions')->insert([
            'owner_id'          => $owner->id,
            'previous'          => $owner->credit,
            'processed'         => $amount,
            'current'           => $owner->credit + $amount,
            'type'              => 'CREDIT',
            'category'          => 'RECHARGE',
            'remark'            => 'Credited by recharge. TraxID: '.$transId,
            'payment_method'    => $payment_method,
            'payment_tranx_id'  => $transId,
            'created_at'        => date('Y-m-d H:i:s'),
            'updated_at'        => date('Y-m-d H:i:s'),
        ]);

        $owner->credit = $owner->credit + $amount;
        $owner->save();

        (new PushNotification())->sendToOne($owner->device_token, 'Credit Recharge Success', 'আপনার ক্রেডিট রিচার্জ সম্পন্ন হয়েছে। নতুন ক্রেডিটের পরিমাণ '.$owner->credit.' টাকা। ');
    }

    public function updateAvailablePromocodes($user_id){
        $applied_promos = PromocodeApplied::where('user_id', $user_id)->where('status', 1)
            ->with('promocode')->get();
        foreach ($applied_promos as $key => $promo){
            if(time() > strtotime($promo->promocode->expiration_date) || $promo->used_count >= $promo->promocode->usage_limit_per_users){
                $promo->status = false;
                $promo->active = false;
                $promo->save();
            }
        }
    }

    public function calculatePartnerCommission($booking, $bidding_price){
        $service_type = DB::table('rental_service_types')->where('id', $booking->service_type_id)->first();
        $commission_models = json_decode($service_type->commission, true);
        $percentage = $commission_models['for_partner'][$booking->division] ?? $service_type->owner_commission_percentage;
        return round(($percentage / 100) * $bidding_price);
    }

    public function calculateUserServiceCharge($booking, $bidding_price){
        $service_type = DB::table('rental_service_types')->where('id', $booking->service_type_id)->first();
        $commission_models = json_decode($service_type->commission, true);
        $percentage = $commission_models['for_user'][$booking->division] ?? $service_type->user_commission;
        return round(($percentage / 100) * $bidding_price);
    }

}
