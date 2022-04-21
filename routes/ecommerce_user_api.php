<?php

//================ V1 Start ====================
Route::group(['prefix' => 'v1'], function () {
    $path_prefix = 'Api\v1';
    Route::get('/ecommerce/get-necessary-info', $path_prefix.'\EcommerceUserApiController@getNecessaryInfo');

    //bazar
    Route::get('/ecommerce/get-products/{page}', $path_prefix.'\EcommerceUserApiController@getProducts');
    Route::get('/ecommerce/bazar-category', $path_prefix.'\EcommerceUserApiController@getBazarCategory');
    Route::get('/ecommerce/get-bazar-products/{category_id}/{page}', $path_prefix.'\EcommerceUserApiController@getBazarProducts');
    Route::get('/ecommerce/get-search-products/{search_txt}/{page}', $path_prefix.'\EcommerceUserApiController@getSearchProducts');
    Route::post('/ecommerce/checkout', $path_prefix.'\EcommerceUserApiController@checkoutMyOrder');

    //express
    Route::get('/ecommerce/express-category', $path_prefix.'\EcommerceUserApiController@getExpressCategory');
    Route::get('/ecommerce/get-express-products/{category_id}/{page}', $path_prefix.'\EcommerceUserApiController@getExpressProducts');
    Route::get('/ecommerce/get-search-express-products/{search_txt}', $path_prefix.'\EcommerceUserApiController@getSearchExpressProducts');
    Route::post('/ecommerce/express-checkout', $path_prefix.'\EcommerceUserApiController@checkoutExpressMyOrder');

    //grocery
    Route::get('/ecommerce/grocery-category', $path_prefix.'\EcommerceUserApiController@getGroceryCategory');
    Route::get('/ecommerce/get-grocery-products/{category_id}/{page}', $path_prefix.'\EcommerceUserApiController@getGroceryProducts');
    Route::get('/ecommerce/get-search-grocery-products/{search_txt}', $path_prefix.'\EcommerceUserApiController@getSearchGroceryProducts');
    Route::post('/ecommerce/grocery-checkout', $path_prefix.'\EcommerceUserApiController@checkoutGroceryMyOrder');

    Route::get('/ecommerce/get-product-details/{product_id}', $path_prefix.'\EcommerceUserApiController@getProductDetails');
    Route::get('/ecommerce/get-my-orders/{page}', $path_prefix.'\EcommerceUserApiController@getMyOrders');
    Route::get('/ecommerce/get-my-order/{order_id}/details', $path_prefix.'\EcommerceUserApiController@getMyOrderByOrderID');

    Route::post('/ecommerce/discount-check', $path_prefix.'\EcommerceUserApiController@discountCheck');
});

Route::group(['prefix' => 'v2'], function () {
    $path_prefix = 'Api\v2';
    Route::get('/bazar/get-home-info', $path_prefix.'\EcommerceUserApiController@getHomeInfo');
    Route::get('/bazar/get-store-area/{service_type}', $path_prefix.'\EcommerceUserApiController@getStoreOrArea');
    Route::get('/bazar/get-products/{store_or_area_id}', $path_prefix.'\EcommerceUserApiController@getProducts');
    Route::get('/bazar/get-search-products/{store_or_area_id}/{search_txt}', $path_prefix.'\EcommerceUserApiController@getSearchProducts');
    Route::get('/bazar/get-product-details/{product_id}', $path_prefix.'\EcommerceUserApiController@getProductDetails');
    Route::post('/bazar/get-order-meta-data', $path_prefix.'\EcommerceUserApiController@getOrderMetaData');
    Route::post('/bazar/checkout', $path_prefix.'\EcommerceUserApiController@checkout');
    Route::get('/bazar/get-my-orders/{page}', $path_prefix.'\EcommerceUserApiController@getMyOrders');
    Route::get('/bazar/get-my-order/{order_id}/details', $path_prefix.'\EcommerceUserApiController@getMyOrderByOrderID');
});

