<?php
use App\Http\Controllers\Api\v1\AirportTollApiController;
//================ V1 Start ====================
Route::group(['prefix' => 'v1'], function () {
//    $prefix = 'Api\v1';
    Route::post('/login', [AirportTollApiController::class,'login']);
    Route::post('/refresh-token', [AirportTollApiController::class,'refreshToken']);
    Route::get('/logout', [AirportTollApiController::class,'logout']);
    Route::get('/profile', [AirportTollApiController::class,'getProfile']);
    Route::post('/sync-toll-data', [AirportTollApiController::class,'syncTollCollectionData']);
    Route::get('/get-today-synced', [AirportTollApiController::class,'getTodaySynced']);
    Route::get('/print-report', [AirportTollApiController::class,'printReport']);
});

//================ V1 END ======================
