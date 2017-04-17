<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Harpia - @yield('title')</title>

    <link rel="stylesheet" href="{{ asset('/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/toastr.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/app.css') }}">

    @yield('stylesheets')
</head>
<body class="hold-transition skin-blue layout-top-nav">
    <div class="wrapper">
        @include('layouts.includes.header-clean')

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            @yield('content')
        </div>

        @include('layouts.includes.footer')
    </div>

    <!-- jQuery 2.2.3 -->
    <script src="{{ asset('/js/plugins/jquery.min.js') }}"></script>
    <!-- Bootstrap 3.3.6 -->
    <script src="{{ asset('/js/bootstrap.js') }}"></script>
    <script src="{{ asset('/js/plugins/toastr.min.js') }}"></script>
    <script src="{{ asset('/js/app.js') }}"></script>

    {!! Flash::render() !!}
    @yield('scripts')
</body>
</html>