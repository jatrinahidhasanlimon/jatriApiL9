<?php

use Illuminate\Support\Facades\Facade;

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Variable
    |--------------------------------------------------------------------------
    |
    |
    */

    'firebase_push_key' => env('FIREBASE_PUSH_KEY', ''),
    'android_vc' => env('ANDROID_VC', 1),
    'android_mu' => env('ANDROID_MU', 0),
    'google_map_api_key' => env('GOOGLE_MAP_API', ''),
    'tracking_api_key' => env('TRACKING_API_KEY', ''),
    'jatri_user_token' => 'J9vuqzxHyaWa3VaT66NsvmQdmUmwwrHj',

    'is_live_bkash_tokenized' => env('IS_BKASH_LIVE', false),
    'bkash_tokenized_creds_live' => [
        'app_key' => env('BKASH_APP_KEY', ''),
        'app_secret' => env('BKASH_APP_SECRET', ''),
        'base_url' => env('BKASH_BASE_URL', ''),
        'username' => env('BKASH_USERNAME', ''),
        'password' => env('BKASH_PASSWORD', ''),
        'host_url' => env('BKASH_HOST_URL', '')
    ],

    'is_live_portwallet' => env('IS_PORTWALLET_LIVE', false),
    'portwallet_creds_live' => [
        'app_key' => env('PORTWALLET_APP_KEY', ''),
        'app_secret' => env('PORTWALLET_APP_SECRET', ''),
        'api_url' => env('PORTWALLET_API_URL', ''),
        'host_url' => env('PORTWALLET_HOST_URL', '')
    ],

    'nagad_creds' => [
        'host_url'      => env('NAGAD_HOST_URL', ''),
        'post_url'      => env('NAGAD_POST_URL', ''),
        'merchant_id'   => env('NAGAD_MERCHANT_ID', ''),
        'public_key'    => env('NAGAD_PUBLIC_KEY', ''),
        'private_key'   => env('NAGAD_PRIVATE_KEY', '')
    ],

    'sms_gateway' => [
        'base_url' => env('SMS_BASE_URL', ''),
        'username' => env('SMS_USERNAME', ''),
        'password' => env('SMS_PASSWORD', ''),
        'sender_key' => env('SMS_SENDER_KEY', ''),
    ],

    'default_user_id' => 1,

    'gps_device_offline_emails' => env('GPS_DEVICE_OFFLINE_EMAILS', ''),

    'my_radar_authorization_token' => env('MY_RADAR_AUTHORIZATION_TOKEN', ''),
    'finder_device_assets' => env('FINDER_DEVICE_ASSETS', ''),

    'google_cloud_error_report' => [
        'project_name' => env('GOOGLE_CLOUD_ERROR_REPORT_PROJECT_NAME', ''),
        'project_key' => env('GOOGLE_CLOUD_ERROR_REPORT_PROJECT_KEY', ''),
    ],
    'cloud_error_report' => [
        'base_url' => env('CLOUD_ERROR_REPORT_BASE_URL', ''),
        'api_key' => env('CLOUD_ERROR_REPORT_API_KEY', ''),
        'app_name' => env('CLOUD_ERROR_REPORT_APP_NAME', ''),
    ],

    'manual_operation_key' => env('MANUAL_OPERATION_SECRET', ''),
    'spam_otp_request_count' => env('SPAM_OTP_REQUEST_COUNT', 15),
    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    'asset_url' => env('ASSET_URL',null),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => 'Asia/Dhaka',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
    */

    'faker_locale' => 'en_US',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => [

        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,

        /*
         * Package Service Providers...
         */

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,

    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => Facade::defaultAliases()->merge([
        // 'ExampleClass' => App\Example\ExampleClass::class,
    ])->toArray(),

];
