<?php

//================ V1 Start ====================
Route::group(['prefix' => 'v1'], function () {
    $prefix = 'Api\v1';
    Route::post('/login', $prefix.'\FuelServiceApiController@login');
    Route::post('/refresh-token', $prefix.'\FuelServiceApiController@refreshToken');
    Route::get('/logout', $prefix.'\FuelServiceApiController@logout');
    Route::get('/profile', $prefix.'\FuelServiceApiController@getProfile');
    Route::get('/offline-data-sync', $prefix.'\FuelServiceApiController@getUpdatedOfflineData');
    Route::post('/sync-sales-data', $prefix.'\FuelServiceApiController@syncSalesData');
    Route::get('/get-today-synced', $prefix.'\FuelServiceApiController@getTodaySynced');
    Route::get('/validate-sync-data', $prefix.'\FuelServiceApiController@validateSyncData');
    Route::post('/provide-duplicate-invoice', $prefix.'\FuelServiceApiController@provideDuplicateInvoice');
});

//================ V1 END ======================
