<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            ⚠️ Low Coverage Areas (Donor Recruitment Needed)
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <p class="mb-6 text-gray-600">The following areas have the fewest registered donors. Consider running a campaign here.</p>

                <div class="space-y-6">
                    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 mb-6">
    <form action="{{ route('hospital.coverage') }}" method="GET" class="flex items-center gap-4">
        <label for="blood_group" class="font-bold text-gray-700">Filter by Blood Group:</label>
        <select name="blood_group" onchange="this.form.submit()" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">All Blood Groups</option>
            @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $group)
                <option value="{{ $group }}" {{ request('blood_group') == $group ? 'selected' : '' }}>
                    {{ $group }}
                </option>
            @endforeach
        </select>
        
        @if(request('blood_group'))
            <a href="{{ route('hospital.coverage') }}" class="text-sm text-indigo-600 hover:underline">Clear Filter</a>
        @endif
    </form>
</div>
                    @foreach($coverage as $area)
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-base font-medium text-gray-700">{{ $area->hospital_name }}</span>
                            <span class="text-sm font-medium text-red-700">{{ $area->total_donors }} Donors</span>
                        </div>
                        
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            @php
                                // Logic to color code based on density
                                $percentage = ($area->total_donors / 50) * 100; // Assuming 50 is a "healthy" target
                                $color = $area->total_donors < 5 ? 'bg-red-600' : ($area->total_donors < 15 ? 'bg-yellow-500' : 'bg-green-500');
                            @endphp
                            <div class="{{ $color }} h-2.5 rounded-full" style="width: {{ min($percentage, 100) }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>

                @if($coverage->isEmpty())
                    <p class="text-center py-10 text-gray-500">No donor data available to analyze.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>