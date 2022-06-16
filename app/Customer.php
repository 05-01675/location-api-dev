<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';

    public function cart()
    {
        return $this->hasMany(Cart::class, 'customer_id', 'customer_id')->where('status','=', 0);
    }

    public function voucherApplied()
    {
        return $this->hasMany(VoucherApplied::class, 'customer_id', 'customer_id');
    }
}
