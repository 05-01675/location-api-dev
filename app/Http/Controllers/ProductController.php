<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\Http\Services\ShopifyService;
use Illuminate\Support\Facades\Http;

class ProductController extends Controller
{
    private $shopifyService;

    public function __construct(ShopifyService $shopifyService)
    {
        $this->shopifyService = $shopifyService;
    }

    public function searchByTitle(Request $request) {
       
        $search_term = $request->input('title');
       
        $storeUrl = $request->header('X-Shopify-Store-Url');
        $accessToken = $request->header('X-Shopify-Access-Token');
        
        $products = $this->shopifyService->getAllProducts();

        $result = array();
        foreach($products['products'] as $product) {
            if( stripos( $product['title'], $search_term ) !== false ) {
                array_push($result, $product);
            }
        }

        $collection = collect($result);

        $perPage = $request->input('perPage');
        $page = null;
        $options = [];

        $baseUrl = "https://6e6c-158-62-1-111.ap.ngrok.io/api/products/search";

        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);

       // $collection = $collection instanceof Collection ? $collection : Collection::make($collection);

        $pagination = new LengthAwarePaginator(array_values($collection->forPage($page, $perPage)->toArray()), $collection->count(), $perPage, $page, $options);

        if ($baseUrl) {
            $pagination->setPath($baseUrl);
        }

       // return $pagination;

        //return $products;
        
        return response()->json($pagination,200);
        
    }

    public function searchWithCategory(Request $request) {
       
        $search_term = $request->input('title');
        $category_id = $request->input('category_id');
       
        $storeUrl = $request->header('X-Shopify-Store-Url');
        $accessToken = $request->header('X-Shopify-Access-Token');

        $products = $this->shopifyService->getproductsWithCollection($category_id);

        //return $products['products'];
        $result = array();
        foreach($products['products'] as $filtered_product){
              // return $filtered_product['title'];
            if( stripos( $filtered_product['title'], $search_term ) !== false ) {
                array_push($result, $filtered_product);
            } 
        }
        
        $collection = collect($result);

        $perPage = $request->input('perPage');
        $page = null;
        $options = [];

        $baseUrl = "https://6e6c-158-62-1-111.ap.ngrok.io/api/products/searchWithCategory";

        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);

        $pagination = new LengthAwarePaginator(array_values($collection->forPage($page, $perPage)->toArray()), $collection->count(), $perPage, $page, $options);

        if ($baseUrl) {
            $pagination->setPath($baseUrl);
        }

        
        return response()->json($pagination,200);
        
    }

    public function getAll(Request $request) {

        $products = $this->shopifyService->getAllProducts();

        $collection = collect($products['products']);

        $perPage = $request->input('perPage');
        $page = null;
        $options = [];

        $baseUrl = "https://6e6c-158-62-1-111.ap.ngrok.io/api/products/getProducts";

        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);

       // $collection = $collection instanceof Collection ? $collection : Collection::make($collection);

        $pagination = new LengthAwarePaginator(array_values($collection->forPage($page, $perPage)->toArray()), $collection->count(), $perPage, $page, $options);

        if ($baseUrl) {
            $pagination->setPath($baseUrl);
        }

        return $pagination;
    }
}
