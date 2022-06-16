<?php

namespace App\Http\Services;

use Exception;
use App\VoucherApplied;

class VoucherService extends ShopifyService
{
    /**
     * Get voucher details
     * 
     * @param Request $request
     * 
     * @return object
     */
    public function getVoucherDetails($request)
    {
        $voucher =  $this->getVoucherByCode($request->code)["discount_code"];

        $price_rule = $this->getPriceRule($voucher["price_rule_id"])["price_rule"];

        // NOTE: For future if needed to get some data only
        // $price_rule = collect($price_rule)->only([
        //     "value_type", 
        //     "value", 
        //     "once_per_customer", 
        //     "usage_limit", 
        //     "id",
        //     "starts_at",
        //     "ends_at",
        //     "prerequisite_subtotal_range",
        //     "prerequisite_shipping_price_range"
        // ]);

        if ($price_rule["once_per_customer"]) {
            $applied =  VoucherApplied::where('customer_id', $request->customer_id)
                ->where('voucher_code', $request->code)
                ->where('pricerule_id', $price_rule["id"])
                ->first();

            if ($applied) {
                throw new Exception("You've already used this voucher.");
            }
        };

        return $price_rule;

    }

    /**
     * Store voucher details
     * 
     * @param integer $customer_id
     * @param string $voucher_code
     * @param integer $price_rule
     * 
     * @return void
     */
    public function storeCustomerVoucher(
        $customer_id, 
        $voucher_code, 
        $pricerule_id
    )
    {
        return VoucherApplied::create([
            'customer_id' => $customer_id,
            'voucher_code' => $voucher_code,
            'pricerule_id' => $pricerule_id
        ]);
    }
}
