<?php

//================ V1 Start ====================
Route::group(['prefix' => 'v1'], function () {
    $prefix = 'Api\v1';

    //otp
    Route::post('/send-otp', $prefix.'\RentalOwnerApiController@sendOTP');

    //profile
    Route::post('/login', $prefix.'\RentalOwnerApiController@login');
    Route::get('/profile', $prefix.'\RentalOwnerApiController@getProfile');
    Route::get('/get-credit', $prefix.'\RentalOwnerApiController@getCredit');
    Route::post('/profile-update', $prefix.'\RentalOwnerApiController@profileUpdate');
    Route::post('/registration', $prefix.'\RentalOwnerApiController@signUp');
    Route::post('/refresh-token', $prefix.'\RentalOwnerApiController@refreshToken');
    Route::get('/credit-statements', $prefix.'\RentalOwnerApiController@getCreditStatements');
    Route::get('/get-service-types', $prefix.'\RentalOwnerApiController@getServiceTypes');

    //booking request
    Route::post('/get-available-request/{page}', $prefix.'\RentalOwnerApiController@getAvailableRequest');
    Route::post('/get-available-request-v2/{page}', $prefix.'\RentalOwnerApiController@getAvailableRequestV2');
    Route::post('/bid-to-booking/{booking_id}', $prefix.'\RentalOwnerApiController@bidToBooking');
    Route::post('/assign-driver/{booking_id}', $prefix.'\RentalOwnerApiController@assignDriver');
    Route::post('/cancel-bidding/{bidding_id}', $prefix.'\RentalOwnerApiController@cancelBidding');
    Route::post('/update-bidding/{bidding_id}', $prefix.'\RentalOwnerApiController@updateBidding');

    //vehicle
    Route::get('/get-vehicles', $prefix.'\RentalOwnerApiController@getVehicles');
    Route::post('/add-vehicle', $prefix.'\RentalOwnerApiController@addVehicle');
    Route::post('/update-vehicle/{vehicle_id}', $prefix.'\RentalOwnerApiController@updateVehicle');
    Route::get('/get-vehicle/{vehicle_id}', $prefix.'\RentalOwnerApiController@getVehicle');

    //driver
    Route::get('/get-drivers', $prefix.'\RentalOwnerApiController@getDrivers');
    Route::post('/add-driver', $prefix.'\RentalOwnerApiController@addDriver');
    Route::post('/update-driver/{driver_id}', $prefix.'\RentalOwnerApiController@updateDriver');

    //history
    Route::get('/get-await-trips', $prefix.'\RentalOwnerApiController@getAwaitTrips');
    Route::get('/get-trip-history/{page}', $prefix.'\RentalOwnerApiController@getTripHistory');

    //analytics
    Route::get('/get-dashboard-data', $prefix.'\RentalOwnerApiController@getDashboardData');

    //learning video
    Route::get('/get-learning-video', $prefix.'\RentalOwnerApiController@getLearningVideo');

    //offer
    Route::get('/get-current-offers', $prefix.'\RentalOwnerApiController@getCurrentOffers');

    //Ratings
    Route::post('/rate-booking/{booking_id}', $prefix.'\RentalOwnerApiController@rateBooking');
    Route::get('/get-ratings/{user_id}', $prefix.'\RentalOwnerApiController@getRatings');

    //Vaccination
    Route::get('/get-vaccination-data', $prefix.'\RentalOwnerApiController@getVaccinationData');
    Route::post('/add-vaccination', $prefix.'\RentalOwnerApiController@addVaccinationData');

    Route::get('/get-leaderboard', $prefix.'\RentalOwnerApiController@getLeaderboard');
});

//================ V2 Start ====================
Route::group(['prefix' => 'v2'], function () {
    $prefix = 'Api\v1';

    Route::post('/verify-otp', $prefix.'\RentalOwnerApiController@verifyOTP');
    Route::post('/login', $prefix.'\RentalOwnerApiController@loginV2');
    Route::post('/registration', $prefix.'\RentalOwnerApiController@signUpV2');
    Route::get('/profile', $prefix.'\RentalOwnerApiController@getProfileV2');
    Route::post('/update-password', $prefix.'\RentalOwnerApiController@updatePassword');
    Route::post('/reset-password', $prefix.'\RentalOwnerApiController@resetPassword');

});

//================ V3 Start ====================
Route::group(['prefix' => 'v3'], function () {
    $prefix = 'Api\v1';

    Route::get('/get-confirmed-trip-history', $prefix.'\RentalOwnerApiController@getConfirmedTripHistory');
    Route::get('/get-completed-trip-history/{page}', $prefix.'\RentalOwnerApiController@getCompletedTripHistory');
    Route::get('/get-trip-details/{booking_id}', $prefix.'\RentalOwnerApiController@getTripDetails');
});
