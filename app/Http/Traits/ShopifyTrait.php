<?php

namespace App\Http\Traits;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

trait ShopifyTrait
{
    private $access_token;
    private $api_key;
    private $api_secret;
    private $api_scopes;
    private $store_url;

    public function __construct()
    {
        $this->access_token = config('shopify-app.access-token');
        $this->api_key = config('shopify-app.api_key');
        $this->api_secret = config('shopify-app.api_secret');
        $this->api_scopes = config('shopify-app.api_scopes');
        $this->store_url = config('shopify-app.store-url');
    }

    
}