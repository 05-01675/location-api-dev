<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/* Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
}); */

//Gcash integration
Route::group(['prefix' => 'gcash'], function(){
    //userinfo
    Route::post('getUser', 'GcashController@getGcashUser');
    Route::post('registerUser', 'GcashController@registerGcashUser');
    Route::post('getAddressBook', 'GcashController@getAddressBook');

    //payment
    Route::post('payment', 'GcashController@payment');
    Route::post('refund', 'GcashController@refund');
    Route::post('notify', 'GcashController@NotifyPayment');
    Route::post('inquirePayment', 'GcashController@inquire');
});

    //addresses
Route::group(['prefix' => 'address'], function (){
    Route::get('provinces', 'AddressController@getProvinces')->name('provinces');
    Route::get('cities/{provinceKey}', 'AddressController@getCitiesByProvince')->name('cities');
    Route::post('update', 'AddressController@update');
    Route::delete('delete', 'AddressController@destroy');
});

Route::group(['prefix' => 'cart'], function (){
    Route::get('/', 'CartController@index')->name('index');
    Route::post('add', 'CartController@store')->name('store');
    Route::post('update', 'CartController@update')->name('update');
    Route::post('change', 'CartController@change')->name('change');
    Route::post('clear', 'CartController@clear')->name('clear');
    Route::post('delete-item', 'CartController@deleteItem')->name('deleteItem');
});

Route::group(['prefix' => 'products'], function (){
    Route::get('search', 'ProductController@searchByTitle');
    Route::get('searchWithCategory', 'ProductController@searchWithCategory');
    Route::get('getProducts', 'ProductController@getAll');
});

Route::group(['prefix' => 'shipping'], function (){
    Route::get('getShippingRates', 'ShippingRatesController@getShippingRates');
   // Route::get('getProducts', 'ProductController@getAll');
});

Route::group(['prefix' => 'checkout'], function (){
    Route::post('create', 'CheckoutController@create');
    Route::post('update', 'CheckoutController@update');
    Route::post('getCheckout', 'CheckoutController@getCheckout');
    Route::get('pullCache', 'CheckoutController@pullCache');
});

Route::group(['prefix' => 'orders'], function (){
    Route::get('getOrders', 'OrderController@getAllOrder');
    Route::get('getOrderbyStatus', 'OrderController@getOrderbyStatus');
    Route::get('getOrderbyId', 'OrderController@getOrderbyId');

});  

Route::group(['prefix' => 'voucher'], function(){
    Route::post('apply', 'VoucherController@apply');
});

