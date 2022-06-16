@extends('shopify-app::layouts.default')

@section('content')
    <!-- You are: (shop domain name) -->
 <!-- <p>You are: {{ session('shopName') }}</p>  -->
    
    <!-- <a class="btn" href="{{url('app_auth')}}">App Info</a> -->
    
    <a class="btn" href="/banners">Mini App Banners</a>
@endsection

@section('scripts')
    @parent

    <script type="text/javascript">
        var AppBridge = window['app-bridge'];
        var actions = AppBridge.actions;
        var TitleBar = actions.TitleBar;
        var Button = actions.Button;
        var Redirect = actions.Redirect;
        var titleBarOptions = {
            title: 'Welcome',
        };
        var myTitleBar = TitleBar.create(app, titleBarOptions);
    </script>
@endsection

<style>
    .btn{
        display: block;
        color: #fff;
        background: #7E1BAA;
        width: 150px;
        margin: 20px 0;
        text-decoration: none;
        text-align: center;
        padding: 20px;
        border-radius: 5px;
    }
</style>