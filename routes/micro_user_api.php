<?php

//================ V1 Start ====================
Route::group(['prefix' => 'v1'], function () {
    $path_prefix = 'Api\v1';
    Route::get('/micro/get-stoppage', $path_prefix.'\MicrobusUserApiController@getMicrobusStoppages');
    Route::post('/micro/get-schedules', $path_prefix.'\MicrobusUserApiController@getScheduleByDate');
    Route::post('/micro/check-availability', $path_prefix.'\MicrobusUserApiController@checkMicrobusAvailability');
    Route::post('/micro/get-ride-details', $path_prefix.'\MicrobusUserApiController@getRideDetails');
    Route::post('/micro/book-ticket', $path_prefix.'\MicrobusUserApiController@bookMicrobusTicket');
    Route::get('/micro/cancel-booking/{booking_id}', $path_prefix.'\MicrobusUserApiController@cancelBooking');
    Route::get('/micro/get-active-booking', $path_prefix.'\MicrobusUserApiController@getActiveBooking');
    Route::get('/micro/get-upcoming-rides', $path_prefix.'\MicrobusUserApiController@getScheduledRides');
    Route::get('/micro/get-ride-history/{page}', $path_prefix.'\MicrobusUserApiController@getRideHistory');
    Route::get('/micro/track/{booking_id}', $path_prefix.'\MicrobusUserApiController@trackBooking');
    Route::post('/micro/submit-complain', $path_prefix.'\MicrobusUserApiController@submitMicrobusComplain');
    Route::post('/micro/submit-feedback', $path_prefix.'\MicrobusUserApiController@submitMicrobusFeedback');
});

//================ V1 END ======================
