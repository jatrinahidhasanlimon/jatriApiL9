<?php

return [
    'projectPath' => env('PROJECT_PATH'),
    'apiDomain' => env("SSLCOMMERZ_DOMAIN_URL", "https://sandbox.sslcommerz.com"), // For Live, use "https://securepay.sslcommerz.com", For Sandbox, use "https://sandbox.sslcommerz.com"
    'apiCredentials' => [
        'store_id' => env("SSLCOMMERZ_STORE_ID"),
        'store_password' => env("SSLCOMMERZ_STORE_PASSWORD"),
    ],
    'apiUrl' => [
        'make_payment' => "/gwprocess/v4/api.php",
        'transaction_status' => "/validator/api/merchantTransIDvalidationAPI.php",
        'transaction_query' => "/validator/api/merchantTransIDvalidationAPI.php",
        'order_validate' => "/validator/api/validationserverAPI.php",
        'refund_payment' => "/validator/api/merchantTransIDvalidationAPI.php",
        'refund_status' => "/validator/api/merchantTransIDvalidationAPI.php",
    ],
    'connect_from_localhost' => env("IS_LOCALHOST_SSLCOMMERZ", true), // For Sandbox, use "true", For Live, use "false"
    'is_live_sslcommerz' => env("IS_LIVE_SSLCOMMERZ", false),
    'sslcommerz_host_url' => env("SSLCOMMERZ_HOST_URL"),
    'sslcommerz_sandbox_host_url' => env("SSLCOMMERZ_SANDBOX_HOST_URL"),

    'success_url' => '/digital-payment/sslcommerz/payment-success',
    'failed_url' => '/digital-payment/sslcommerz/payment/fail',
    'cancel_url' => '/digital-payment/cancel',
    'ipn_url' => '/digital-payment/ipn',
];
