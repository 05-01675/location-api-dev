<?php

namespace App\Http\Controllers;

use App\ShippingRates;
use Illuminate\Http\Request;
use App\Http\Services\ShopifyService;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;

class ShippingRatesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private $shopifyService;

    public function __construct(ShopifyService $shopifyService)
    {
        $this->shopifyService = $shopifyService;
    }

    public function getShippingRates(Request $request)
    {
        $value = $request->province;
        $response = $this->shopifyService->getShippingRates();  
        //return $response;
        if(array_key_exists('shipping_zones', $response)) {   
            $shipping_zone = $response['shipping_zones']; 
            $rates = array();

            foreach($shipping_zone as $key => $shipping_rates) {                
                foreach($shipping_rates['countries'] as $key2 => $country) {
                    foreach($country['provinces'] as $key3 => $province) {
                        array_push($rates, collect($province)); 
                    }
                }
            }     

            $filtered = [];
            $filtered = array_filter($rates, function($data) use ($value) {
                return str_replace(' ', '', strtolower($data['name'])) == str_replace(' ', '', strtolower($value));
            }); 

            if(!empty($filtered)) {  
                foreach($filtered as $key4 => $shipping_area) {
                    $shipping_zones = collect($shipping_area);
                }            
    
                    $shipping_rate_filtered = array_filter($shipping_zone, function($val) use ($shipping_zones) {
                    return $val['id'] == $shipping_zones['shipping_zone_id'];
                });     
    
                return response()->json($shipping_rate_filtered, 200);

            } else {

                $msg = "No Data Available right Now";
                return response()->json($msg, 204);

            }

        } else {
            return response()->json("No Data Available right Now", 204);
        }
       

    }
    
}
