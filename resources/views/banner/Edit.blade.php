@extends('banner.Layout')
@section('content')
    <h2 style="margin-top: 12px;" class="text-center">Edit Banner</a></h2>
    @if($banner_info->image)
        <img class="banner-img" id="original" src="{{  asset('storage/Banners/'.$banner_info->image) }}" height="70" width="70">
    @endif
    <br>
    <form action="/banners/update/{{$banner_info->id}}" method="POST" name="update_banner" enctype="multipart/form-data">
        {{ csrf_field() }}
        @method('PATCH')
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Status *</strong>
                    <select class="form-control" name="status">
                        <option>Select Status</option>
                        <option value=1 {{ $banner_info->status == 1 ? 'selected' : '' }} > Active </option>
                        <option value=0 {{ $banner_info->status == 0 ? 'selected' : '' }} > Inactive </option>
                    </select>
                    <span class="text-danger">{{ $errors->first('status') }}</span>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Redirect Type (Product/Category)</strong>
                    <select class="form-control" name="redirect_type">
                        <option value="">Select</option>
                        <option value="product" {{ $banner_info->redirect_type == "product" ? 'selected' : '' }} > Product </option>
                        <option value="category" {{ $banner_info->redirect_type == "category" ? 'selected' : '' }} > Category </option>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Product/Category ID Link</strong>
                    <input type="text" name="image_url" class="form-control" value="{{ $banner_info->image_url }}">
                </div>
            </div>        
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Banner Image *</strong>
                    <input type="file" name="image" class="form-control" placeholder="" value="{{ $banner_info->image }}">
                    <p>{{ $banner_info->image }} </p>
                    <span class="text-danger">{{ $errors->first('image') }}</span>
                </div>
            </div>

            <div class="col-md-12">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </form>
@endsection

<style>
.banner-img {
    display: block;
    margin: 0 auto;
}
</style>