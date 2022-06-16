<?php

namespace App\Http\Controllers;

use Exception;
use App\Http\Traits\Logs;
use Illuminate\Http\Request;
use App\Http\Services\ShopifyService;
use App\Http\Services\VoucherService;
use App\Http\Traits\FormatResponse;
use App\VoucherApplied;
use Illuminate\Validation\ValidationException;

class VoucherController extends Controller
{
    use Logs, FormatResponse;

    public function apply(Request $request)
    {
        try{

            $this->validate($request,[
                'code' => 'required',
                'customer_id' => 'required|integer'
            ]);
            
            $voucher = (new VoucherService())->getVoucherDetails($request);
        
            return $this->successResponse(
                "Successfully retrieved voucher details.",
                $voucher
            );

        }catch(Exception $ex){
            $this->log($request->all(), $ex->getMessage());
            return $this->errorResponse($ex->getMessage(), $ex->getCode());
        }
    }
}