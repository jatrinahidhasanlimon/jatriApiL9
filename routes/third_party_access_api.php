<?php

Route::group(['prefix' => 'xyz'], function () {
    Route::post('/get-data', 'Api\v1\IOTApiController@getVerifiedRideCount');
});
