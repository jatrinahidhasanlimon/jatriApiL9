<?php

//================ V1 Start ====================
Route::group(['prefix' => 'v1'], function () {
    $prefix = 'Api\v1';
    Route::post('/login', $prefix.'\MicrobusAdministratorApiController@login');
    Route::post('/refresh-token', $prefix.'\MicrobusAdministratorApiController@refreshToken');
    Route::get('/logout', $prefix.'\MicrobusAdministratorApiController@logout');
    Route::get('/profile', $prefix.'\MicrobusAdministratorApiController@getProfile');

    Route::get('/schedule-rides', $prefix.'\MicrobusAdministratorApiController@getScheduledRides');
    Route::get('/current-ride', $prefix.'\MicrobusAdministratorApiController@getCurrentRideDetails');
    Route::get('/pick-drop-data/{ride_id}/{stoppage_id}', $prefix.'\MicrobusAdministratorApiController@getPickDropData');
    Route::get('/start-ride/{ride_id}', $prefix.'\MicrobusAdministratorApiController@startRide');
    Route::get('/end-ride/{ride_id}', $prefix.'\MicrobusAdministratorApiController@endRide');
    Route::get('/confirm-ticket/{confirmation_code}', $prefix.'\MicrobusAdministratorApiController@confirmTicket');
    Route::get('/drop-user/{booking_id}', $prefix.'\MicrobusAdministratorApiController@dropUser');
    Route::post('/update-location', $prefix.'\MicrobusAdministratorApiController@updateLocation');
});

//================ V1 END ======================
