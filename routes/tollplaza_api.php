<?php
use App\Http\Controllers\Api\v1\TollPlazaApiController;
//================ V1 Start ====================
Route::group(['prefix' => 'v1'], function () {
//    $prefix = 'Api\v1';
    Route::post('/login', [ TollPlazaApiController::class,'login']);
    Route::post('/refresh-token', [ TollPlazaApiController::class,'refreshToken']);
    Route::get('/logout', [ TollPlazaApiController::class,'logout']);
    Route::get('/profile', [ TollPlazaApiController::class,'getProfile']);
    Route::get('/offline-data-sync', [ TollPlazaApiController::class,'getUpdatedOfflineData']);
    Route::post('/sync-toll-collection-data', [ TollPlazaApiController::class,'syncTollCollectionData']);
    Route::get('/get-today-synced', [ TollPlazaApiController::class,'getTodaySynced']);
    Route::post('/exit-vehicle', [ TollPlazaApiController::class,'exitVehicle']);
    Route::post('/print-report', [ TollPlazaApiController::class,'printReport']);
    Route::get('/validate-sync-data', [ TollPlazaApiController::class,'validateSyncData']);
});

//================ V1 END ======================
