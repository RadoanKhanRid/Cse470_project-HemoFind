<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Hospital Admin Tools
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Search -->
            <form action="{{ route('hospital.index') }}" method="GET" class="mb-6 flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or blood group (e.g. O+)..." class="border-gray-300 rounded-md w-full">
                
                
    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded font-bold">
        Search
    </button>

    <a href="{{ route('blood.requests') }}" class="bg-red-600 text-white px-4 py-2 rounded font-bold flex items-center justify-center whitespace-nowrap hover:bg-red-700 transition">
        Blood Requests
    </a>
    <a href="{{ route('donations.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md font-bold text-xs uppercase tracking-widest hover:bg-indigo-700 transition shadow">
    + Add New Donor
</a>
    
</form>

            </form>

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <table style="width: 100%; border-collapse: collapse; background-color: white;">
    <thead>
        <tr style="background-color: #f3f4f6; text-align: left; border-bottom: 2px solid #e5e7eb;">
            <th style="padding: 12px; color: #374151; font-weight: 700;">Donor Name</th>
            <th style="padding: 12px; color: #374151; font-weight: 700;">Blood Group</th>
            <th style="padding: 12px; color: #374151; font-weight: 700;">Status</th>
            <th style="padding: 12px; color: #374151; font-weight: 700;">Arrival Time</th>
            <th style="padding: 12px; color: #374151; font-weight: 700;">Action</th>
        </tr>
    </thead>

    <tbody>
        @forelse($donations as $d)
            @php 
                $status = strtolower(trim($d->status)); 
            @endphp
            <tr style="border-bottom: 1px solid #f3f4f6;">
                <td style="padding: 12px;">{{ $d->donor_name }}</td>
                
                <td style="padding: 12px; color: #dc2626; font-weight: 800; font-size: 1.1rem;">
                    {{ $d->blood_type }}
                </td>

                <td style="padding: 12px;">
                    <span style="padding: 4px 8px; border-radius: 9999px; font-size: 12px; font-weight: 700; 
                        {{ $status == 'completed' ? 'background-color: #dcfce7; color: #166534;' : ($status == 'arrived' ? 'background-color: #dbeafe; color: #1e40af;' : 'background-color: #f3f4f6; color: #374151;') }}">
                        {{ strtoupper($status) }}
                    </span>
                </td>

                <td style="padding: 12px; font-size: 14px;">
                    @if($d->arrived_at)
                        <span style="font-weight: 600;">{{ \Carbon\Carbon::parse($d->arrived_at)->format('d M, Y') }}</span><br>
                        <span style="color: #9ca3af; font-size: 12px;">{{ \Carbon\Carbon::parse($d->arrived_at)->format('h:i A') }}</span>
                    @else
                        <span style="color: #d1d5db; font-style: italic;">Not recorded</span>
                    @endif
                </td>

                <td style="padding: 12px;">
                    @if($status == 'pending')
                        <form action="{{ route('checkin.donation', $d->id) }}" method="POST">
                            @csrf
                            <button type="submit" style="background-color: #2563eb; color: white; padding: 6px 12px; border-radius: 4px; border: none; font-weight: bold; cursor: pointer;">
                                Mark Arrived
                            </button>
                        </form>
                    @elseif($status == 'arrived')
                        <form action="{{ route('verify.donation', $d->id) }}" method="POST">
                            @csrf
                            <button type="submit" style="background-color: #16a34a; color: white; padding: 6px 12px; border-radius: 4px; border: none; font-weight: bold; cursor: pointer;">
                                Mark Completed
                            </button>
                        </form>
                    @elseif($status == 'completed')
                        <a href="{{ route('download.certificate', $d->id) }}" style="color: #dc2626; font-weight: bold; text-decoration: underline; font-size: 14px;">
                            Download PDF
                        </a>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" style="padding: 20px; text-align: center; color: #9ca3af;">No donors found.</td>
            </tr>
        @endforelse
    </tbody>
</table>
            </div>
        </div>
    </div>
</x-app-layout>