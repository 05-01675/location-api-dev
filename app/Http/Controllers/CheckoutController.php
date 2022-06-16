<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\ShopifyService;
use App\Checkout;

class CheckoutController extends Controller
{
    private $shopifyService;

    public function __construct(ShopifyService $shopifyService)
    {
        $this->shopifyService = $shopifyService;
    }

    public function create(Request $request) 
    {
       // return $request->all();
        $response = $request->all();
        
        
        $response = $this->shopifyService->createCheckout($request->all());
        
        //$data = $request->all();
        $checkout = $response['checkout'];

        if(!array_key_exists('customer_id', $checkout)) {
                return response()->json([
                    'errors' => "Missing request data",
                ], 422);
        } 

        $customer_id = $checkout['customer_id'];
        $email = $checkout['email'];
        $req = $request->all();
        $checkout = new Checkout;
        $checkout->status = 'pending';
        $checkout->order_id = 1;
        $checkout->customer_id = $customer_id;
        $checkout->email = $email;
        $checkout->request = json_encode($req);   
        $checkout->response = json_encode($response);      
        $checkout->shipping_lines = json_encode($checkout['shipping_rates']);    
        $checkout->viewed = 0;     
        $checkout->save();
    
        return $response;
        
    }

   /*  public function update(Request $request) {
        $storeUrl = $request->header('X-Shopify-Store-Url');
        $accessToken = $request->header('X-Shopify-Access-Token');
        $checkoutToken = $request->checkout_token;
        $updateCheckout = Http::withHeaders(
            ["X-Shopify-Access-Token" => $accessToken]
        )
        ->put("https://" . $storeUrl . "/admin/api/2021-01/checkouts/$checkoutToken.json", [
            "checkout" => (array)$request->checkout
        ]);

        return $updateCheckout;
    } */

    public function getCheckout(Request $request) 
    {
        $data = $request->all();
        $req = json_encode($data['request']);
        $req_data = json_decode($req, true); 

        $val = $req_data['request'];
        $billing_address = $val['billing_address'];
        
        if(!array_key_exists('customer_id', $billing_address)|| !array_key_exists('email', $val)) {
            return response()->json([
                'errors' => "Missing request data",
            ], 422);
        } else {

            $checkout = new Checkout;
            $checkout->status = 'pending';
            $checkout->order_id = 1;
            $checkout->customer_id = $billing_address['customer_id'];
            $checkout->email = $val['email'];
            $checkout->request = json_encode($data['request']);   
            $checkout->response = json_encode($data['response']);      
            $checkout->shipping_lines = json_encode($data['shipping_rates']);    
            $checkout->viewed = 0;     
            $checkout->save();

            return $checkout;
        }

        
          
    }

    public function pullCache(Request $request) 
    {
        $checkout = Checkout::get();        

        return json_decode($checkout);   
    }
}
