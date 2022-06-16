<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CallbackLogs extends Model
{
    protected $table = 'callback_logs';

    protected $fillable = ['request', 'response'];
}
