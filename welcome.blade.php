<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hemofind - Blood Finding App</title>
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
</head>
<body class="antialiased bg-red-50 text-gray-900 font-sans">
    <div class="min-h-screen flex flex-col pt-6 sm:pt-0">
        <div class="absolute top-0 right-0 p-6 text-right z-10">
            @if (Route::has('login'))
                <div class="space-x-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="font-semibold text-hemo hover:text-hemodark focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="font-semibold text-hemo hover:text-hemodark focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Log in</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="font-semibold text-hemo hover:text-hemodark focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Register</a>
                        @endif
                    @endauth
                </div>
            @endif
        </div>

        <div class="flex-grow flex items-center justify-center">
            <div class="text-center px-6">
                <h1 class="text-6xl font-extrabold text-hemo tracking-tight mb-4">Hemofind</h1>
                <p class="text-xl text-gray-700 max-w-2xl mx-auto mb-8">
                    Connect with blood donors instantly. A simple, reliable and quick way to find the blood you need, when you need it most.
                </p>
                <div class="flex justify-center gap-4">
                    <a href="{{ route('register') }}" class="bg-hemo hover:bg-hemodark text-white font-bold py-3 px-8 rounded-full shadow-lg transition transform hover:-translate-y-1">
                        Find a Donor
                    </a>
                    <a href="{{ route('register') }}" class="bg-white border-2 border-hemo text-hemo hover:bg-red-50 font-bold py-3 px-8 rounded-full shadow-lg transition transform hover:-translate-y-1">
                        Become a Donor
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>