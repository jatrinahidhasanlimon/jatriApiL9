<?php

//================ V1 Start ====================
Route::group(['prefix' => 'v1'], function () {
    $prefix = 'Api\v1';
    Route::post('/login', $prefix.'\OfflineCounterApiController@login');
    Route::get('/profile', $prefix.'\OfflineCounterApiController@getProfile');
    Route::post('/change-password', $prefix.'\OfflineCounterApiController@changePassword');
    Route::post('/sync-ticket-data', $prefix.'\OfflineCounterApiController@syncTicketsData');
});

//================ V1 END ======================
