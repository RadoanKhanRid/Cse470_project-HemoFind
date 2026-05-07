<x-app-layout>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="glass p-10 rounded-[3rem] border border-white/20 shadow-2xl">
                <h2 class="text-3xl font-black text-slate-800 mb-8">Post Blood Request</h2>

                <form method="POST" action="{{ route('requests.store') }}" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="patient_name" :value="__('Patient Name')" />
                            <x-text-input id="patient_name" class="block mt-1 w-full" type="text" name="patient_name" :value="old('patient_name')" required autofocus />
                            <x-input-error :messages="$errors->get('patient_name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="blood_group" :value="__('Blood Group Needed')" />
                            <select id="blood_group" name="blood_group" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $group)
                                    <option value="{{ $group }}">{{ $group }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('blood_group')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="hospital_name" :value="__('Hospital Name')" />
                            <x-text-input id="hospital_name" class="block mt-1 w-full" type="text" name="hospital_name" :value="old('hospital_name')" required />
                            <x-input-error :messages="$errors->get('hospital_name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="area" :value="__('Area (Dhaka)')" />
                            <x-text-input id="area" class="block mt-1 w-full" type="text" name="area" :value="old('area')" required />
                            <x-input-error :messages="$errors->get('area')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="contact_number" :value="__('Contact Number')" />
                            <x-text-input id="contact_number" class="block mt-1 w-full" type="text" name="contact_number" :value="old('contact_number')" required />
                            <x-input-error :messages="$errors->get('contact_number')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="urgency" :value="__('Urgency')" />
                            <select id="urgency" name="urgency" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="Urgent">Urgent</option>
                                <option value="Scheduled">Scheduled</option>
                            </select>
                            <x-input-error :messages="$errors->get('urgency')" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="description" :value="__('Additional Information')" />
                        <textarea id="description" name="description" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" rows="3">{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <!-- Location Picker -->
                    <div class="space-y-4">
                        <x-input-label :value="__('Patient Location / Hospital Destination')" />
                        <p class="text-xs text-slate-500 mb-2">Drag the marker to the exact hospital or patient location.</p>
                        <div id="map" style="height: 300px; border-radius: 1.5rem; border: 2px solid #e2e8f0;"></div>
                        <input type="hidden" name="latitude" id="latitude" value="23.8103">
                        <input type="hidden" name="longitude" id="longitude" value="90.4125">
                    </div>

                    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
                    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
                    <script>
                        var map = L.map('map').setView([23.8103, 90.4125], 13);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

                        var marker = L.marker([23.8103, 90.4125], {draggable: true}).addTo(map);

                        marker.on('dragend', function(e) {
                            var pos = marker.getLatLng();
                            document.getElementById('latitude').value = pos.lat;
                            document.getElementById('longitude').value = pos.lng;
                        });

                        // Current Location button
                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(function(position) {
                                var lat = position.coords.latitude;
                                var lng = position.coords.longitude;
                                map.setView([lat, lng], 15);
                                marker.setLatLng([lat, lng]);
                                document.getElementById('latitude').value = lat;
                                document.getElementById('longitude').value = lng;
                            });
                        }
                    </script>

                    <div class="flex items-center justify-end mt-4">
                        <x-primary-button class="w-full justify-center py-4 text-lg">
                            {{ __('Post Request') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
