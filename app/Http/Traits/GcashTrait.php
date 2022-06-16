<?php

namespace App\Http\Traits;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\GcashLog;

trait GcashTrait
{
    //gcash keys
    private $clientId;
    private $baseUrl;
    private $merchantId;
    private $productCode;
    private $appId;
    private $gcashLog;
    private $segment;
    private $keyPem;

    public function __construct()
    {
        $this->keyPem = asset('storage/Keys/chibbs_private_key.pem');
        $this->baseUrl = config('gcash.gcash_api_base_url');
        $this->clientId = config('gcash.client_id');
        $this->merchantId = config('gcash.merchant_id');
        $this->productCode = config('gcash.gcash_product_code');
        $this->appId = config('gcash.app_id');

        //shopify
        $this->access_token = config('shopify-app.access-token');
        $this->api_key = config('shopify-app.api_key');
        $this->api_secret = config('shopify-app.api_secret');
        $this->api_scopes = config('shopify-app.api_scopes');
        $this->store_url = "https://test-discoveryv2.myshopify.com/";
      
    }

    public function getAccessToken(array $args)
    {
       // return $this->store_url;
        
        $data = [
            "path" => "/v1/authorizations/applyToken.htm",
            "payload" => [
                "referenceClientId" => 2021050720441300026824,
                "grantType" => "AUTHORIZATION_CODE",
                "authCode" => $args['auth_code'],
                "extendInfo" => "{\"customerBelongsTo\":\"GCASH\"}"
           ]
        ];

        

        $response = $this->consume($data);
       
        $gcash_log = new GcashLog;
        $gcash_log->user_id = 0;
        $gcash_log->route = $args['route'];
        $gcash_log->path = $data['path'];
        $gcash_log->request = json_encode($data);
        $gcash_log->response = json_encode($response);
        $gcash_log->save();
        
        return $response;     
    }

    public function getUserAddressBook(array $args, $accessToken)
    {
        $data = [
            "path" => "/v1/customers/inquireAddressByAccessToken.htm",
            "payload" => [
                "accessToken" => $accessToken,
                "extendInfo" => "{\"customerBelongsTo\":\"GCASH\"}"
           ]
        ];

        $response = $this->consume($data);
       
        $gcash_log = new GcashLog;
        $gcash_log->user_id = 0;
        $gcash_log->route = $args['route'];
        $gcash_log->path = $data['path'];
        $gcash_log->request = json_encode($data);
        $gcash_log->response = json_encode($response);
        $gcash_log->save();
        
        return $response;     
    }

    public function getAddressData(array $address, $accessToken) 
    {
       
        //return $address;
        foreach($address as $key => $item){
            //return $item;
            foreach($item as $key2 => $value) {
                foreach($value as $key3 => $addressData) {
                    $returnAddress = $addressData['addressData'];
                    return $returnAddress['addressProviderData']; 
                }
                /* return $val['addressData'];
                $addressData = $value['addressData'];
                return $addressData['addressProviderData']; */
            }
        }

        
    }

    

    public function getUserInfo(array $args, $accessToken)
    {
        $data = [
            "path" => "/v1/customers/user/inquiryUserInfoByAccessToken.htm",
           // "path"=> "/v1/customer/inquiryUserInfoByAccessToken.htm",
            "payload" => [
                "accessToken" => $accessToken,
                "extendInfo" => "{\"customerBelongsTo\":\"GCASH\"}"
            ]
        ];

       // return $data;
        $response = $this->consume($data);
        $gcash_log = new GcashLog;
        $gcash_log->user_id = 0;
        $gcash_log->route = $args['route'];
        $gcash_log->path = $data['path'];
        $gcash_log->request = json_encode($data);
        $gcash_log->response = json_encode($response);
        $gcash_log->save();
        return $response;
    }


    public function signData($stringDataToSign) : string
    {
        $privateKey = $this->keyPem;      
        $pKeyId = openssl_pkey_get_private(file_get_contents($privateKey));
     //   $pKeyId = openssl_get_privatekey($privateKey);
        openssl_sign($stringDataToSign, $signature, $pKeyId, "sha256WithRSAEncryption");
        $base64 = base64_encode($signature);
        $raw = rawurlencode($base64);
        return $raw;
    }

    public function consume(array $data)
    {
        // assemble header signature
        date_default_timezone_set('Asia/Manila');
        $requestTime =  date("Y-m-d")."T".date("H:i:s")."+08:00";
        $payload = json_encode($data['payload']);
        $stringToSign = "POST ".$data['path']."\n".$this->clientId.".".$requestTime.".".$payload;
        $url = $this->baseUrl . $data['path'];
        $signature  = $this->signData($stringToSign);

        //var_export($stringToSign);
        try{
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json;charset=UTF-8',
                'Client-Id: ' . $this->clientId,
                'Request-Time: ' . $requestTime,
                'Signature: algorithm=RSA256, keyVersion=2, signature=' . $signature,
                'Content-Length: ' . strlen($payload))
            );
            $result = curl_exec($ch);
            return json_decode($result, true); 

        } catch (Exception $e) {    

        }
    }

    public function pay(array $args)
    {
        $paymentReqId = $this->clientId . Str::random(20);
        $amount = strval($args["amount"] * 100);
        $data = [
            "path" => "/v1/payments/pay.htm",
            "payload" => [
                "partnerId" => $this->merchantId,
                "appId" => $this->appId,
                "paymentRequestId" => $paymentReqId,
                "productCode" => $this->productCode,
                "paymentOrderTitle" => "WPIT" . $paymentReqId,
                "paymentAmount" => [
                    "currency" => "PHP",
                    "value" => $amount
                ],
                "paymentFactor" => [
                    "isCashierPayment" => true
                ],
                "paymentNotifyUrl" => "https://6848-158-62-0-53.ap.ngrok.io/api/gcash/notify",
                "extendInfo" => "{\"customerBelongsTo\":\"GCASH\"}" //optional
            ],
        ];


        $response = $this->consume($data);

        $gcash_log = new GcashLog;
            $gcash_log->user_id = 0;
            $gcash_log->route = $args['route'];
            $gcash_log->path = $data['path'];
            $gcash_log->request = json_encode($data);
            $gcash_log->response = json_encode($response);
            $gcash_log->save();

        return $response;
    }

    public function addCustomer(array $args)
    {
        /*
            this add a customers in the shopify
        */
        //return $args;
        $data = [
            "path" => "/admin/api/2022-01/customers.json",
        ];
        //return $data;
        $url = $this->store_url . $data['path'];
       // return $this->store_url;
         
        try{
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($args));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json;charset=UTF-8',
                'X-Shopify-Access-Token:'. $this->access_token,
               
            ));
            $result = curl_exec($ch);
            var_export($result);
            return json_decode($result, true);

        } catch (Exception $e) {
            return $e;
        }
        
        //return $response;        
    }



}