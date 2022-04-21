<?php

namespace App\Http\Controllers\Utility\Common;
use App\Http\Controllers\Controller;
use App\Model\DtIntercityBookingRequest;
use DB;
use FcmPushNotification\FcmPushNotification\PushNotification;

class DigitalTicketingHelper extends Controller
{

    public function scheduleServiceToMaster($booking_id){
        $services_data = DB::table('dt_intercity_booking_services')->where('status', 'PENDING')->where('request_id', $booking_id)->orderBy('priority')->first();
        if($services_data){
            $masters = DB::table('dt_intercity_master_subscriptions')
                ->leftjoin('dt_intercity_masters', 'dt_intercity_masters.id', '=', 'dt_intercity_master_subscriptions.master_id')
                ->where('dt_intercity_master_subscriptions.service_id', $services_data->service_id)
                ->where('dt_intercity_master_subscriptions.boarding_id', $services_data->boarding_id)
                ->where('dt_intercity_masters.activity_status', 1)
                ->select('dt_intercity_masters.id as id', 'dt_intercity_masters.device_token as device_token')
                ->get();
            if(count($masters) > 0){
                $request_filters = [];
                if($services_data->priority == 1) $date_time = date('Y-m-d H:i', strtotime("+1 minutes")).':00';
                else $date_time = date('Y-m-d H:i').':00';
                foreach($masters as $master){
                    $request_filters[] = [
                        'booking_service_id'    => $services_data->id,
                        'request_id'            => $services_data->request_id,
                        'master_id'             => $master->id,
                        'created_at'            => $date_time,
                        'updated_at'            => date('Y-m-d H:i:s'),
                    ];
                    (new PushNotification())->sendToOne( //send push to master
                        $master->device_token,
                        'টিকিটের জন্য রিকোয়েস্ট এসেছে!',
                        'ইউজার টিকিটের জন্য রিকোয়েস্ট প্রদান করেছে। সিট বাছাই করে, রিকোয়েস্ট গ্রহণ করুন।',
                        '',
                        false,
                        [
                            'type'      => 'landing_new_ticket_request',
                            'target'    => ''
                        ]
                    );
                }
                DB::table('dt_intercity_booking_request_filters')->insert($request_filters);
                DB::table('dt_intercity_booking_services')->where('id', $services_data->id)->update(['status' => 'PROCESSING']);
            }else{
                DB::table('dt_intercity_booking_services')->where('id', $services_data->id)->update(['status' => 'CLOSED', 'updated_at' => date('Y-m-d H:i:s')]);
                $this->scheduleServiceToMaster($booking_id);
            }
        }else{
            $hasActiveService = DB::table('dt_intercity_booking_services')->where('status', 'PROCESSING')->where('request_id', $booking_id)->first();
            if($hasActiveService == null){
                $booking = DtIntercityBookingRequest::find($booking_id);
                if($booking){
                    $booking->status                = 'CANCELLED';
                    $booking->cancelled_by          = 'SYSTEM';
                    $booking->cancellation_cause    = 'Request Timeout';
                    $booking->cancelled_at = date('Y-m-d H:i:s');
                    $booking->save();
                }
                (new PushNotification())->sendToOne( //send push to user
                    $booking->user->device_token,
                    'We are sorry!',
                    'Our agents might be busy or your ticket(s) is unavailable. Please send a request again!',
                    '',
                    false,
                    [
                        'type'      => 'dt_homepage',
                        'target'    => ''
                    ]
                );
            }
        }
    }

}
