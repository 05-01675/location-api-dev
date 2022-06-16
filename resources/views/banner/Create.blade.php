@extends('banner.Layout')
@section('content')
    <h2 style="margin-top: 12px;" class="text-center">Add Banner</a></h2>
    <br>
    <form action="/banners/store" method="post" name="add_banner" enctype="multipart/form-data">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="shopname" value="{{ session('shopName') }}">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Status *</strong>
                    <select class="form-control" name="status">
                        <option value=1> Active </option>
                        <option value=0> Inactive </option>
                    </select>
                    <span class="text-danger">{{ $errors->first('status') }}</span>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Redirect Type (Product/Category)</strong>
                    <select class="form-control" name="redirect_type">
                        <option value="">Select</option>
                        <option value="product"> Product </option>
                        <option value="category"> Category </option>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Product/Category ID Link</strong>
                    <input type="text" name="image_url" class="form-control" placeholder="">
                    <span class="text-danger">{{ $errors->first('image_url') }}</span>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Banner Image *</strong>
                    <input type="file" name="image" class="form-control" placeholder="">
                    <span class="text-danger">{{ $errors->first('image') }}</span>
                </div>
            </div>
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </form>
@endsection