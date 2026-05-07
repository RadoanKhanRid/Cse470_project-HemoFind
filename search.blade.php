<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Find Blood Donors') }}
        </h2>
    </x-slot>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        #map { 
            height: 450px; 
            width: 100%; 
            border-radius: 2rem; 
            margin-bottom: 1.5rem; 
            border: 8px solid white; 
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            z-index: 10;
        }
        .leaflet-container {
            font-family: 'Outfit', sans-serif;
        }
        .blood-radio:checked + div {
            border-color: #dc2626;
            background-color: #fef2f2;
            color: #b91c1c;
            transform: scale(1.05);
            box-shadow: 0 10px 15px -3px rgba(220, 38, 38, 0.2);
        }
    </style>

    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <!-- Search Form Card -->
                <div class="lg:col-span-12">
                    <div class="bg-white p-8 md:p-12 rounded-[3rem] shadow-2xl border border-slate-100">
                        <div class="text-center mb-10">
                            <h2 class="text-4xl font-black text-slate-900 mb-4 tracking-tight">Find a Donor</h2>
                            <p class="text-slate-500 font-medium">Select blood type and mark the hospital location on the map.</p>
                        </div>

                        <form method="POST" action="{{ route('search.process') }}" class="space-y-10">
                            @csrf
                            
                            <!-- Step 1: Blood Group -->
                            <div>
                                <label class="block text-sm font-bold text-slate-400 uppercase tracking-widest mb-6 text-center">1. Select Needed Blood Group</label>
                                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-4">
                                    @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $group)
                                        <label class="relative cursor-pointer group">
                                            <input type="radio" name="blood_group" value="{{ $group }}" class="peer hidden blood-radio" required>
                                            <div class="text-center py-6 border-2 border-slate-100 rounded-2xl font-black text-2xl text-slate-400 transition-all hover:border-hemo-200 hover:bg-slate-50">
                                                {{ $group }}
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Step 2: Location -->
                            <div class="pt-10 border-t border-slate-100">
                                <label class="block text-sm font-bold text-slate-400 uppercase tracking-widest mb-6 text-center">2. Pin Hospital Location</label>
                                
                                <div class="max-w-4xl mx-auto">
                                    <!-- Geocoding Search Box -->
                                    <div class="relative mb-6">
                                        <div class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-400">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                        </div>
                                        <input type="text" id="addressSearch" placeholder="Search for hospital or area (e.g. Dhaka Medical, Uttara)..." 
                                               class="w-full p-5 pl-14 pr-32 border-2 border-slate-100 rounded-[2rem] focus:border-hemo-600 focus:ring-0 font-bold text-slate-700 bg-slate-50 shadow-inner">
                                        <button type="button" onclick="searchLocation()" class="absolute right-4 top-1/2 -translate-y-1/2 bg-hemo-600 text-white px-6 py-2 rounded-full font-bold hover:bg-hemo-700 transition shadow-lg shadow-hemo-100">
                                            Search
                                        </button>
                                    </div>

                                    <div id="map"></div>
                                    
                                    <div class="bg-blue-50 border border-blue-100 p-4 rounded-2xl flex items-center gap-4 mb-10">
                                        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white flex-shrink-0">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <p class="text-blue-800 text-sm font-medium">
                                            Drag the <span class="font-black text-hemo-600">Red Marker</span> to the exact hospital entrance for more accurate donor matching and ETA calculation.
                                        </p>
                                    </div>

                                    <input type="hidden" id="latitude" name="latitude" value="23.8103">
                                    <input type="hidden" id="longitude" name="longitude" value="90.4125">
                                    
                                    <div class="flex flex-col items-center gap-6">
                                        <div class="flex items-center gap-4">
                                            <span class="text-slate-400 font-bold uppercase text-xs tracking-widest">Search Radius</span>
                                            <div class="flex gap-2">
                                                @foreach([2, 5, 10, 15] as $r)
                                                    <label class="cursor-pointer">
                                                        <input type="radio" name="radius" value="{{ $r }}" class="peer hidden" {{ $r == 5 ? 'checked' : '' }}>
                                                        <div class="px-5 py-2 rounded-full border-2 border-slate-100 font-bold text-slate-500 peer-checked:border-hemo-600 peer-checked:bg-hemo-600 peer-checked:text-white transition-all">
                                                            {{ $r }}km
                                                        </div>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>

                                        <button class="group relative bg-hemo-600 hover:bg-hemo-700 text-white font-black py-6 px-20 rounded-[2.5rem] text-2xl shadow-2xl shadow-hemo-200 transition-all hover:scale-105 active:scale-95 flex items-center gap-4" type="submit">
                                            <span>Find Heroes</span>
                                            <svg class="w-8 h-8 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var map = L.map('map', {
                scrollWheelZoom: false
            }).setView([23.8103, 90.4125], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Enable scroll zoom on click
            map.on('focus', function() { map.scrollWheelZoom.enable(); });
            map.on('blur', function() { map.scrollWheelZoom.disable(); });

            // Custom Red Icon
            var redIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });

            var marker = L.marker([23.8103, 90.4125], {
                draggable: true,
                icon: redIcon
            }).addTo(map);

            // Update inputs
            function updateForm(lat, lng) {
                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lng;
            }

            marker.on('dragend', function(e) {
                var pos = marker.getLatLng();
                updateForm(pos.lat, pos.lng);
            });

            map.on('click', function(e) {
                marker.setLatLng(e.latlng);
                updateForm(e.latlng.lat, e.latlng.lng);
            });

            window.searchLocation = function() {
                var query = document.getElementById('addressSearch').value;
                if (query.length < 3) return;

                const btn = event.target;
                const originalText = btn.innerText;
                btn.innerText = '...';

                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}, Dhaka`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.length > 0) {
                            var lat = data[0].lat;
                            var lon = data[0].lon;
                            map.setView([lat, lon], 16);
                            marker.setLatLng([lat, lon]);
                            updateForm(lat, lon);
                        }
                    })
                    .finally(() => {
                        btn.innerText = originalText;
                    });
            };
            
            // Auto-detect current location
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var lat = position.coords.latitude;
                    var lng = position.coords.longitude;
                    map.setView([lat, lng], 15);
                    marker.setLatLng([lat, lng]);
                    updateForm(lat, lng);
                }, function() {
                    console.log("Geolocation permission denied.");
                });
            }

            document.getElementById('addressSearch').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    searchLocation();
                }
            });

            // Handle window resize for map scaling
            setTimeout(function() {
                map.invalidateSize();
            }, 500);
        });
    </script>
</x-app-layout>

