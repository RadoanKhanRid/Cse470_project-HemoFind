<x-app-layout>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="glass p-10 rounded-[3rem] border border-white/20 shadow-2xl text-center">
                <div class="w-24 h-24 bg-hemo-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg shadow-hemo-200">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <h2 class="text-3xl font-black text-slate-800 mb-2">Check-in as Donor</h2>
                <p class="text-slate-600 mb-8">Complete your profile to start helping others in Dhaka.</p>

                <!-- Leaflet CSS -->
                <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
                <style>
                    #donorMap { height: 300px; border-radius: 1rem; margin-top: 1rem; border: 2px solid #f1f5f9; z-index: 1; }
                </style>

                <form method="POST" action="{{ route('donor.profile.update') }}" class="space-y-6 text-left">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="blood_group" :value="__('Your Blood Group')" />
                            <select id="blood_group" name="blood_group" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $group)
                                    <option value="{{ $group }}" {{ $user->blood_group == $group ? 'selected' : '' }}>{{ $group }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('blood_group')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="area" :value="__('Your Area/Neighborhood')" />
                            <x-text-input id="area" class="block mt-1 w-full" type="text" name="area" :value="old('area', $user->area)" placeholder="e.g. Gulshan, Dhanmondi" required />
                            <x-input-error :messages="$errors->get('area')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Geocoding & Map -->
                    <div class="border-t border-slate-100 pt-6">
                        <label class="block font-bold text-sm text-slate-700 mb-2">Set Your Exact Location on Map</label>
                        <div class="relative mb-3">
                            <input type="text" id="donorAddressSearch" placeholder="Type your street or area to find on map..." 
                                   class="w-full p-3 pr-10 border border-slate-200 rounded-xl text-sm focus:border-hemo-600 focus:ring-0">
                            <button type="button" onclick="searchDonorLocation()" class="absolute right-3 top-3 text-slate-400 hover:text-hemo-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </button>
                        </div>
                        <div id="donorMap"></div>
                        <p class="text-[10px] text-slate-400 mt-2 italic text-center">Drag the marker to your precise location to help patients calculate ETA accurately.</p>
                        
                        <input type="hidden" id="latitude" name="latitude" value="{{ $user->latitude ?? '23.8103' }}">
                        <input type="hidden" id="longitude" name="longitude" value="{{ $user->longitude ?? '90.4125' }}">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="last_donated_at" :value="__('Last Donated Date (Optional)')" />
                            <x-text-input id="last_donated_at" class="block mt-1 w-full" type="date" name="last_donated_at" :value="old('last_donated_at', $user->last_donated_at)" />
                            <x-input-error :messages="$errors->get('last_donated_at')" class="mt-2" />
                        </div>

                        <div class="flex items-center mt-6">
                            <input id="is_available" type="checkbox" name="is_available" value="1" class="rounded border-gray-300 text-hemo-600 shadow-sm focus:ring-hemo-500" {{ $user->is_available ? 'checked' : '' }}>
                            <label for="is_available" class="ml-2 text-sm text-gray-600 font-bold">Available to donate now</label>
                        </div>
                    </div>

                    <div class="mt-8">
                        <x-primary-button class="w-full justify-center py-4 text-lg bg-hemo-600 hover:bg-hemo-700 shadow-xl shadow-hemo-100">
                            {{ __('Update & Check-in as Donor') }}
                        </x-primary-button>
                    </div>
                </form>

                <!-- Leaflet JS -->
                <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
                <script>
                    var lat = {{ $user->latitude ?? 23.8103 }};
                    var lng = {{ $user->longitude ?? 90.4125 }};
                    
                    var donorMap = L.map('donorMap').setView([lat, lng], 14);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap contributors'
                    }).addTo(donorMap);

                    var donorMarker = L.marker([lat, lng], {
                        draggable: true
                    }).addTo(donorMap);

                    donorMarker.on('dragend', function(e) {
                        var pos = donorMarker.getLatLng();
                        document.getElementById('latitude').value = pos.lat;
                        document.getElementById('longitude').value = pos.lng;
                    });

                    donorMap.on('click', function(e) {
                        donorMarker.setLatLng(e.latlng);
                        document.getElementById('latitude').value = e.latlng.lat;
                        document.getElementById('longitude').value = e.latlng.lng;
                    });

                    function searchDonorLocation() {
                        var query = document.getElementById('donorAddressSearch').value;
                        if (query.length < 3) return;

                        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${query}, Dhaka`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.length > 0) {
                                    var newLat = data[0].lat;
                                    var newLon = data[0].lon;
                                    donorMap.setView([newLat, newLon], 16);
                                    donorMarker.setLatLng([newLat, newLon]);
                                    document.getElementById('latitude').value = newLat;
                                    document.getElementById('longitude').value = newLon;
                                }
                            });
                    }

                    document.getElementById('donorAddressSearch').addEventListener('keypress', function (e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            searchDonorLocation();
                        }
                    });
                </script>
            </div>
        </div>
    </div>
</x-app-layout>
