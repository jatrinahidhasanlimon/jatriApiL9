<?php

//================ V1 Start ====================

// bKash Integration
Route::get('/bkash/tokenization/payment', 'ThirdParty\bkash\BkashController@index');
Route::get('/bkash/tokenization/payment/success', 'ThirdParty\bkash\BkashController@success');
Route::get('/bkash/tokenization/payment/fail', 'ThirdParty\bkash\BkashController@fail');
Route::get('/bkash/tokenization/payment/{invoice}/execute-agreement', 'ThirdParty\bkash\BkashController@executeAgreement');
Route::get('/bkash/tokenization/payment/{invoice}/checkout-agreement-payment', 'ThirdParty\bkash\BkashController@checkoutAgreementPayment');
Route::get('/bkash/tokenization/payment/{invoice}/checkout-no-agreement-payment', 'ThirdParty\bkash\BkashController@checkoutNoAgreementPayment');

// PortWallet Integration
Route::get('/portwallet/payment', 'ThirdParty\portwallet\PortwalletController@index');
Route::get('/portwallet/payment-success', 'ThirdParty\portwallet\PortwalletController@paymentSuccess');
Route::get('/portwallet/payment/success', 'ThirdParty\portwallet\PortwalletController@success');
Route::get('/portwallet/payment/fail', 'ThirdParty\portwallet\PortwalletController@fail');

// SSLCommerz Integration
Route::get('/sslcommerz/payment', 'ThirdParty\sslcommerz\SSLCommerzController@index');
Route::post('/sslcommerz/payment-success', 'ThirdParty\sslcommerz\SSLCommerzController@paymentSuccess');
Route::match(['get', 'post'], '/sslcommerz/payment/fail', 'ThirdParty\sslcommerz\SSLCommerzController@fail');
Route::post('/cancel', 'ThirdParty\sslcommerz\SSLCommerzController@cancel');
Route::post('/ipn', 'ThirdParty\sslcommerz\SSLCommerzController@ipnResponse');

// Nagad Integration
Route::get('/nagad/payment', 'ThirdParty\nagad\NagadController@index');
Route::get('/nagad/payment-callback/{invoice}', 'ThirdParty\nagad\NagadController@paymentCallback');
Route::get('/nagad/payment/success', 'ThirdParty\nagad\NagadController@success');
Route::get('/nagad/payment/fail', 'ThirdParty\nagad\NagadController@fail');
//================ V1 END ======================
