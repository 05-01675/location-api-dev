<?php

namespace App\Http\Services;

use App\Cart;
use App\CartItem;
use App\Customer;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ShopifyService 
{
    private $base_url;
    private $app_access_token;
    private $store_name;

    public function __construct()
    {
        $this->base_url = "https://test-discoveryv2.myshopify.com";
        $this->app_access_token = config('shopify-app.access-token');
        $this->store_name = config('shopify-app.store-url');
    }

    public function changeCart($data) : ?object
    {
        try{
            $response = Http::post($this->base_url . '/cart/change',
                                [
                                   $data
                                ]
                               );
             return $response;

        } catch(Exception $e){

        }
    }

    public function clearCart($data)
    {
       
        try{
            $response = Cart::where('customer_id', $data['customer_id'])
                    ->where('status', 0)
                    ->with('items')->delete();
            
            return $response;

        } catch(Exception $e){

        }
    }

    public function getAllProducts() {
        $data = [
            "path" => "/admin/api/2021-04/products.json?status=active",
        ];

        $url = $this->base_url . $data['path'];
         
        try{
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            //curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json;charset=UTF-8',
                'X-Shopify-Access-Token:'. $this->app_access_token,
            ));
            $result = curl_exec($ch);

            return json_decode($result, true);

        } catch (Exception $e) {
            
        }
    }

    public function getAllCollection() {
        $data = [
           // "path" => "/admin/api/2022-04/collections/272945774730/products.json?status=active",
           "path" => "/admin/api/2022-04/smart_collections.json",
        ];

        $url = $this->base_url . $data['path'];
         
        try{
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            //curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json;charset=UTF-8',
                'X-Shopify-Access-Token:'. $this->app_access_token,
            ));
            $result = curl_exec($ch);

            return json_decode($result, true);

        } catch (Exception $e) {
            
        }
    }

    public function getproductsWithCollection($category_id) {
       
        $products = array();
        $values = array();
        $products_filtered = array();
       
            $data = [
                "path" => "/admin/api/2022-04/collections/".$category_id."/products.json?status=active",
               //"path" => "/admin/api/2022-04/smart_collections.json",
            ];
    
             $url = $this->base_url . $data['path'];
             
            try{
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
                //curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json;charset=UTF-8',
                    'X-Shopify-Access-Token:'. $this->app_access_token,
                ));

                $result = curl_exec($ch);

                return json_decode($result, true);
                
    
            } catch (Exception $e) {
                
            } 
    
        
    }

    public function getShippingRates() {
        $data = [
            "path" => "/admin/api/2022-04/shipping_zones.json",
        ];

        $url = $this->store_name . $data['path'];
         
        try{
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            //curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json;charset=UTF-8',
                'X-Shopify-Access-Token:'. $this->app_access_token,
            ));
            $result = curl_exec($ch);
           
            return json_decode($result, true);

        } catch (Exception $e) {
            
        }
    }

    public function createCheckout(array $args) {
        //return $args;
        $data = [
            "path" => "/admin/api/2021-01/checkouts.json",
        ];

        if(array_key_exists('shipping_address', $args['checkout'])){
            $args['checkout']['shipping_address']['phone'] = strval($args['checkout']['shipping_address']['phone']);
        }

        if(array_key_exists('billing_address', $args['checkout'])){
            $args['checkout']['billing_address']['phone'] = strval($args['checkout']['billing_address']['phone']);
        }


        $url = $this->store_name . $data['path'];
         
        try{
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($args));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json;charset=UTF-8',
                'X-Shopify-Access-Token:'. $this->app_access_token,
               
            ));
            $result = curl_exec($ch);
          //  var_export($result);
            return json_decode($result, true);

        } catch (Exception $e) {
            return "aa";
        }
    }

    public function getCheckout(array $args) {
        //return $args;
        $data = [
            "path" => "/admin/api/2021-01/checkouts.json",
        ];

        $url = $this->store_name . $data['path'];
         
        try{
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($args));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json;charset=UTF-8',
                'X-Shopify-Access-Token:'. $this->app_access_token,
               
            ));
            $result = curl_exec($ch);
          //  var_export($result);
            return json_decode($result, true);

        } catch (Exception $e) {
            return "aa";
        }
    }

    public function getOrders(array $args) 
    {
        //get all orders via email and financial status
        //return $args['email'];
        $data = [
            "path" => "/admin/api/2021-04/orders.json?email=".$args['email']."&status=any",
        ];

        $url = $this->store_name . $data['path'];
         
        try{
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            //curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json;charset=UTF-8',
                'X-Shopify-Access-Token:'. $this->app_access_token,
            ));
            $result = curl_exec($ch);

            return json_decode($result, true);

        } catch (Exception $e) {
            
        }
    }

    public function getOrderViaStatus(array $args) 
    {
        //get all orders via email and financial status

        $data = [
            "path" => "/admin/api/2022-04/orders.json?email=" .$args['email']."&status=" .$args['status'],
        ];

        //return $data;
        $url = $this->store_name . $data['path'];
         
        try{
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json;charset=UTF-8',
                'X-Shopify-Access-Token:'. $this->app_access_token,
            ));
            $result = curl_exec($ch);

            return json_decode($result, true);

        } catch (Exception $e) {
            
        }
    }

    public function getOrderById(array $args) 
    {
        //get all orders via orderId
        $data = [
            "path" => "/admin/api/2021-04/orders/".$args['order_id'].".json"
        ];

        $url = $this->store_name . $data['path'];
         
        try{
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json;charset=UTF-8',
                'X-Shopify-Access-Token:'. $this->app_access_token,
            ));
            $result = curl_exec($ch);

            return json_decode($result, true);

        } catch (Exception $e) {
            
        }
    }

    public function getVoucherByCode(string $code)
    {
        $data = [
            "path" => "/admin/api/2022-04/discount_codes/lookup.json?code=" . $code
        ];

        $url = $this->store_name . $data['path'];
        
        return $this->shopifyGet($url);
       
    }

    public function getPriceRule(int $rule_id)
    {
        $data = [
            "path" => "/admin/api/2022-04/price_rules/". $rule_id .".json"
        ];

        $url = $this->store_name . $data['path'];

        return $this->shopifyGet($url);

    }

    private function shopifyGet(string $url)
    {
        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $this->app_access_token,
        ])->get($url);

        $response = json_decode($response, true);

        if(array_key_exists("errors", $response)){
            throw new Exception(json_encode($response["errors"]));
        }

        return $response;
    }
    
}