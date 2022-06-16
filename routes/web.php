<?php

use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => 'verify.shopify'], function () {
    Route::get('/', function (){
        $shopName = Auth::user()->name;
        session(['shopName' => $shopName]);
        return view('welcome');
    });

    Route::get('banners', 'BannerController@index');
    Route::get('banners/create', 'BannerController@create');
    Route::get('banners/edit/{id}', 'BannerController@edit'); 
    
});

Route::post('banners/store', 'BannerController@store'); 
Route::patch('banners/update/{id}', 'BannerController@update'); 
Route::delete('banners/destroy/{id}', 'BannerController@destroy'); 

Route::get('app_auth', function(Request $request) {
	$shopName = Auth::user()->name;
    // $api_key = env('SHOPIFY_API_KEY');
    // $scopes = env('SHOPIFY_APP_SCOPES');
	// $redirect_uri = env('SHOPIFY_APP_REDIRECT_URI');
	
    // $url = "https://" . $shop . "/admin/oauth/authorize?client_id=" . $api_key . "&scope=" . $scopes . "&redirect_uri=" . urlencode($redirect_uri);
	// return redirect()->to($url);
})->middleware(['verify.shopify'])->name('home');  

Route::get('/promotional-banners', 'BannerController@getPromotionalBanners')->name('promotional-banners');
