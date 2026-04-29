<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Active Blood Requests') }}
            </h2>
            <a href="{{ route('blood.requests.create') }}" class="bg-red-600 text-white px-4 py-2 rounded-md font-bold text-xs uppercase tracking-widest hover:bg-red-700 transition">
                + Post New Request
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white p-6 shadow-sm sm:rounded-lg border border-gray-100">
                <table style="width: 100%; border-collapse: collapse; background-color: white;">
                    <thead>
                        <tr style="background-color: #f3f4f6; text-align: left; border-bottom: 2px solid #e5e7eb;">
                            <th style="padding: 12px; color: #374151; font-weight: 700;">Patient Name</th>
                            <th style="padding: 12px; color: #374151; font-weight: 700;">Blood Group</th>
                            <th style="padding: 12px; color: #374151; font-weight: 700;">Hospital</th>
                            <th style="padding: 12px; color: #374151; font-weight: 700;">Priority</th>
                            <th style="padding: 12px; color: #374151; font-weight: 700; text-align: center;">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($requests as $request)
                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 12px;">
                                    <span style="font-weight: 600; color: #111827;">{{ $request->patient_name }}</span>
                                </td>
                                
                                <td style="padding: 12px; color: #dc2626; font-weight: 800; font-size: 1.1rem;">
                                    {{ $request->blood_type }}
                                </td>

                                <td style="padding: 12px; color: #4b5563; font-size: 14px;">
                                    {{ $request->hospital_name }}
                                </td>

                                <td style="padding: 12px;">
                                    @php
                                        $colors = [
                                            'red' => 'background-color: #fef2f2; color: #991b1b; border: 1px solid #fee2e2;',
                                            'yellow' => 'background-color: #fffbeb; color: #92400e; border: 1px solid #fef3c7;',
                                            'green' => 'background-color: #f0fdf4; color: #166534; border: 1px solid #dcfce7;'
                                        ];
                                        $style = $colors[$request->color] ?? 'background-color: #f3f4f6;';
                                    @endphp
                                    <span style="padding: 4px 10px; border-radius: 9999px; font-size: 11px; font-weight: 700; {{ $style }}">
                                        {{ strtoupper($request->priority) }}
                                    </span>
                                </td>

                                <td style="padding: 12px; text-align: center;">
                                    @if($request->lat && $request->lng)
                                        <a href="{{ route('blood.requests.nearby', $request->id) }}" 
                                           style="background-color: #4f46e5; color: white; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: bold; text-decoration: none; display: inline-block;">
                                            🔍 Find Nearby
                                        </a>
                                    @else
                                        <span style="color: #9ca3af; font-size: 12px; font-style: italic;">No GPS Location</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="padding: 40px; text-align: center; color: #9ca3af;">
                                    No active blood requests found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>