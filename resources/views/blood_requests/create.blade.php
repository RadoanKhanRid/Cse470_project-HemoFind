<x-app-layout>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Post New Blood Request</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 shadow-sm rounded-lg border">
                
                <form action="{{ route('blood.requests.store') }}" method="POST">
                    @csrf

                    <div class="mb-5">
                        <label class="block font-bold mb-2">Patient Name</label>
                        <input type="text" name="patient_name" required class="w-full border-gray-300 rounded-md">
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-5">
                        <div>
                            <label class="block font-bold mb-2">Blood Group</label>
                            <select name="blood_type" required class="w-full border-gray-300 rounded-md">
                                <option value="">Select</option>
                                <option value="A+">A+</option><option value="A-">A-</option>
                                <option value="B+">B+</option><option value="B-">B-</option>
                                <option value="O+">O+</option><option value="O-">O-</option>
                                <option value="AB+">AB+</option><option value="AB-">AB-</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold mb-2">Required Date</label>
                            <input type="date" name="required_date" required class="w-full border-gray-300 rounded-md">
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="block font-bold mb-2">Hospital Name & Location Search</label>
                        <div class="flex gap-2 mb-2">
                            <input type="text" id="address_input" name="hospital_name" required 
                                   class="w-full border-gray-300 rounded-md" 
                                   placeholder="Search hospital...">
                            <button type="button" onclick="searchAddress()" class="bg-blue-600 text-white px-4 py-2 rounded">Search</button>
                        </div>
                        <ul id="results_list" class="bg-white border rounded hidden max-h-40 overflow-y-auto z-50 relative"></ul>
                    </div>

                    <div id="map" style="height: 250px;" class="mb-5 rounded border"></div>

                    <input type="hidden" name="lat" id="lat">
                    <input type="hidden" name="lng" id="lng">

                    <button type="submit" class="w-full bg-red-600 text-white py-3 rounded-md font-bold hover:bg-red-700 transition">
                        Submit Request
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Initialize Map
        var map = L.map('map').setView([23.8103, 90.4125], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        var marker = L.marker([23.8103, 90.4125], {draggable: true}).addTo(map);

        function updateCoords(lat, lng) {
            document.getElementById('lat').value = lat;
            document.getElementById('lng').value = lng;
        }

        // Set initial coords
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
                            document.getElementById('address_input').value = item.display_name;
                            list.classList.add('hidden');
                        };
                        list.appendChild(li);
                    });
                });
        }
    </script>
</x-app-layout>