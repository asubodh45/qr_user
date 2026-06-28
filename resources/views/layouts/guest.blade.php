<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'QR Employee') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans text-gray-900 antialiased">

<div class="min-h-screen flex flex-col items-center justify-center py-12 px-4">

    <div class="mb-8 text-center">
        <div class="inline-flex items-center gap-3">
            <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4v1m0 14v1M4.22 4.22l.71.71M19.07 19.07l.71.71M1 12h1M22 12h1M4.22 19.78l.71-.71M19.07 4.93l.71-.71"/>
            </svg>
            <span class="text-2xl font-bold text-gray-800">QR Employee</span>
        </div>
    </div>

    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg px-8 py-8">
        {{ $slot }}
    </div>

</div>

</body>
</html>
