<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Cartlex') }} Fleet Portal - @yield('title', 'Welcome')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-gradient-to-br from-[#0D1B2A] to-[#1A2F45] flex items-center justify-center p-4" x-data>

<div class="w-full max-w-md">
    <!-- Logo -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center gap-3 mb-3">
            <div class="w-12 h-12 bg-[#C41E3A] rounded-2xl flex items-center justify-center shadow-lg">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div class="text-left">
                <div class="text-white font-bold text-2xl leading-none">Cartlex</div>
                <div class="text-slate-400 text-sm">Fleet Partner Portal</div>
            </div>
        </div>
    </div>

    <!-- Card -->
    <div class="bg-white rounded-2xl shadow-2xl p-8">
        @yield('content')
    </div>

    <p class="text-center text-slate-500 text-xs mt-6">
        © {{ date('Y') }} Cartlex. Powered by Limitlex Technologies.
    </p>
</div>

</body>
</html>
