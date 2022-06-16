<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CartItem extends Model
{
    use SoftDeletes;
    
    protected  $table = 'cart_items';

    protected $guarded = [];
    
    public function cart()
    {
        return $this->belongsTo(Cart::class, 'cart_id');
    }
}