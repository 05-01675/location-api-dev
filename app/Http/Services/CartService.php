<?php

namespace App\Http\Services;

use App\Cart;
use App\CartItem;
use App\Customer;
use Illuminate\Http\Request;

class CartService 
{
    /**
     * Get Cart and Cart Items
     * 
     * @param integer $customer_id
     * @return Collection
     */
    public function getAllCart(int $customer_id)
    {       
        $response = Customer::where('customer_id', $customer_id)
            ->with('cart.items')
            ->firstOrFail();
        
        return $response;  
    }

    /**
     * Create Cart and Cart Item 
     * 
     * @param Request $data
     * @return Collection
     */

    public function store(Request $data)
    {
        $customer_id = $data->customer_id;
        
        $customer = Customer::where('customer_id', $customer_id)
            ->with('cart.items')
            ->firstOrFail();

        
        $cart = Cart::firstOrCreate(
            [
                'customer_id' => $customer_id,
                'status' => 0
            ]
        );
        
        foreach($data["items"] as $item){
            if(array_key_exists('variant_id', $item) && $item["variant_id"]){
                CartItem::updateOrCreate(
                    [
                        'cart_id' => $cart->id,
                        'product_id' => $item["id"],
                        'variant_id' => $item["variant_id"]
                    ],
                    [
                        'quantity' => $item["quantity"]
                    ]
                );
            }else{
                CartItem::updateOrCreate(
                    [
                        'cart_id' => $cart->id,
                        'product_id' => $item["id"]
                    ],
                    [
                        'quantity' => $item["quantity"]
                    ]
                );
            }
           
        }

        return $customer;
               
    }

    /**
     * Update Cart Item 
     * 
     * @param Request $data
     * @return Collection
     */
    public function update(Request $data)
    {
        $item = $data;
            
        $cart = Cart::findOrFail($item->cart_id);
        
        $cartItem = $cart->items()
                        ->where('product_id', $item->product_id)
                        ->where('variant_id', $item->variant_id)
                        ->firstOrFail();
        if($cartItem){
            $cartItem->update(['quantity' => $item->quantity]);
        }
        
        return $cart->items;
    }

    /**
     * Delete Cart Item
     * 
     * @param Request $request
     * @return Collection
     * 
     */
    public function deleteItem(Request $request)
    {
        $item = $request;
        $cart = Cart::findOrFail($item->cart_id);

        $cartItem = $cart->items()
                    ->where('product_id', $item->product_id)
                    ->where('id', $item->id)
                    ->where('variant_id', $item->variant_id)
                    ->firstOrFail();

        if($cartItem){
            $cartItem->delete();
        }
      
        return $cart->items;
    }

}