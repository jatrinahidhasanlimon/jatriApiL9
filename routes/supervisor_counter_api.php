<?php

//================ V1 Start ====================
Route::group(['prefix' => 'v1'], function () {
    $prefix = 'Api\v1';
    Route::post('/login', $prefix.'\SupervisorCounterApiController@login');
    Route::post('/refresh-token', $prefix.'\SupervisorCounterApiController@refreshToken');
    Route::get('/logout', $prefix.'\SupervisorCounterApiController@logout');
    Route::get('/profile', $prefix.'\SupervisorCounterApiController@getProfile');
    Route::get('/offline-data-sync', $prefix.'\SupervisorCounterApiController@getUpdatedOfflineData');
    Route::post('/sync-booking-data', $prefix.'\SupervisorCounterApiController@syncBookingData');
    Route::get('/get-today-synced', $prefix.'\SupervisorCounterApiController@getTodaySynced');
    Route::get('/get-today-synced-upto-3am', $prefix.'\SupervisorCounterApiController@getTodaySyncedUpto3am');
    Route::post('/get-booking-by-serial', $prefix.'\SupervisorCounterApiController@getBookingBySerial');
    Route::get('/cancel-booking/{booking_id}', $prefix.'\SupervisorCounterApiController@cancelBooking');
    Route::get('/validate-sync-data', $prefix.'\SupervisorCounterApiController@validateSyncData');
    Route::get('/print-report', $prefix.'\SupervisorCounterApiController@printReport');
    Route::get('/print-report-upto-3am', $prefix.'\SupervisorCounterApiController@printReportUpto3am');

    //highly secured route to manual JobSchedule if failed
    Route::post('/sync-data-manually', $prefix.'\SupervisorCounterApiController@syncDataManually');
});

//================ V1 END ======================
