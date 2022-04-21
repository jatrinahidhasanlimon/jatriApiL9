<?php

//================ V1 Start ====================
Route::group(['prefix' => 'v1'], function () {
    $prefix = 'Api\v1';
    Route::post('/login', $prefix.'\GPApiController@login');
    Route::post('/refresh-token', $prefix.'\GPApiController@refreshToken');
    Route::get('/logout', $prefix.'\GPApiController@logout');
    Route::get('/profile', $prefix.'\GPApiController@getProfile');
    Route::get('/offline-data-sync', $prefix.'\GPApiController@getUpdatedOfflineData');
    Route::post('/collect-gp', $prefix.'\GPApiController@collectGP');
});

//================ V1 END ======================
