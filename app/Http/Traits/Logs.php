<?php

namespace App\Http\Traits;

use App\CallbackLogs;
use Illuminate\Http\Request;

trait Logs
{
    /**
     * Log request
     * 
     * @param $paylpad
     * @param $message
     * 
     * @return void
     */
    public function log($payload, $message)
    {
        CallbackLogs::create([
            'request' => json_encode($payload),
            'response' => json_encode($message)
        ]);
    }
}