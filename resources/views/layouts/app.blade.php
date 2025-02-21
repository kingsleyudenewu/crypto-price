<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Crypto Price Tracker</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @livewireStyles
</head>
<body class="bg-gray-100">
<div class="container mx-auto px-4 py-8">
    @yield('content')
</div>

@livewireScripts
<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
