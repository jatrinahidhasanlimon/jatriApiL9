<?php

//================ V1 Start ====================
Route::group(['prefix' => 'v1'], function () {
    $prefix = 'Api\v1';

    Route::post('/login', $prefix.'\DTMasterApiController@login');
    Route::get('/profile', $prefix.'\DTMasterApiController@getProfile');
    Route::post('/change-password', $prefix.'\DTMasterApiController@changePassword');
    Route::post('/profile-update', $prefix.'\DTMasterApiController@profileUpdate');
    Route::post('/refresh-token', $prefix.'\DTMasterApiController@refreshToken');
    Route::post('/change-activity-status', $prefix.'\DTMasterApiController@changeActivityStatus');

    Route::get('/get-available-requests', $prefix.'\DTMasterApiController@getAvailableRequests');
    Route::post('/accept-booking', $prefix.'\DTMasterApiController@acceptBooking');
    Route::get('/skip-accepting-booking/{broadcast_id}', $prefix.'\DTMasterApiController@skipAcceptingBooking');
    Route::get('/booking-history/{page}/{state}', $prefix.'\DTMasterApiController@getBookingHistory');
    Route::get('/verify-ticket/{otv}', $prefix.'\DTMasterApiController@verifyTicket');
    Route::get('/booking-details/{id}', $prefix.'\DTMasterApiController@bookingDetails');
});
