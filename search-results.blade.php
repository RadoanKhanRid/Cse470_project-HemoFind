<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Search Results') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-red-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Summary -->
            <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-hemo">
                <p class="text-gray-700 text-lg">Looking for <strong class="text-hemo">{{ $bg }}</strong> within <strong>{{ $radius }}km</strong> radius.</p>
                <input type="hidden" id="hospitalLat" value="{{ $lat }}">
                <input type="hidden" id="hospitalLng" value="{{ $lng }}">
            </div>

            <!-- Exact Matches -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-xl font-bold text-green-700 mb-4 border-b pb-2 flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Exact Matches ({{ $exactMatches->count() }})
                    </h3>
                    @if($exactMatches->isEmpty())
                        <p class="text-gray-500 italic">No exact matches found within {{ $radius }}km.</p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($exactMatches as $donor)
                                @include('components.donor-card', ['donor' => $donor, 'type' => 'Exact'])

                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Compatible Matches -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-xl font-bold text-blue-700 mb-4 border-b pb-2 flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Compatible Matches ({{ $compatibleMatches->count() }})
                    </h3>
                    @if($compatibleMatches->isEmpty())
                        <p class="text-gray-500 italic">No compatible matches found within {{ $radius }}km.</p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($compatibleMatches as $donor)
                                @include('components.donor-card', ['donor' => $donor, 'type' => 'Compatible'])

                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Universal Fallback (O-) -->
            @if($bg !== 'O-')
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-xl font-bold text-orange-700 mb-4 border-b pb-2 flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Universal Donors (O-) Fallback ({{ $universalMatches->count() }})
                    </h3>
                    @if($universalMatches->isEmpty())
                        <p class="text-gray-500 italic">No universal donors found within {{ $radius }}km.</p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($universalMatches as $donor)
                                @include('components.donor-card', ['donor' => $donor, 'type' => 'Universal'])

                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            @endif

        </div>
    </div>

    <!-- Routing ETA Script via OSRM -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const hLat = document.getElementById('hospitalLat').value;
            const hLng = document.getElementById('hospitalLng').value;
            
            const etaElements = document.querySelectorAll('.eta-display');
            
            etaElements.forEach(el => {
                const dLat = el.dataset.lat;
                const dLng = el.dataset.lng;
                
                // Using OSRM public routing API to calculate driving ETA
                const url = `https://router.project-osrm.org/route/v1/driving/${dLng},${dLat};${hLng},${hLat}?overview=false`;
                
                fetch(url)
                    .then(res => res.json())
                    .then(data => {
                        if(data.routes && data.routes.length > 0) {
                            const durationSeconds = data.routes[0].duration;
                            const minutes = Math.round(durationSeconds / 60);
                            el.innerHTML = `<span class="text-green-700 font-bold flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> ETA: ~${minutes} mins driving</span>`;
                        } else {
                            el.innerText = "ETA: Unavailable";
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        el.innerText = "ETA: Error calculating";
                    });
            });
        });
    </script>
</x-app-layout>
