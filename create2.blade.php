<x-app-layout>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Add New Donor</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 shadow-sm sm:rounded-lg border border-gray-200">
                
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

                    <input type="hidden" name="status" value="pending">

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