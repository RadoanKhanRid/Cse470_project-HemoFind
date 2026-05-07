<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Blood Inventory Management
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="mb-6">
                <a href="{{ route('hospital.index') }}" class="bg-gray-700 text-white px-4 py-2 rounded font-bold">
                    Back to Hospital Dashboard
                </a>
            </div>

            <div class="bg-white p-6 shadow-sm sm:rounded-lg mb-8">
                <h3 class="text-lg font-bold mb-4">Stock Summary</h3>

                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: #f3f4f6;">
                            <th style="padding: 12px; text-align: left;">Blood Type</th>
                            <th style="padding: 12px; text-align: left;">Available Units</th>
                            <th style="padding: 12px; text-align: left;">Used Units</th>
                            <th style="padding: 12px; text-align: left;">Expired Units</th>
                            <th style="padding: 12px; text-align: left;">Stock Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($summary as $row)
                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                <td style="padding: 12px; color: #dc2626; font-weight: 800;">
                                    {{ $row->blood_type }}
                                </td>

                                <td style="padding: 12px;">
                                    {{ $row->available_units }}
                                </td>

                                <td style="padding: 12px;">
                                    {{ $row->used_units }}
                                </td>

                                <td style="padding: 12px;">
                                    {{ $row->expired_units }}
                                </td>

                                <td style="padding: 12px;">
                                    @if($row->available_units <= 2)
                                        <span style="background-color: #fee2e2; color: #991b1b; padding: 4px 10px; border-radius: 9999px; font-weight: 800; font-size: 12px;">
                                            LOW STOCK
                                        </span>
                                    @else
                                        <span style="background-color: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 9999px; font-weight: 800; font-size: 12px;">
                                            GOOD
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="padding: 20px; text-align: center; color: #9ca3af;">
                                    No inventory yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <h3 class="text-lg font-bold mb-4">Blood Bag Details</h3>

                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: #f3f4f6;">
                            <th style="padding: 12px; text-align: left;">Bag Number</th>
                            <th style="padding: 12px; text-align: left;">Donor</th>
                            <th style="padding: 12px; text-align: left;">Blood Type</th>
                            <th style="padding: 12px; text-align: left;">Hospital</th>
                            <th style="padding: 12px; text-align: left;">Collected</th>
                            <th style="padding: 12px; text-align: left;">Expires</th>
                            <th style="padding: 12px; text-align: left;">Status</th>
                            <th style="padding: 12px; text-align: left;">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($bags as $bag)
                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                <td style="padding: 12px; font-weight: 700;">
                                    {{ $bag->bag_number }}
                                </td>

                                <td style="padding: 12px;">
                                    {{ $bag->donor_name }}
                                </td>

                                <td style="padding: 12px; color: #dc2626; font-weight: 800;">
                                    {{ $bag->blood_type }}
                                </td>

                                <td style="padding: 12px;">
                                    {{ $bag->hospital_name ?? 'N/A' }}
                                </td>

                                <td style="padding: 12px;">
                                    {{ $bag->collection_date ? $bag->collection_date->format('d M Y') : 'N/A' }}
                                </td>

                                <td style="padding: 12px;">
                                    {{ $bag->expiry_date ? $bag->expiry_date->format('d M Y') : 'N/A' }}
                                </td>

                                <td style="padding: 12px;">
                                    @if($bag->status == 'Available')
                                        <span style="background-color: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 9999px; font-weight: 800; font-size: 12px;">
                                            Available
                                        </span>
                                    @elseif($bag->status == 'Used')
                                        <span style="background-color: #dbeafe; color: #1e40af; padding: 4px 10px; border-radius: 9999px; font-weight: 800; font-size: 12px;">
                                            Used
                                        </span>
                                    @else
                                        <span style="background-color: #fee2e2; color: #991b1b; padding: 4px 10px; border-radius: 9999px; font-weight: 800; font-size: 12px;">
                                            Expired
                                        </span>
                                    @endif
                                </td>

                                <td style="padding: 12px;">
                                    @if($bag->status == 'Available')
                                        <form action="{{ route('inventory.used', $bag->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" style="background-color: #2563eb; color: white; padding: 6px 12px; border-radius: 4px; border: none; font-weight: bold; cursor: pointer;">
                                                Mark Used
                                            </button>
                                        </form>
                                    @else
                                        <span style="color: #9ca3af; font-style: italic;">
                                            No action
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="padding: 20px; text-align: center; color: #9ca3af;">
                                    No blood bags found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>