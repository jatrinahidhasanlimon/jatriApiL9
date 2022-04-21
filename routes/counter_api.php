<?php

//================ V1 Start ====================
Route::group(['prefix' => 'v1'], function () {
    $prefix = 'Api\v1';
    Route::post('/login', $prefix.'\CounterApiController@login');
    Route::post('/refresh-token', $prefix.'\CounterApiController@refreshToken');
    Route::get('/logout', $prefix.'\CounterApiController@logout');
    Route::get('/profile', $prefix.'\CounterApiController@getProfile');
    Route::get('/offline-data-sync', $prefix.'\CounterApiController@getUpdatedOfflineData');
    Route::post('/sync-booking-data', $prefix.'\CounterApiController@syncBookingData');
    Route::post('/sync-booking-data-gcdc', $prefix.'\CounterApiController@syncBookingDataGCDCRoadA'); // GCDC Road A use only
    Route::post('/sync-booking-data-gcdc-road-b', $prefix.'\CounterApiController@syncBookingDataGCDCRoadB'); // GCDC Road B use only
    Route::post('/sync-booking-data-for-central-company', $prefix.'\CounterApiController@syncBookingDataForCentralCompany'); // for all central company to auto sync with one vehicle
    Route::post('/get-booking-by-serial', $prefix.'\CounterApiController@getBookingBySerial');
    Route::get('/cancel-booking/{booking_id}', $prefix.'\CounterApiController@cancelBooking');
    Route::get('/get-vehicles-location', $prefix.'\CounterApiController@getVehiclesLocation');
    Route::get('/get-today-synced', $prefix.'\CounterApiController@getTodaySynced');
    Route::get('/get-today-digital-ticket/{ride_id}/stats', $prefix.'\CounterApiController@getTodayDigitalTicketStats');
    Route::get('/get-today-digital-ticket/{ride_id}/details', $prefix.'\CounterApiController@getTodayDigitalTicketDetails');
    Route::get('/print-report', $prefix.'\CounterApiController@printReport');
    Route::get('/validate-sync-data', $prefix.'\CounterApiController@validateSyncData');
    Route::post('/start-new-ride', $prefix.'\CounterApiController@startNewRide');
    Route::get('/get-last-created-rides', $prefix.'\CounterApiController@getLastCreatedRide');

    Route::group(['prefix' => 'digital-booking'], function (){
        $tab_prefix = 'Api\v1';
        Route::post('/login', $tab_prefix.'\CounterTabApiController@login');
        Route::get('/summary', $tab_prefix.'\CounterTabApiController@getVehicleWiseBookingSummary');
        Route::get('/bookings/{vehicle_id}', $tab_prefix.'\CounterTabApiController@getBookingsByVehicle');
    });
});

//================ V1 END ======================
