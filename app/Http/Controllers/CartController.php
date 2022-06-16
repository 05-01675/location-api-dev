<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Services\CartService;
use App\Http\Services\ShopifyService;
use App\Http\Traits\Logs;
use Exception;

class CartController extends Controller
{
    use Logs;

    private $shopifyService;
    private $cartService;

    public function __construct(
        ShopifyService $shopifyService,
        CartService $cartService
    )
    {
        $this->shopifyService = $shopifyService;
        $this->cartService = $cartService;
    }

    public function index(Request $request)
    {
        
        try{
            $response = $this->cartService->getAllCart($request->customer_id);   

            return response()->json($response, 200);

        }catch(Exception $ex){
            $this->log($request->all(), $ex->getMessage());
        }

    }

    public function store(Request $request)
    {
        try{

            $response = $this->cartService->store($request);
            
            return response()->json($response, 201);
        }catch(Exception $ex){
            $this->log($request->all(), $ex->getMessage());
        }
        
    }

    public function update(Request $request)
    {
        try{

            $response = $this->cartService->update($request);

            return response()->json($response, 201);

        }catch(Exception $ex){
            $this->log($request->all(), $ex->getMessage());
        }
       
    }

    public function deleteItem(Request $request){
        try{
            $response = $this->cartService->deleteItem($request);

            return response()->json($response, 200);
        }catch(Exception $ex){
            $this->log($request->all(), $ex->getMessage());
        }
        
    }
}