<?php

return [

    'merchant_id' => env('MERCHANT_ID', ''),
    'client_id' => env('CLIENT_ID', ''),
    'app_id' => env('APP_ID', ''),
    'gcash_private_pem_key' => env('GCASH_PRIVATE_KEY' ,''),
    'gcash_public_pem_key' => env('GCASH_PUBLIC_KEY' ,''),
    'gcash_api_base_url' => env('GCASH_BASE_URL', ''),
    'gcash_product_code' => env('GCASH_PRODUCT_CODE_ID', ''),
    'gcash_app_url' => env('APP_URL', ''),
];