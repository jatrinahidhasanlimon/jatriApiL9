<?php

//================ V1 Start ====================

Route::group(['prefix' => 'v1'], function () {
    Route::get('/{token}/{device_id}/verified-ride-count', 'Api\v1\IOTApiController@getVerifiedRideCount');
});

Route::group(['prefix' => 'my-radar'], function () {
    Route::post('/update-location', 'Api\v1\IOTApiController@updateLocationFromMyRadar');
});

//================ V1 END ======================
