<?php

//================ V1 Start ====================
Route::group(['prefix' => 'v1'], function () {
    $prefix = 'Api\v1';
    Route::post('/login', $prefix.'\B2bDriverApiController@login');
    Route::post('/refresh-token', $prefix.'\B2bDriverApiController@refreshToken');
    Route::get('/profile', $prefix.'\B2bDriverApiController@getProfile');

    Route::post('/update-location', $prefix.'\B2bDriverApiController@updateLocation');
});

//================ V1 END ======================
