<?php

namespace App\Http\Controllers\Utility\Common;

use App\Http\Controllers\Controller;
use Log;

class ThirdPartyServiceManager extends Controller
{
    public static function sendErrorReportToGoogleCloud($message, $filePath = '', $lineNumber = 0, $functionName = '')
    {
        try {
            $data = array();
            $data['serviceContext']['service'] = 'API';
            $data['message'] = $message;
            $data['context']['reportLocation']['filePath'] = $filePath;
            $data['context']['reportLocation']['lineNumber'] = $lineNumber;
            $data['context']['reportLocation']['functionName'] = $functionName;
            $sendData = json_encode($data);

            $url = curl_init('https://clouderrorreporting.googleapis.com/v1beta1/projects/' . config('app.google_cloud_error_report')['project_name'] . '/events:report?key=' . config('app.google_cloud_error_report')['project_key']);
            $header = array(
                'Content-Type:application/json'
            );

            curl_setopt($url, CURLOPT_HTTPHEADER, $header);
            curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($url, CURLOPT_POSTFIELDS, $sendData);
            curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
            $result = curl_exec($url);
            curl_close($url);
            return $result;
        } catch (\Exception $ex) {
        }
    }

    public static function sendErrorReportToCloud(\Exception $ex, $user_id = 0, $user_type = 'unknown', $client_ip = '0.0.0.0')
    {
        try {
            $data = [
                "apiKey" => config('app.cloud_error_report')['api_key'],
                "payloadVersion" => 1,
                "notifier" => [
                    "name" => config('app.cloud_error_report')['app_name'],
                    "version" => "1.0.0",
                    "url" => "#"
                ],
                "events" => [[
                    "exceptions" => [[
                        "errorClass" => $ex instanceof \PHPUnit_Framework_ExceptionWrapper ? $ex->getClassname() : get_class($ex),
                        "message" => $ex->getMessage(),
                        "stacktrace" => [[
                            "file" => $ex->getFile(),
                            "lineNumber" => $ex->getLine(),
                            "columnNumber" => 0,
                            "method" => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,2)[1]['function'] ?? 'NoMethodError',
                            "inProject" => true,
                            "code" => $ex->getTrace()
                        ]],
                        "type" => "API"
                    ]],
                    "severity" => "error",
                    "user" => [
                        "id" => $user_id,
                        "type" => $user_type,
                        "clientIp" => $client_ip
                    ]
                ]]
            ];

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => config('app.cloud_error_report')['base_url'], //'https://notify.bugsnag.com/',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Accept: */*'
                ),
            ));

            curl_exec($curl);
            curl_close($curl);

            Log::channel('slack')->error($ex->getMessage(), collect($ex->getTrace())->take(5)->toArray());
        } catch (\Exception $ex) {
            Log::error('sendErrorReport '.$ex->getMessage());
        }
    }
}
