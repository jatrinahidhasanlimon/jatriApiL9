<?php

//================ V1 Start ====================
Route::group(['prefix' => 'v1/digital-ticketing'], function () {
    $path_prefix = 'Api\v1';
    Route::get('/get-counters', $path_prefix.'\DTUserApiController@getCountersForWeb');
    Route::get('/get-from-to-data-sets', $path_prefix.'\DTUserApiController@getAvailableFromToDataSets');
    Route::post('/get-available-services', $path_prefix.'\DTUserApiController@getAvailableServices');
    Route::post('/request-booking', $path_prefix.'\DTUserApiController@requestBooking');

    Route::get('/booking-stats', $path_prefix.'\DTUserApiController@getBookingStats');
    Route::get('/booking-history/{page}/{state}', $path_prefix.'\DTUserApiController@bookingHistory');
    Route::get('/booking-details/{booking_id}', $path_prefix.'\DTUserApiController@bookingDetails');
    Route::post('/cancel-booking', $path_prefix.'\DTUserApiController@cancelBooking');
});
//================ V1 END ======================
