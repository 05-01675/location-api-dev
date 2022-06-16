<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Str;
use App\Http\Services\ShopifyService;

class OrderController extends Controller
{
    private $shopifyService;

    public function __construct(ShopifyService $shopifyService)
    {
        $this->shopifyService = $shopifyService;
    }

    public function getAllOrder(Request $request)
    {
        $data = [
             'email' => $request->input('email'),
            'number' => $request->input('phone')
            
        ];

        $orders = $this->shopifyService->getOrders($data);

        return $orders;

    }

    public function getOrderbyStatus(Request $request)
    {
        //statues 
        //open - unfullfilled & not paid/paid | closed - fullfilled & completed | canceled | any (all kind of order status)
        $data = [
            'email' => $request->input('email'),
            'status' => $request->input('status'),
            'fulfillment_status' => $request->input('fulfillment_status'),
            //'financial_status' => $request->input('financial_status')
        ];

        $orders = $this->shopifyService->getOrderViaStatus($data);

        $collection = array();
  
        foreach($orders['orders'] as $filtered_order){

          if( stripos( $filtered_order['fulfillment_status'], $data['fulfillment_status'] ) !== false ) {
              array_push($collection, $filtered_order);
          } 
      }

        return $collection;
    }

    public function getOrderbyId(Request $request)
    {
        $data = [
            'order_id' => $request->input('order_id')
       ];

       $orders = $this->shopifyService->getOrderById($data);

       return $orders;
    }


}
