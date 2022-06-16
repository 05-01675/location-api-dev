<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AddressController extends Controller
{
    /**
     * Retrieve province list
     * @return json 
     */

    public function getProvinces() {
        $path = asset('storage/Addresses/province.json');

        $provinces = json_decode(file_get_contents($path), true); 

        return $provinces;
    }

    /**
     * Retrieve cities list based on province key
     * @param string
     * @return json
     */

    public function getCitiesByProvince($provinceKey = '') {

        $path = asset('storage/Addresses/cities.json');
        $cities = json_decode(file_get_contents($path), true); 
        $filteredCities = array();
        foreach($cities as $city) {
            if($city['province'] == $provinceKey) {
                array_push($filteredCities,$city);
            }
        }
        return $filteredCities;
    }

    /**
     * Update customer address
     */

    public function update(Request $request) {
        $storeUrl = config('shopify-app.store-url');
        $accessToken = config('shopify-app.access-token');
        $customerId = $request->customer_id;
        $addressId = $request->address_id;        
        $updateAddress = Http::withHeaders(
            ["X-Shopify-Access-Token" => $accessToken]
        )
        ->put($storeUrl . "/admin/api/2021-01/customers/$customerId/addresses/$addressId.json", [
            "address" => (array)$request->address
        ]);
        return $updateAddress;
    }

    public function destroy(Request $request) {
        $storeUrl = config('shopify-app.store-url');
        $accessToken = config('shopify-app.access-token');
        $customerId = $request->customer_id;
        $addressId = $request->address_id;
        $deleteAddress = Http::withHeaders(
            ["X-Shopify-Access-Token" => $accessToken]
        )
        ->delete($storeUrl . "/admin/api/2021-04/customers/$customerId/addresses/$addressId.json", [
            "address" => (array)$request->address
        ]);
        return $deleteAddress;
    }
}
