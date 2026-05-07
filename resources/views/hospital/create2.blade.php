<x-app-layout>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Add New Donor</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 shadow-sm sm:rounded-lg border border-gray-200">
              {{-- This catches the "Medical History" logic from the Controller --}}
@if(session('error'))
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
        {{ session('error') }}
    </div>
@endif

{{-- This catches the validation rules like "min:18" or "required" --}}
@if ($errors->any())
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
                
                <form action="{{ route('donations.store') }}" method="POST">
                    @csrf

                    <div class="mb-5">
                        <label class="block font-bold text-gray-700 mb-2">Donor Full Name</label>
                        <input type="text" name="donor_name" required class="w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div class="mb-5">
                        <label class="block font-bold text-gray-700 mb-2">Blood Group</label>
                        <select name="blood_type" required class="w-full border-gray-300 rounded-md shadow-sm">
                            <option value="A+">A+</option><option value="A-">A-</option>
                            <option value="B+">B+</option><option value="B-">B-</option>
                            <option value="O+">O+</option><option value="O-">O-</option>
                            <option value="AB+">AB+</option><option value="AB-">AB-</option>
                        </select>
                    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
    <div>
        <label class="block font-bold text-gray-700 mb-2">Phone Number</label>
        <input type="text" name="phone" required 
               class="w-full border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500" 
               placeholder="e.g. 01712345678">
    </div>

    <div>
        <label class="block font-bold text-gray-700 mb-2">Email Address</label>
        <input type="email" name="email" required 
               class="w-full border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500" 
               placeholder="e.g. donor@example.com">
    </div>
    <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 mb-6 mt-2">
    <h3 class="font-bold text-gray-700 border-b pb-2 mb-4 uppercase text-xs tracking-wider">Medical Eligibility</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div>
            <label class="block font-bold text-gray-700 mb-2 text-sm">Age</label>
            <input type="number" name="age" required 
                   class="w-full border-gray-300 rounded-md shadow-sm" 
                   placeholder="18 - 65">
        </div>

        <div>
            <label class="block font-bold text-gray-700 mb-2 text-sm">Weight (kg)</label>
            <input type="number" name="weight" required 
                   class="w-full border-gray-300 rounded-md shadow-sm" 
                   placeholder="Min 50kg">
        </div>
    </div>

    <div class="space-y-4">
        <div class="flex items-center justify-between bg-white p-3 rounded border">
            <label class="text-sm font-medium text-gray-700">Tattoo or Surgery in last 6 months?</label>
            <div class="flex gap-4">
                <label class="inline-flex items-center">
                    <input type="radio" name="has_recent_tattoo_surgery" value="1" class="text-red-600 focus:ring-red-500">
                    <span class="ml-2 text-sm">Yes</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="radio" name="has_recent_tattoo_surgery" value="0" checked class="text-green-600 focus:ring-green-500">
                    <span class="ml-2 text-sm">No</span>
                </label>
            </div>
        </div>

        <div class="flex items-center justify-between bg-white p-3 rounded border">
            <label class="text-sm font-medium text-gray-700">Any Chronic Diseases (Hepatitis/HIV)?</label>
            <div class="flex gap-4">
                <label class="inline-flex items-center">
                    <input type="radio" name="has_chronic_condition" value="1" class="text-red-600 focus:ring-red-500">
                    <span class="ml-2 text-sm">Yes</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="radio" name="has_chronic_condition" value="0" checked class="text-green-600 focus:ring-green-500">
                    <span class="ml-2 text-sm">No</span>
                </label>
            </div>
        </div>
    </div>
</div>
</div>

                    <input type="hidden" name="status" value="pending">
                    <div class="mt-4">
                        <label class="block font-medium text-sm text-gray-700">
                            Hospital Name
                        </label>

                        <input
                            type="text"
                            name="hospital_name"
                            value="{{ old('hospital_name') }}"
                            required
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full"
                        >
                    </div>
                    <div class="mb-5">
                        <label class="block font-bold text-gray-700 mb-2">Donor Home Location</label>
                        <div class="flex gap-2 mb-2">
                            <input type="text" id="address_input" class="w-full border-gray-300 rounded-md shadow-sm" placeholder="Search area (e.g. Uttara, Dhaka)">
                            <button type="button" onclick="searchAddress()" class="bg-blue-600 text-white px-4 py-2 rounded font-bold">Search</button>
                        </div>
                        <ul id="results_list" class="bg-white border rounded hidden max-h-40 overflow-y-auto z-50 relative shadow-lg"></ul>
                        
                        <div id="map" style="height: 300px;" class="mt-4 rounded border shadow-inner"></div>
                        <p class="text-xs text-gray-500 mt-2 italic">Drag the marker to the donor's exact home.</p>
                    </div>

                    <input type="hidden" name="lat" id="lat">
                    <input type="hidden" name="lng" id="lng">

                    <div class="flex justify-end gap-4 mt-8">
                        <a href="{{ route('hospital.index') }}" class="text-gray-500 py-2">Cancel</a>
                        <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-md font-bold uppercase tracking-widest hover:bg-indigo-700 transition">
                            Save Donor
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        var map = L.map('map').setView([23.8103, 90.4125], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        var marker = L.marker([23.8103, 90.4125], {draggable: true}).addTo(map);

        function updateCoords(lat, lng) {
            document.getElementById('lat').value = lat;
            document.getElementById('lng').value = lng;
        }
        updateCoords(23.8103, 90.4125);

        marker.on('dragend', function(e) {
            var pos = marker.getLatLng();
            updateCoords(pos.lat, pos.lng);
        });

        function searchAddress() {
            var query = document.getElementById('address_input').value;
            var list = document.getElementById('results_list');
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${query}`)
                .then(res => res.json())
                .then(data => {
                    list.innerHTML = '';
                    list.classList.remove('hidden');
                    data.slice(0, 5).forEach(item => {
                        var li = document.createElement('li');
                        li.className = "p-2 hover:bg-gray-100 cursor-pointer text-sm border-b";
                        li.innerText = item.display_name;
                        li.onclick = function() {
                            map.setView([item.lat, item.lon], 16);
                            marker.setLatLng([item.lat, item.lon]);
                            updateCoords(item.lat, item.lon);
                            list.classList.add('hidden');
                        };
                        list.appendChild(li);
                    });
                });
        }
    </script>
</x-app-layout>