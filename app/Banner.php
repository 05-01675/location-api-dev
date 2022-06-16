<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $table = 'banners';

    protected $fillable = [
        'image', 
        'shop_name',
        'status',
        'created_at',
        'updated_at'
       ];
}
