<x-app-layout>

    {{-- ─── Header ─────────────────────────────────────────── --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Hospital Admin Tools
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- ─── Search & Action Bar ─────────────────────── --}}
            <form action="{{ route('hospital.index') }}" method="GET"
                  class="mb-6 flex gap-2">

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search by name or blood group (e.g. O+)..."
                    class="border-gray-300 rounded-md w-full"
                >

                <button type="submit"
                        class="bg-blue-500 text-white px-4 py-2 rounded font-bold">
                    Search
                </button>

                <a href="{{ route('blood.requests') }}"
                   class="bg-red-600 text-white px-4 py-2 rounded font-bold flex items-center justify-center whitespace-nowrap hover:bg-red-700 transition">
                    Blood Requests
                </a>

                <a href="{{ route('inventory.index') }}"
                    class="bg-green-600 text-white px-4 py-2 rounded font-bold flex items-center justify-center whitespace-nowrap hover:bg-green-700 transition"
                    style="background-color:#16a34a; color:white;">
                        Blood Inventory
                </a>

                <a href="{{ route('donations.create') }}"
                   class="bg-indigo-600 text-white px-4 py-2 rounded-md font-bold text-xs uppercase tracking-widest hover:bg-indigo-700 transition shadow">
                    + Add New Donor
                </a>

            </form>

            {{-- ─── Donor Table ──────────────────────────────── --}}
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <table style="width:100%; border-collapse:collapse; background-color:white;">
                    <thead>
                        <tr style="background-color:#f3f4f6; text-align:left; border-bottom:2px solid #e5e7eb;">
                            <th style="padding:12px; color:#374151; font-weight:700;">Donor Name</th>
                            <th style="padding:12px; color:#374151; font-weight:700;">Blood Group</th>
                            <th style="padding:12px; color:#374151; font-weight:700;">Status</th>
                            <th style="padding:12px; color:#374151; font-weight:700;">Arrival Time</th>
                            <th style="padding:12px; color:#374151; font-weight:700;">Cooldown Timer</th>
                            <th style="padding:12px; color:#374151; font-weight:700;">Trust</th>
                            <th style="padding:12px; color:#374151; font-weight:700;">Hospital Badge</th>
                            <th style="padding:12px; color:#374151; font-weight:700;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($donations as $d)
                            @php
                                $status = strtolower(trim($d->status));
                            @endphp
                            <tr style="border-bottom:1px solid #f3f4f6;">

                                {{-- Donor Name --}}
                                <td style="padding:12px;">{{ $d->donor_name }}</td>

                                {{-- Blood Group --}}
                                <td style="padding:12px; color:#dc2626; font-weight:800; font-size:1.1rem;">
                                    {{ $d->blood_type }}
                                </td>

                                {{-- Status Badge --}}
                                <td style="padding:12px;">
                                    @php
                                        $statusStyle = match($status) {
                                            'completed' => 'background-color:#dcfce7; color:#166534;',
                                            'arrived'   => 'background-color:#dbeafe; color:#1e40af;',
                                            default     => 'background-color:#f3f4f6; color:#374151;',
                                        };
                                    @endphp
                                    <span style="padding:4px 8px; border-radius:9999px; font-size:12px; font-weight:700; {{ $statusStyle }}">
                                        {{ strtoupper($status) }}
                                    </span>
                                </td>

                                {{-- Arrival Time --}}
                                <td style="padding:12px; font-size:14px;">
                                    @if($d->arrived_at)
                                        <span style="font-weight:600;">
                                            {{ \Carbon\Carbon::parse($d->arrived_at)->format('d M, Y') }}
                                        </span>
                                        <br>
                                        <span style="color:#9ca3af; font-size:12px;">
                                            {{ \Carbon\Carbon::parse($d->arrived_at)->format('h:i A') }}
                                        </span>
                                    @else
                                        <span style="color:#d1d5db; font-style:italic;">Not recorded</span>
                                    @endif
                                </td>

                                {{-- Cooldown Timer --}}
                                <td style="padding:12px; font-size:14px;">
                                    @if($d->cooldown_until && \Carbon\Carbon::parse($d->cooldown_until)->isFuture())
                                        <span
                                            class="cooldown-timer"
                                            data-time="{{ \Carbon\Carbon::parse($d->cooldown_until)->toIso8601String() }}"
                                            style="color:#dc2626; font-weight:800;"
                                        >Loading...</span>
                                    @elseif($d->cooldown_until)
                                        <span style="color:#16a34a; font-weight:800;">Available</span>
                                    @else
                                        <span style="color:#9ca3af; font-style:italic;">No Cooldown</span>
                                    @endif
                                </td>

                                {{-- Trust Tier --}}
                                <td style="padding:12px;">
                                    @php
                                        $trustLabel = $d->trust_tier ?? 'New Donor';
                                        $trustStyle = match($trustLabel) {
                                            'Diamond'  => 'background-color:#e0f2fe; color:#0369a1;',
                                            'Platinum' => 'background-color:#f3e8ff; color:#6b21a8;',
                                            'Gold'     => 'background-color:#fef3c7; color:#92400e;',
                                            'Silver'   => 'background-color:#e5e7eb; color:#374151;',
                                            'Bronze'   => 'background-color:#fed7aa; color:#9a3412;',
                                            default    => 'background-color:#f3f4f6; color:#6b7280;',
                                        };
                                    @endphp
                                    <span style="padding:4px 10px; border-radius:9999px; font-size:12px; font-weight:800; {{ $trustStyle }}">
                                        {{ $trustLabel }}
                                    </span>
                                    <br>
                                    <span style="font-size:12px; color:#6b7280;">
                                        Score: {{ $d->trust_score ?? 0 }}
                                    </span>
                                </td>

                                {{-- Hospital Badge --}}
                                <td style="padding:12px;">
                                    @if($d->hospitals_visited_badge)
                                        <span style="padding:4px 10px; border-radius:9999px; font-size:12px; font-weight:800; background-color:#dbeafe; color:#1e40af;">
                                            🏥 {{ $d->hospitals_visited_badge }}
                                        </span>
                                        <br>
                                        <span style="font-size:12px; color:#6b7280;">
                                            {{ $d->hospitals_visited_count }} hospital(s)
                                        </span>
                                    @else
                                        <span style="color:#9ca3af; font-style:italic;">No badge</span>
                                    @endif
                                </td>

                                {{-- Action --}}
                                <td style="padding:12px;">
                                    @if($status === 'pending')
                                        <form action="{{ route('checkin.donation', $d->id) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                    style="background-color:#2563eb; color:white; padding:6px 12px; border-radius:4px; border:none; font-weight:bold; cursor:pointer;">
                                                Mark Arrived
                                            </button>
                                        </form>
                                    @elseif($status === 'arrived')
                                        <form action="{{ route('verify.donation', $d->id) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                    style="background-color:#16a34a; color:white; padding:6px 12px; border-radius:4px; border:none; font-weight:bold; cursor:pointer;">
                                                Mark Completed
                                            </button>
                                        </form>
                                    @elseif($status === 'completed')
                                        <a href="{{ route('download.certificate', $d->id) }}"
                                           style="color:#dc2626; font-weight:bold; text-decoration:underline; font-size:14px;">
                                            Download PDF
                                        </a>
                                    @endif
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="8"
                                    style="padding:20px; text-align:center; color:#45597c;">
                                    No donors found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    {{-- ─── Live Cooldown Countdown Script ─────────────────────── --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function updateCooldownTimers() {
                document.querySelectorAll('.cooldown-timer').forEach(function (timer) {
                    const endTime  = new Date(timer.dataset.time).getTime();
                    const now      = Date.now();
                    const distance = endTime - now;

                    if (distance <= 0) {
                        timer.textContent = 'Available';
                        timer.style.color = '#16a34a';
                        return;
                    }

                    const days    = Math.floor(distance / (1000 * 60 * 60 * 24));
                    const hours   = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    timer.textContent = `${days}d : ${hours}h : ${minutes}m : ${seconds}s`;
                });
            }

            updateCooldownTimers();
            setInterval(updateCooldownTimers, 1000);
        });
    </script>

</x-app-layout>