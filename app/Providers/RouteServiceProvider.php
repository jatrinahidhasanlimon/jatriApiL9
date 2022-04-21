<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
//            Route::middleware('api')
//                ->prefix('api')
//                ->group(base_path('routes/api.php'));


//                Route::prefix('user-api')
//                    ->middleware('api')
//                    ->group(base_path('routes/user_api.php'));
//
//                Route::prefix('owner-api')
//                    ->middleware('api')
//                    ->group(base_path('routes/owner_api.php'));
//
//                Route::prefix('counter-api')
//                    ->middleware('api')
//                    ->group(base_path('routes/counter_api.php'));
//
//                Route::prefix('counter-api')
//                    ->middleware('api')
//                    ->group(base_path('routes/counter_api.php'));
//
//                Route::prefix('supervisor-counter-api')
//                    ->middleware('api')
//                    ->group(base_path('routes/supervisor_counter_api.php'));

                Route::prefix('airport-toll-api')
                    ->middleware('api')
                    ->group(base_path('routes/airport_toll_api.php'));

                Route::prefix('tollplaza-api')
                    ->middleware('api')
                    ->group(base_path('routes/tollplaza_api.php'));
//
//                Route::prefix('gp-api')
//                    ->middleware('api')
//                    ->group(base_path('routes/gp_service_api.php'));
//
//                Route::prefix('waybill')
//                    ->middleware('api')
//                    ->group(base_path('routes/waybill_api.php'));
//
//                Route::prefix('digital-payment')
//                    ->middleware('api')
//                    ->group(base_path('routes/payment.php'));
//
//                Route::prefix('b2b-driver-api')
//                    ->middleware('api')
//                    ->group(base_path('routes/b2b_driver_api.php'));
//
//                Route::prefix('rental-owner-api')
//                    ->middleware('api')
//                    ->group(base_path('routes/rental_owner_api.php'));
//
//                Route::prefix('user-api')
//                    ->middleware('api')
//                    ->group(base_path('routes/rental_user_api.php'));
//
//                Route::prefix('dt-master-api')
//                    ->middleware('api')
//                    ->group(base_path('routes/dt_master_api.php'));
//
//                Route::prefix('user-api')
//                    ->middleware('api')
//                    ->group(base_path('routes/dt_user_api.php'));
//
//                Route::prefix('offline-counter-api')
//                    ->middleware('api')
//                    ->group(base_path('routes/offline_counter_api.php'));



//            Route::middleware('web')
//                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
