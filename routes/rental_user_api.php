<?php

//================ V1 Start ====================
Route::group(['prefix' => 'v1/rental'], function () {
    $path_prefix = 'Api\v1';
    Route::get('/get-available-services', $path_prefix.'\RentalUserApiController@getAvailableServices');
    Route::post('/request-booking', $path_prefix.'\RentalUserApiController@requestBooking');
    Route::get('/booking-bidding/{booking_id}', $path_prefix.'\RentalUserApiController@bookingBidding');
    Route::get('/booking-confirm/{bid_id}', $path_prefix.'\RentalUserApiController@confirmBooking');
    Route::get('/booking-history/{page}', $path_prefix.'\RentalUserApiController@bookingHistory');
    Route::get('/booking-history-v2/{page}', $path_prefix.'\RentalUserApiController@bookingHistoryV2');
    Route::get('/booking-details/{booking_id}', $path_prefix.'\RentalUserApiController@bookingDetails');
    Route::post('/cancel-booking/{booking_id}', $path_prefix.'\RentalUserApiController@cancelBooking');
    Route::post('/complete-booking/{booking_id}', $path_prefix.'\RentalUserApiController@completeBooking');
    Route::post('/rate-booking/{booking_id}', $path_prefix.'\RentalUserApiController@rateBooking');
    Route::get('/get-vehicle/{vehicle_id}', $path_prefix.'\RentalUserApiController@getVehicle');
    Route::get('/get-learning-video', $path_prefix.'\RentalUserApiController@getLearningVideo');
    Route::get('/get-current-offers', $path_prefix.'\RentalUserApiController@getCurrentOffers');
    Route::get('/get-ratings/{merchant_id}', $path_prefix.'\RentalUserApiController@getRatings');
    Route::get('/get-vaccination-data/{merchant_id}', $path_prefix.'\RentalUserApiController@getVaccinationData');
    Route::get('/get-partner-profile/{merchant_id}', $path_prefix.'\RentalUserApiController@getPartnerProfile');
    Route::get('/get-promocodes/{service_id}', $path_prefix.'\RentalUserApiController@getPromocodes');
    Route::get('/get-active-promocode/{service_id}', $path_prefix.'\RentalUserApiController@getActivePromocode');
    Route::get('/active-promocode/{promo_id}', $path_prefix.'\RentalUserApiController@activePromocode');
    Route::get('/apply-promocode/{promocode}', $path_prefix.'\RentalUserApiController@applyPromocode');
    Route::post('/get-active-promocode', $path_prefix.'\RentalUserApiController@getActivePromocodeV2');
    Route::get('/booking-ongoing', $path_prefix.'\RentalUserApiController@bookingOngoing');
    Route::get('/get-favourite-routes', $path_prefix.'\RentalUserApiController@getFavouriteRoutes');
    Route::post('/request-for-business', $path_prefix.'\RentalUserApiController@requestForBusiness');
    Route::get('/get-book-for-events', $path_prefix.'\RentalUserApiController@getBookForEvents');
    Route::post('/request-for-event-booking', $path_prefix.'\RentalUserApiController@eventBookingRequest');
    Route::post('/partner-joining-request', $path_prefix.'\RentalUserApiController@partnerJoiningRequest');
});
//================ V1 END ======================
