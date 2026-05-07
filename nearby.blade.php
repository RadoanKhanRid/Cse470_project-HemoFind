<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Donors Near {{ $request->hospital_name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow sm:rounded-lg">
                <div class="mb-6">
                    <h3 class="text-lg font-bold">Requirement: <span class="text-red-600">{{ $request->blood_type }}</span></h3>
                    <p class="text-gray-600">Searching within a 5km radius of the hospital.</p>
                </div>

                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b">
                            <th class="py-2">Donor Name</th>
                            <th class="py-2">Distance</th>
                            <th class="py-2">Status</th>
                            <th class="py-2">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($nearbyDonors as $donor)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3">{{ $donor->donor_name }}</td>
                            <td class="py-3 font-bold text-blue-600">
                                {{ number_format($donor->distance, 2) }} km
                            </td>
                            <td class="py-3">
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Available</span>
                            </td>
                            <td class="py-3">
                                <a href="#" class="bg-red-500 text-white px-3 py-1 rounded text-sm font-bold">Contact</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-10 text-center text-gray-500">
                                No matching donors found within 5km of this location.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                
                <div class="mt-6">
                    <a href="{{ route('blood.requests') }}" class="text-gray-600 underline">Back to List</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>