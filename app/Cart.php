<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cart extends Model
{
    use SoftDeletes;
    protected $table = "cart";

    protected $fillable = ['customer_id', 'status'];
    
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id','customer_id');
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }
}