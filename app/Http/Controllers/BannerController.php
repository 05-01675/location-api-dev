<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Banner;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Resource\BannerResource;
use App\Repository\BannerRepository;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;


class BannerController extends Controller
{
    
    private $bannerRepo;

    public function __construct(BannerRepository $bannerRepo)
    {
        $this->bannerRepo = $bannerRepo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {   
       
       $shop_name = Auth::user()->name;
        
      
        $data['banners'] = Banner::where('shop_name', $shop_name)->orderBy('id','desc')->paginate(10);
        
        return view('banner.List',$data);


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $shopName = Auth::user()->name;
        session(['shopName' => $shopName]);
        return view('banner.Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {   
        //$shop_name = Auth::user()->name;

        
        if($request->shopname == '') {
            return Redirect::to('banners')
                ->with('error','Missing shop name.');
        } 

        if($request->hasFile('image')) {

            $request->validate([
                'image' => 'mimes:jpeg,jpg,png|max:2048',
                'status' => 'required'
            ]);
   
            if ($files = $request->file('image')) {
                $filename = $files->getClientOriginalName();
                //$destinationPath = public_path('storage') . '/gmp/banners'; // upload path
                $destinationPath = storage_path('app/public') . '/Banners';
                $profileImage = $filename;
                $files->move($destinationPath, $profileImage);
                $insert['image'] = "$profileImage";
            }
            $insert['image_url'] = $request->get('image_url');
            $insert['redirect_type'] = $request->get('redirect_type');
            $insert['status'] = $request->get('status');
            $insert['shop_name'] = $request->shopname != '' ? $request->shopname : '';
            $insert['created_at'] = Carbon::now();
            Banner::insert($insert);
            return Redirect::to('banners')
            ->with('success','Great! Banner successfully uploaded.');
        }
        
        abort(500, 'Could not upload image.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit($id)
    {
        
        $where = array('id' => $id);
        $data['banner_info'] = Banner::where($where)->first();
        return view('banner.Edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required'
        ]);
        $update = ['status' => $request->status];
        if ($files = $request->file('image')) {
            $filename = $files->getClientOriginalName();
            // $destinationPath = public_path('storage') . '/gmp/banners'; // upload path
            $destinationPath = storage_path('app/public') . '/Banners';
            $profileImage = $filename;
            $files->move($destinationPath, $profileImage);
            $update['image'] = "$profileImage";
        }
        $update['image_url'] = $request->get('image_url');
        $update['redirect_type'] = $request->get('redirect_type');
        $update['status'] = $request->get('status');
       // $update['shop_name'] = session('shopName') != '' ? session('shopName') : '';
        $update['updated_at'] = Carbon::now();
        
        Banner::where('id',$id)->update($update);
        
        return Redirect::to('banners')
        ->with('success','Great! Banner successfully updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy($id)
    {
        Banner::where('id',$id)->delete();
        return Redirect::to('banners')->with('success','Banner successfully deleted');
    }

    public function getPromotionalBanners(Request $request)
    {
    
        $shop_name =  $request->header('X-Shopify-Store-Url');
        
        $response = $this->bannerRepo->getActiveBanners($shop_name);

        return BannerResource::collection($response);
    }
}
