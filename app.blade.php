<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

     
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />


        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            hemo: '#b91c1c',
                            hemodark: '#7f1d1d',
                        }
                    }
                }
            }
        </script>
        <style>
            .text-indigo-600 { color: #b91c1c !important; }
            .bg-indigo-600 { background-color: #b91c1c !important; }
            .ring-indigo-500 { --tw-ring-color: #b91c1c !important; }
        </style>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>
    <body class="font-sans antialiased text-gray-900">
        <div class="min-h-screen bg-red-50">
            @include('layouts.navigation')


            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset


            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
