<?php

//================ V1 Start ====================
Route::group(['prefix' => 'v1'], function () {
    $path_prefix = 'Api\v1';
    //profile
    Route::post('/login', $path_prefix.'\OwnerApiController@login');
    Route::get('/profile', $path_prefix.'\OwnerApiController@getProfile');
    Route::post('/change-password', $path_prefix.'\OwnerApiController@changePassword');
    Route::post('/update-profile', $path_prefix.'\OwnerApiController@updateProfile');

    //stats
    Route::post('/stats', $path_prefix.'\OwnerApiController@getStats');
    Route::get('/vehicle-list/{company_id}', $path_prefix.'\OwnerApiController@getVehicleList');
    Route::get('/company-list/', $path_prefix.'\OwnerApiController@getCompanyList');
    Route::post('/rides', $path_prefix.'\OwnerApiController@getRides');
    Route::get('/booking/{ride_id}', $path_prefix.'\OwnerApiController@getBookingByRideID');
    Route::get('/vehicle-location/{vehicle_id}', $path_prefix.'\OwnerApiController@getVehicleLocationInfo');
    Route::get('/vehicle-location/{vehicle_id}/previous/{date_time}', $path_prefix.'\OwnerApiController@getVehiclePreviousLocationInfo');
    Route::post('/vehicle-passed-distance/', $path_prefix.'\OwnerApiController@getVehiclePassedDistanceStats');
    Route::post('/vehicle-stoppage-hit/', $path_prefix.'\OwnerApiController@getVehicleStoppageHitStats');
    Route::post('/bookings/', $path_prefix.'\OwnerApiController@getBookingsFromAccessibleVehicle'); //V1
    Route::post('/bookings/{page}', $path_prefix.'\OwnerApiController@getBookingsFromAccessibleVehicleV2'); //V2
    Route::get('/vehicle-stoppages-for-company/{company_id}', $path_prefix.'\OwnerApiController@getVehicleWithStoppagesByCompanyId');

    //complain
    Route::get('/complain/history/{page}', $path_prefix.'\OwnerApiController@getComplain');

    Route::get('/accessible-vehicles-location', $path_prefix.'\OwnerApiController@allAccessibleVehiclesLocation');
    Route::post('/ticketing-summary', $path_prefix.'\OwnerApiController@getTicketingSummary');

    //counter wise reports
    Route::post('/counter-wise-reports', $path_prefix.'\OwnerApiController@getCounterWiseReport');
    Route::post('/counter-wise-reports-details', $path_prefix.'\OwnerApiController@getCounterWiseReportDetails');
    Route::post('/supervisor-counter-wise-reports', $path_prefix.'\OwnerApiController@getSupervisorCounterWiseReport');

    //waybill reports
    Route::get('/waybill-companies', $path_prefix.'\OwnerApiController@getWaybillCompanies');
    Route::get('/waybill-vehicle-wise-reports/{company_id}', $path_prefix.'\OwnerApiController@getWaybillVehicleWiseReports');
    Route::get('/waybill-submission-reports/{vehicle_id}', $path_prefix.'\OwnerApiController@getWaybillSubmissionReports');
});

//================ V1 END ======================
