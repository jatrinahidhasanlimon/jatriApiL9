<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

//        // new
        'loginCheck' => \App\Http\Middleware\loginCheck::class,
        'serviceAdminCheck' => \App\Http\Middleware\serviceAdminCheck::class,
        'jwt.verify' => \App\Http\Middleware\JwtMiddleware::class,
        'auth_merchant_api' => \App\Http\Middleware\JWTMerchantAuth::class,
        'auth_counter_man_api' => \App\Http\Middleware\JWTCounterManAuth::class,
        'auth_counter_man_tab_api' => \App\Http\Middleware\JWTCounterManTabAuth::class,
        'auth_user_api' => \App\Http\Middleware\JWTUserAuth::class,
        'auth_admin_api' => \App\Http\Middleware\JWTAdminAuth::class,
        'jwt_auth_user_api' => \App\Http\Middleware\JWTUserApiAuth::class,
        'jwt_auth_owner_api' => \App\Http\Middleware\JWTOwnerAPIAuth::class,
        'jwt_auth_microbus_administrator_api' => \App\Http\Middleware\JWTMicrobusAdministratorAuth::class,
        'jwt_auth_b2b_driver_api' => \App\Http\Middleware\JWTB2bDriverAuth::class,
        'auth_supervisor_counter_man_api' => \App\Http\Middleware\JWTSupervisorCounterManAuth::class,
        'jwt_auth_airport_tollman_api' => \App\Http\Middleware\JWTAirportTollmanAuth::class,
        'jwt_auth_toll_collector_api' => \App\Http\Middleware\JWTTollPlazaCollectorAuth::class,
        'jwt_auth_fuel_salesman_api' => \App\Http\Middleware\JWTFuelSalesmanAuth::class,
        'jwt_auth_gp_collector_api' => \App\Http\Middleware\JWTGPCollectorAuth::class,
        'jwt_auth_rental_owner_api' => \App\Http\Middleware\JWTRentalOwnerAPIAuth::class,
        'jwt_auth_waybill_checker_api' => \App\Http\Middleware\JWTWaybillCheckerAuth::class,
        'jwt_auth_dt_master_api' => \App\Http\Middleware\JWTDTMasterAPIAuth::class,
        'jwt_auth_offline_counterman_api' => \App\Http\Middleware\JWTOfflineCountermanAPIAuth::class,
    ];
}
