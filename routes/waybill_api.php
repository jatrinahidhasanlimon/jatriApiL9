<?php

//================ V1 Start ====================
Route::group(['prefix' => 'v1'], function () {
    $prefix = 'Api\v1';
    Route::post('/login', $prefix.'\WaybillApiController@login');
    Route::post('/refresh-token', $prefix.'\WaybillApiController@refreshToken');
    Route::get('/logout', $prefix.'\WaybillApiController@logout');
    Route::get('/profile', $prefix.'\WaybillApiController@getProfile');
    Route::get('/offline-data-sync', $prefix.'\WaybillApiController@getUpdatedOfflineData');
    Route::post('/submit-waybill', $prefix.'\WaybillApiController@submitWaybill');
    Route::get('/get-today-synced', $prefix.'\WaybillApiController@getTodaySynced');
    Route::post('/print-report', $prefix.'\WaybillApiController@printReport');
});

//================ V1 END ======================
