<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Public Blood Stock Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100">
    <div class="min-h-screen py-10">
        <div class="max-w-6xl mx-auto px-6">

            <div class="mb-8 text-center">
                <h1 class="text-3xl font-extrabold text-gray-800">
                    Blood Bank Stock Dashboard
                </h1>
                <p class="text-gray-500 mt-2">
                    Public view of currently available blood stock.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                @forelse($summary as $row)
                    <div class="bg-white rounded-xl shadow p-6 border">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 font-bold uppercase">
                                    Blood Type
                                </p>

                                <h2 class="text-4xl font-extrabold text-red-600 mt-2">
                                    {{ $row->blood_type }}
                                </h2>
                            </div>

                            <div class="text-right">
                                <p class="text-sm text-gray-500 font-bold uppercase">
                                    Units
                                </p>

                                <p class="text-3xl font-extrabold text-gray-800 mt-2">
                                    {{ $row->available_units }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-4">
                            @if($row->available_units <= 2)
                                <span class="inline-block bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-extrabold">
                                    LOW STOCK
                                </span>
                            @else
                                <span class="inline-block bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-extrabold">
                                    AVAILABLE
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-4 bg-white rounded-xl shadow p-8 text-center text-gray-500">
                        No blood stock available right now.
                    </div>
                @endforelse
            </div>

            <div class="mt-10 text-center">
                <a href="{{ route('login') }}" class="text-blue-600 font-bold underline">
                    Hospital/Admin Login
                </a>
            </div>

        </div>
    </div>
</body>
</html>