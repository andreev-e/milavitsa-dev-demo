<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>DIT-RPA</title>
    <link type="text/css" rel="stylesheet" href="{{ mix('css/app.css') }}">
</head>
<body id="page-top" class="sidebar-toggled">
<div id="wrapper">
    <div id="content-wrapper">
        <div class="container-fluid">
            @yield('content')
        </div>
    </div>
</div>
<script src="{{ mix('js/app.js') }}"></script>
@yield('script')
</body>
</html>
