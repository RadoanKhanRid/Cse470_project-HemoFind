<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-12 text-center">
                <h2 class="text-4xl font-extrabold text-slate-900 mb-4 animate__animated animate__fadeInDown">How would you like to help today?</h2>
                <p class="text-slate-600 text-lg">Welcome back, {{ Auth::user()->name }}. Your actions today can save a life.</p>
            </div>

            <!-- Ongoing Help Sessions -->
            @if($activeDonation || $incomingHelp)
                <div class="mb-12 space-y-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-2xl font-black text-slate-800 uppercase tracking-widest flex items-center gap-3">
                            <span class="w-3 h-3 bg-hemo-600 rounded-full animate-ping"></span>
                            Ongoing
                        </h3>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Case 1: You are the Donor (Outgoing Help) -->
                        @if($activeDonation)
                            <div class="bg-hemo-600 p-8 rounded-[3rem] text-white shadow-2xl flex flex-col md:flex-row justify-between items-center group overflow-hidden relative border-4 border-hemo-500">
                                <div class="relative z-10">
                                    <p class="text-xs font-bold uppercase opacity-80 mb-2 tracking-[0.2em]">Your Active Mission</p>
                                    <h4 class="text-3xl font-black mb-1">Helping {{ $activeDonation->bloodRequest->patient_name }}</h4>
                                    <p class="text-lg opacity-90 font-bold mb-2">{{ $activeDonation->bloodRequest->hospital_name }}</p>
                                    @if($activeDonation->distance)
                                        <div class="inline-flex items-center gap-2 bg-white/20 px-4 py-2 rounded-xl text-sm font-black border border-white/30 backdrop-blur-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            Distance: {{ $activeDonation->distance }} km away
                                        </div>
                                    @endif
                                </div>
                                <div class="flex flex-col items-center gap-3 relative z-10 mt-6 md:mt-0">
                                    <a href="{{ route('donation.track', $activeDonation) }}" class="bg-white text-hemo-600 px-10 py-4 rounded-full font-black text-base hover:scale-110 transition-transform shadow-xl">
                                        Open Tracking Map
                                    </a>
                                </div>
                                <div class="absolute -right-8 -bottom-8 text-white/10 group-hover:scale-110 transition-transform pointer-events-none">
                                    <svg class="w-56 h-56" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                                </div>
                            </div>
                        @endif

                        <!-- Case 2: You are the Receiver (Incoming Help) -->
                        @if($incomingHelp)
                            <div class="bg-blue-600 p-8 rounded-[3rem] text-white shadow-2xl flex flex-col md:flex-row justify-between items-center group overflow-hidden relative border-4 border-blue-500">
                                <div class="relative z-10 flex flex-col md:flex-row items-center gap-6">
                                    <div class="w-20 h-20 bg-white/20 rounded-[1.5rem] flex items-center justify-center text-white font-black text-3xl shadow-lg border border-white/30">
                                        {{ substr($incomingHelp->donor->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold uppercase opacity-80 mb-2 tracking-[0.2em]">Donor is Incoming</p>
                                        <h4 class="text-3xl font-black mb-1">{{ $incomingHelp->donor->name }} is coming!</h4>
                                        <div class="flex flex-wrap items-center gap-4 mt-2">
                                            <span class="bg-white/30 px-3 py-1 rounded-lg text-xs font-black uppercase tracking-wider">Group: {{ $incomingHelp->donor->blood_group }}</span>
                                            <span class="bg-white/30 px-3 py-1 rounded-lg text-xs font-black uppercase tracking-wider">From: {{ $incomingHelp->donor->area ?? 'Dhaka' }}</span>
                                            @if($incomingHelp->distance)
                                                <span class="bg-white text-blue-600 px-3 py-1 rounded-lg text-xs font-black uppercase tracking-wider shadow-sm">
                                                    {{ $incomingHelp->distance }} km away
                                                </span>
                                            @endif
                                            <a href="tel:{{ $incomingHelp->donor->mobile }}" class="flex items-center gap-2 text-sm font-black bg-white text-blue-600 px-4 py-1.5 rounded-full hover:scale-105 transition-transform shadow-lg">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 005.47 5.47l.773-1.548a1 1 0 011.06-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path></svg>
                                                Call Donor Now
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-col items-center gap-3 relative z-10 mt-6 md:mt-0">
                                    <a href="{{ route('donation.track', $incomingHelp) }}" class="bg-white text-blue-600 px-10 py-4 rounded-full font-black text-base hover:scale-110 transition-transform shadow-xl">
                                        Track Donor Live
                                    </a>
                                </div>
                                <div class="absolute -right-8 -bottom-8 text-white/10 group-hover:scale-110 transition-transform pointer-events-none">
                                    <svg class="w-56 h-56" fill="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Option 1: Find a Donor -->
                <a href="{{ route('search.index') }}" class="group relative overflow-hidden glass p-10 rounded-[3rem] shadow-2xl border-b-8 border-hemo-600 transition-all hover:scale-[1.02] hover:shadow-hemo-100 flex flex-col items-center text-center">
                    <div class="w-20 h-20 bg-hemo-100 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-hemo-600 transition-colors">
                        <svg class="w-10 h-10 text-hemo-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-3xl font-black text-slate-800 mb-4 tracking-tight">Find a Donor</h3>
                    <p class="text-slate-600 leading-relaxed mb-6">
                        In an emergency? Select a blood type and find compatible donors near your hospital in Dhaka.
                    </p>
                    <span class="inline-flex items-center gap-2 text-hemo-600 font-bold group-hover:gap-4 transition-all">
                        Select Blood Type 
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </span>
                </a>

                <!-- Option 2: Become a Donor / Check-in -->
                <a href="{{ route('donor.profile.show') }}" class="group relative overflow-hidden glass p-10 rounded-[3rem] shadow-2xl border-b-8 border-blue-500 transition-all hover:scale-[1.02] hover:shadow-blue-100 flex flex-col items-center text-center">
                    <div class="w-20 h-20 bg-blue-100 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-blue-500 transition-colors">
                        <svg class="w-10 h-10 text-blue-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-3xl font-black text-slate-800 mb-4 tracking-tight">Donor Check-in</h3>
                    <p class="text-slate-600 leading-relaxed mb-6">
                        Complete your donor profile and let others know you're ready to help.
                    </p>
                    <span class="inline-flex items-center gap-2 text-blue-600 font-bold group-hover:gap-4 transition-all">
                        Update Profile 
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </span>
                </a>
            </div>

            <!-- New Section: Help Feed -->
            <div class="mt-12 text-center">
                <a href="{{ route('requests.index') }}" class="inline-flex items-center gap-4 px-12 py-6 bg-slate-900 text-white rounded-full font-black text-xl hover:bg-slate-800 transition-all shadow-2xl hover:scale-105">
                    <svg class="w-8 h-8 text-hemo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    View Blood Help Feed
                </a>
            </div>

            <!-- Footer Stats (Small) -->
            <div class="mt-16 flex flex-wrap justify-center gap-8 opacity-60">
                <div class="text-center">
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-500">Your Group</p>
                    <p class="text-lg font-black text-slate-800">{{ Auth::user()->blood_group }}</p>
                </div>
                <div class="text-center border-l border-slate-200 pl-8">
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-500">Your Area</p>
                    <p class="text-lg font-black text-slate-800">{{ Auth::user()->area ?? 'Dhaka' }}</p>
                </div>
            </div>

        </div>
    </div>


</x-app-layout>