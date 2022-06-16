<?php

namespace App\Http\Traits;

trait FormatResponse 
{

    public function successResponse(
        $message = "Success", 
        $data = [], 
        $code = 200
    )
    {
        return response()->json([
            "message" => $message,
            "data" => $data,
            "code" => $code
        ]);
    }

    public function errorResponse(
        $message = "Error", 
        $code = 0
    )
    {
        return response()->json([
            "error" => true,
            "message" => $message,
            "code" => $code
        ]);
    }

}