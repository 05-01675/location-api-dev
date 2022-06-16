<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>GCash Mini Program Banners</title>
        <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
        <style>
        body{
            background-color: #25274d;
            margin-top: 1em;
        }
        .container{
            /* background: #ff9b00; */
            background: #d5ddea;
            padding: 4%;
            border-radius: 0.5rem;
            /* border-top-left-radius: 0.5rem;
            border-bottom-left-radius: 0.5rem; */
        }
        </style>
    </head>
    <body>
        <div class="container">
            <br><br><br>
            @yield('content')
        </div>
    </body>
</html>