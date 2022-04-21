<?php

//================ V1 Start ====================
Route::group(['prefix' => 'v1'], function () {
    $path_prefix = 'Api\v1';
    //profile
    //Route::post('/login', $path_prefix.'\UserApiController@login');
    //Route::post('/registration', $path_prefix.'\UserApiController@signUp');
    //Route::get('/profile', $path_prefix.'\UserApiController@getProfile');
    //Route::post('profile-update', $path_prefix.'\UserApiController@profileUpdate');

    //tracking
    Route::get('/tracking/get-stoppages', $path_prefix.'\UserApiController@getStoppageList');
    Route::post('/tracking/get-available-service/{tracking_id}', $path_prefix.'\UserApiController@getAvailableService');
    Route::get('/tracking/{vehicle_id}/track/{tracking_id}', $path_prefix.'\UserApiController@trackVehicleNow');
    Route::get('/tracking/{vehicle_id}/notification/{tracking_id}/{status}', $path_prefix.'\UserApiController@setTrackingNotification');
    Route::get('/tracking/active-notification', $path_prefix.'\UserApiController@getActiveNotificationVehicles');
    Route::get('/tracking/subscription-packages', $path_prefix.'\UserApiController@subscriptionPackages');
    Route::get('/tracking/subscribe/{package_id}', $path_prefix.'\UserApiController@subscribe');
    Route::get('/tracking/my-subscription', $path_prefix.'\UserApiController@getMySubscription');

    //ticketing
    Route::get('/ticketing/company', $path_prefix.'\UserApiController@getCompanyList');
    Route::get('/ticketing/stoppage/{company_id}', $path_prefix.'\UserApiController@getStoppageListByCompany');
    Route::post('/ticketing/fare', $path_prefix.'\UserApiController@getTicketFare');
    Route::post('/ticketing/instant-booking', $path_prefix.'\UserApiController@instantBookingTicket');
    Route::get('/ticketing/history/{page}', $path_prefix.'\UserApiController@bookHistory');
});


//================ V1 END ======================

//================ V2 Start ====================
Route::group(['prefix' => 'v2'], function () {
    $path_prefix = 'Api\v2';
    //profile
    //Route::post('/login', $path_prefix.'\UserApiController@login');
    //Route::post('/registration', $path_prefix.'\UserApiController@signUp');
    //Route::get('/profile', $path_prefix.'\UserApiController@getProfile');
    //Route::post('/profile-update', $path_prefix.'\UserApiController@profileUpdate');
    //Route::post('/cancel-bkash-agreement', $path_prefix.'\UserApiController@cancelBkashAgreement');
    //Route::get('/recharge-history/{page}', $path_prefix.'\UserApiController@getRechargeHistory');

    //tracking
    Route::post('/tracking/nearest-stoppage', $path_prefix.'\UserApiController@getNearestStoppage');
    Route::get('/tracking/get-stoppages', $path_prefix.'\UserApiController@getStoppageList');
    Route::post('/tracking/get-available-service/{tracking_id}', $path_prefix.'\UserApiController@getAvailableService');
    Route::get('/tracking/{vehicle_id}/track/{tracking_id}', $path_prefix.'\UserApiController@trackVehicleNow');
    Route::get('/tracking/{vehicle_id}/notification/{tracking_id}/{status}', $path_prefix.'\UserApiController@setTrackingNotification');
    Route::get('/tracking/active-notification', $path_prefix.'\UserApiController@getActiveNotificationVehicles');
    Route::get('/tracking/subscription-packages', $path_prefix.'\UserApiController@subscriptionPackages');
    Route::get('/tracking/subscribe/{package_id}', $path_prefix.'\UserApiController@subscribe');
    Route::get('/tracking/my-subscription', $path_prefix.'\UserApiController@getMySubscription');

    //ticketing
    Route::get('/ticketing/company', $path_prefix.'\UserApiController@getCompanyList');
    Route::get('/ticketing/stoppage/{company_id}', $path_prefix.'\UserApiController@getStoppageListByCompany');
    Route::post('/ticketing/fare', $path_prefix.'\UserApiController@getTicketFare');
    Route::post('/ticketing/book-instant-ticket', $path_prefix.'\UserApiController@bookInstantTicket');
    Route::get('/ticketing/history/{page}', $path_prefix.'\UserApiController@bookHistory');
});
//================ V2 END ======================

//================ V3 Start ====================
Route::group(['prefix' => 'v3'], function () {
    $path_prefix = 'Api\v3';
    Route::post('/send-otp', $path_prefix.'\UserApiController@sendOTP');
    //profile
    Route::post('/login', $path_prefix.'\UserApiController@login');
    Route::get('/profile', $path_prefix.'\UserApiController@getProfile');
    Route::post('/profile-update', $path_prefix.'\UserApiController@profileUpdate');
    Route::post('/registration', $path_prefix.'\UserApiController@signUp');
    Route::post('/refresh-token', $path_prefix.'\UserApiController@refreshToken');
    //buypass
    Route::get('/buypass/get-available', $path_prefix.'\UserApiController@getAvailableBuyPass');
    Route::post('/buypass/calculate-amount', $path_prefix.'\UserApiController@calculateAmount');
    Route::post('/buypass/buy', $path_prefix.'\UserApiController@buyPassUsingWallet');

    //tracking
    Route::get('/tracking/subscription-packages', $path_prefix.'\UserApiController@subscriptionPackages');

    //ticketing
    Route::post('/ticketing/fare', $path_prefix.'\UserApiController@getTicketFare');
    Route::post('/ticketing/book-instant-ticket', $path_prefix.'\UserApiController@bookInstantTicket');
    Route::post('/ticketing/nearest-stoppage', $path_prefix.'\UserApiController@getNearestStoppageByCompany');

    //complain
    Route::get('/complain/history/{page}', $path_prefix.'\UserApiController@getComplain');
    Route::get('/complain/vehicles/{company_id}', $path_prefix.'\UserApiController@getVehiclesByCompany');
    Route::post('/complain/submit', $path_prefix.'\UserApiController@submitComplain');

    //b2b
    Route::get('/b2b/get-assigned-vehicles', $path_prefix.'\UserApiController@getB2bAssignedVehicles');
    Route::get('/b2b/track-vehicle/{vehicle_id}', $path_prefix.'\UserApiController@trackB2bVehicle');

    //games
    Route::get('/game/get-available-game', $path_prefix.'\UserApiController@getAvailableGames');
    Route::post('/game/submit-winner-point', $path_prefix.'\UserApiController@submitGameWinnerPoint');

    Route::post('/sos', $path_prefix.'\UserApiController@provideSOS');
});
//================ V3 END ======================
