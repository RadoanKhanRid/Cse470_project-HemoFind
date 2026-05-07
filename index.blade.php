<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-6">
                <h2 class="text-3xl font-black text-slate-800 tracking-tight">Blood Help Feed</h2>
                
                <div class="flex items-center gap-2 bg-slate-100 p-1 rounded-2xl border border-slate-200">
                    <button onclick="filterRequests('all')" class="filter-btn active px-6 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all">All</button>
                    <button onclick="filterRequests('Urgent')" class="filter-btn px-6 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all">Urgent</button>
                    <button onclick="filterRequests('Scheduled')" class="filter-btn px-6 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all">Scheduled</button>
                </div>

                <a href="{{ route('requests.create') }}" class="px-8 py-4 bg-hemo-600 text-white rounded-2xl font-black hover:bg-hemo-700 transition-all shadow-xl shadow-hemo-200 uppercase tracking-widest text-xs">
                    Post New Request
                </a>
            </div>

            <script>
                function filterRequests(urgency) {
                    const cards = document.querySelectorAll('.request-card');
                    const buttons = document.querySelectorAll('.filter-btn');
                    
                    buttons.forEach(btn => btn.classList.remove('bg-white', 'shadow-sm', 'active'));
                    event.target.classList.add('bg-white', 'shadow-sm', 'active');

                    cards.forEach(card => {
                        if (urgency === 'all' || card.dataset.urgency === urgency) {
                            card.style.display = 'block';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                }
            </script>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($requests as $request)
                    @php
                        $isAccepted = $request->latestDonation && in_array($request->latestDonation->status, ['accepted', 'on_the_way', 'arrived']);
                    @endphp
                    <div data-urgency="{{ $request->urgency }}" class="request-card {{ $isAccepted ? 'bg-green-50 border-green-200' : 'bg-white border-slate-100' }} p-8 rounded-[2.5rem] shadow-xl border-2 transition-all group relative overflow-hidden">
                        
                        @if($isAccepted)
                            <div class="absolute top-0 right-0 bg-green-600 text-white text-[10px] font-black px-4 py-1 rounded-bl-2xl uppercase tracking-widest shadow-lg">
                                Donor Accepted
                            </div>
                        @elseif($request->isFallbackActive())
                            <div class="absolute top-0 right-0 bg-orange-500 text-white text-[10px] font-black px-4 py-1 rounded-bl-2xl uppercase tracking-widest shadow-lg animate-pulse">
                                Smart Fallback: O- Invited
                            </div>
                        @endif

                        <!-- Header with Poster Info & Delete -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-slate-100 rounded-full flex items-center justify-center text-slate-400 text-xs font-black">
                                    {{ substr($request->user->name ?? 'U', 0, 1) }}
                                </div>
                                <div>
                                    <h4 class="text-sm font-black text-slate-800 leading-tight">{{ $request->user->name ?? 'User' }}</h4>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">{{ $request->created_at->diffForHumans() }}</p>
                                </div>
                            </div>

                            @if(Auth::id() === $request->user_id)
                                <form action="{{ route('requests.destroy', $request) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this blood request?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-red-500 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            @endif
                        </div>

                        <div class="flex items-center gap-4 mb-4 pt-4 border-t border-slate-50">
                            <div class="w-14 h-14 bg-hemo-100 rounded-2xl flex items-center justify-center text-hemo-600 font-black text-xl shadow-inner">
                                {{ $request->blood_group }}
                            </div>
                            <div>
                                <h3 class="font-black text-slate-900 text-lg leading-tight">{{ $request->patient_name }}</h3>
                                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">{{ $request->area }}</p>
                            </div>
                        </div>

                        <div class="space-y-3 mb-6">
                            <div class="flex items-center gap-2 text-sm text-slate-600 font-bold">
                                <svg class="w-4 h-4 text-hemo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-7h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                {{ $request->hospital_name }}
                            </div>
                            <div class="flex items-center gap-2 text-sm text-slate-600 font-bold">
                                <svg class="w-4 h-4 text-hemo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span class="{{ $request->urgency == 'Urgent' ? 'text-red-600' : '' }}">{{ $request->urgency }}</span>
                            </div>
                        </div>

                        @if(isset($request->distance))
                            <div class="mb-6 flex items-center gap-2 bg-blue-50 px-4 py-2 rounded-xl text-blue-700 text-xs font-black border border-blue-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                Distance: {{ $request->distance }} km away from you
                            </div>
                        @endif

                        @if($isAccepted)
                            <div class="w-full text-center py-4 bg-green-100 text-green-700 rounded-2xl font-black uppercase tracking-widest text-xs border border-green-200">
                                Donor Is On The Way
                            </div>
                        @else
                            <a href="tel:{{ $request->contact_number }}" class="block w-full text-center py-3 bg-slate-900 text-white rounded-xl font-bold hover:bg-slate-800 transition-colors mb-3">
                                Contact Now
                            </a>

                            @if(Auth::user()->role === 'donor')
                                <form action="{{ route('donation.accept', $request->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full text-center py-4 bg-hemo-600 text-white rounded-2xl font-black hover:bg-hemo-700 transition-all shadow-lg shadow-hemo-100 uppercase tracking-widest text-xs">
                                        Accept & Help
                                    </button>
                                </form>
                            @endif
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
