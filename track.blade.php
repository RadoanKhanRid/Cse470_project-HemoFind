<x-app-layout>
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Map Section -->
            <div class="space-y-6">
                <div class="bg-white p-8 rounded-[3rem] shadow-2xl border border-slate-100">
                    <div class="flex justify-between items-center mb-8">
                        <h2 class="text-3xl font-black text-slate-800">Donor Live Tracking</h2>
                        <div class="flex items-center gap-4">
                            @if(Auth::id() === $donation->donor_id && in_array($donation->status, ['accepted', 'on_the_way', 'arrived']))
                                <form action="{{ route('donation.cancel', $donation) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this help session?')">
                                    @csrf
                                    <button type="submit" class="text-sm font-bold text-red-500 hover:text-red-700 uppercase tracking-widest bg-red-50 px-4 py-2 rounded-full border border-red-100 transition-all hover:scale-105">
                                        Cancel Help
                                    </button>
                                </form>
                            @endif
                            <span id="status-badge" class="px-6 py-2 rounded-full text-sm font-black bg-green-100 text-green-700 uppercase tracking-widest border border-green-200">
                                {{ ucfirst($donation->status) }}
                            </span>
                        </div>
                    </div>
                    
                    <div id="trackingMap" style="height: 550px; border-radius: 2rem; border: 4px solid #f8fafc;" class="shadow-inner"></div>
                    
                    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-8 p-8 bg-slate-50 rounded-[2.5rem] border border-slate-100 shadow-inner">
                        <!-- Donor Details -->
                        <div class="flex items-center gap-6">
                            <div class="w-20 h-20 bg-hemo-600 rounded-[1.5rem] flex items-center justify-center text-white font-black text-3xl shadow-xl shadow-hemo-200">
                                {{ substr($donation->donor->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-[11px] text-slate-400 font-bold uppercase tracking-[0.2em] mb-1">Donor Details</p>
                                <p class="font-black text-2xl text-slate-800">{{ $donation->donor->name }}</p>
                                <div class="flex items-center gap-3 mt-1">
                                    <span class="px-2 py-0.5 bg-hemo-100 text-hemo-600 rounded text-[10px] font-black">{{ $donation->donor->blood_group }}</span>
                                    <a href="tel:{{ $donation->donor->mobile }}" class="text-slate-600 font-bold text-sm hover:text-hemo-600 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 005.47 5.47l.773-1.548a1 1 0 011.06-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path></svg>
                                        {{ $donation->donor->mobile }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Receiver Details -->
                        <div class="md:text-right flex flex-col md:items-end justify-center">
                            <p class="text-[11px] text-slate-400 font-bold uppercase tracking-[0.2em] mb-1">Patient & Destination</p>
                            <p class="font-black text-2xl text-slate-800">{{ $donation->bloodRequest->patient_name }}</p>
                            <div class="flex md:justify-end items-center gap-3 mt-1">
                                <span class="px-2 py-0.5 bg-slate-200 text-slate-700 rounded text-[10px] font-black">Need: {{ $donation->bloodRequest->blood_group }}</span>
                                <span class="px-2 py-0.5 {{ $donation->bloodRequest->urgency === 'immediate' ? 'bg-red-600 text-white' : 'bg-orange-500 text-white' }} rounded text-[10px] font-black uppercase">{{ $donation->bloodRequest->urgency }}</span>
                            </div>
                            <p class="text-slate-500 font-bold text-sm mt-2 flex items-center md:justify-end gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                {{ $donation->bloodRequest->hospital_name }}, {{ $donation->bloodRequest->area }}
                            </p>
                            
                            @if(Auth::id() === $donation->donor_id)
                                <a href="https://www.google.com/maps/dir/?api=1&destination={{ $donation->bloodRequest->latitude }},{{ $donation->bloodRequest->longitude }}" target="_blank" class="mt-4 inline-flex items-center gap-2 bg-slate-900 text-white px-6 py-2 rounded-xl font-bold text-sm hover:bg-slate-800 transition-all">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L4.5 20.29l.71.71L12 18l6.79 3 .71-.71L12 2z"/></svg>
                                    Navigate to Hospital
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Leaflet & Real-time Logic -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const donationId = {{ $donation->id }};
        const isDonor = {{ Auth::id() === $donation->donor_id ? 'true' : 'false' }};
        const hospitalLat = {{ $donation->bloodRequest->latitude ?? 23.8103 }};
        const hospitalLng = {{ $donation->bloodRequest->longitude ?? 90.4125 }};

        const requesterId = {{ $donation->bloodRequest->user_id }};
        const isRequester = {{ Auth::id() }} === requesterId;

        // Initialize Map
        var map = L.map('trackingMap').setView([hospitalLat, hospitalLng], 14);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

        // Hospital Marker (Static reference)
        L.marker([hospitalLat, hospitalLng], {
            icon: L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                iconSize: [25, 41], iconAnchor: [12, 41]
            })
        }).addTo(map).bindPopup('Hospital Location');

        // Donor Marker (Live)
        var donorMarker = L.marker([0, 0], {
            icon: L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
                iconSize: [25, 41], iconAnchor: [12, 41]
            })
        }).addTo(map).bindPopup('Donor');

        // Requester Marker (Live)
        var requesterMarker = L.marker([hospitalLat, hospitalLng], {
            icon: L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
                iconSize: [25, 41], iconAnchor: [12, 41]
            })
        }).addTo(map).bindPopup('Patient/Receiver');

        // 1. Live Location Sender (Runs for both Donor and Requester)
        if ("geolocation" in navigator) {
            setInterval(() => {
                navigator.geolocation.getCurrentPosition(position => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    if (isDonor) donorMarker.setLatLng([lat, lng]);
                    if (isRequester) requesterMarker.setLatLng([lat, lng]);
                    
                    fetch("{{ route('donation.location.update', $donation) }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ latitude: lat, longitude: lng })
                    });
                });
            }, 5000);
        }

        // 2. Map Polling (Update markers from server)
        setInterval(() => {
            fetch("{{ route('donation.data', $donation) }}") 
                .then(res => res.json())
                .then(data => {
                    if (data.live_latitude && data.live_longitude) {
                        donorMarker.setLatLng([data.live_latitude, data.live_longitude]);
                    }
                    if (data.requester_latitude && data.requester_longitude) {
                        requesterMarker.setLatLng([data.requester_latitude, data.requester_longitude]);
                    }
                });
        }, 5000);
    </script>
</x-app-layout>
