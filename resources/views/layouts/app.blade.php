<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-100">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name') }}</title>

        <link rel="apple-touch-icon" sizes="180x180" href="/images/logo.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/images/logo.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/images/logo.png">


        <!-- Fonts -->

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

    </head>
    <body class="h-full">
        <div id="app">
            <app-layout :channels="{{ $channels }}">
                @yield('content')
            </app-layout>
        </div>
    </body>
</html>
