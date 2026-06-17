@extends('layouts.app')

@section('title', 'Community & Accountability')
@section('header_title', 'Accountability Partner')

@section('content')
<div class="max-w-6xl mx-auto space-y-12">
    
    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-center gap-6">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Community & Accountability</h1>
            <p class="text-gray-500 mt-2 text-lg">Connect with like-minded brothers/sisters and grow together.</p>
        </div>
        <div class="bg-indigo-50 px-8 py-4 rounded-2xl border border-indigo-100 text-center shadow-inner">
            <span class="block text-sm text-indigo-600 font-bold uppercase tracking-wider">Your Total Points</span>
            <span class="text-4xl font-black text-indigo-700">{{ auth()->user()->total_points ?? 0 }}</span>
        </div>
    </div>

    @if($pendingRequests->count() > 0)
    <div class="bg-amber-50 p-6 rounded-3xl border border-amber-200 shadow-sm">
        <h2 class="text-xl font-bold text-amber-900 mb-6 flex items-center gap-2">
            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>
            Pending Partner Requests ({{ $pendingRequests->count() }})
        </h2>
        <div class="grid md:grid-cols-2 gap-4">
            @foreach($pendingRequests as $req)
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-amber-100 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    @if($req->user->image)
                        <img src="{{ asset('storage/' . $req->user->image) }}" class="h-12 w-12 rounded-full object-cover border-2 border-amber-200 shadow-sm" alt="Profile">
                    @else
                        <div class="h-12 w-12 bg-amber-100 text-amber-700 rounded-full flex items-center justify-center font-black text-xl uppercase shadow-sm">
                            {{ substr($req->user->name, 0, 1) }}
                        </div>
                    @endif
                    <div>
                        <h4 class="font-bold text-gray-900">{{ $req->user->name }}</h4>
                        <p class="text-xs text-gray-500 font-bold mt-1">Level: <span class="text-indigo-600">{{ $req->user->level }}</span></p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <form action="{{ route('partner.accept', $req->id) }}" method="POST">
                        @csrf
                        <button class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded-xl text-sm font-bold transition shadow-md">Accept</button>
                    </form>
                    <form action="{{ route('partner.reject', $req->id) }}" method="POST">
                        @csrf
                        <button class="bg-red-50 hover:bg-red-100 text-red-600 px-4 py-2 rounded-xl text-sm font-bold transition">Reject</button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($activePartners->count() > 0)
    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 space-y-6">
        <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
            <span class="w-2 h-7 bg-emerald-500 rounded-full"></span> My Active Partners & Today's Progress
        </h2>
        <div class="grid md:grid-cols-2 gap-6">
            @foreach($activePartners as $partner)
            <div class="border border-gray-100 bg-gray-50/50 p-6 rounded-2xl space-y-4">
                <div class="flex justify-between items-start">
                    <div class="flex items-center gap-3">
                        @if($partner->image)
                            <img src="{{ asset('storage/' . $partner->image) }}" class="h-12 w-12 rounded-full object-cover border-2 border-indigo-200 shadow-sm" alt="Profile">
                        @else
                            <div class="h-12 w-12 bg-indigo-600 text-white rounded-full flex items-center justify-center font-bold uppercase shadow-sm">
                                {{ substr($partner->name, 0, 1) }}
                            </div>
                        @endif
                        <div>
                            <h4 class="font-bold text-gray-900">{{ $partner->name }}</h4>
                            <p class="text-xs text-gray-500 font-semibold">{{ $partner->level }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-sm font-bold text-indigo-600">{{ $partner->total_points }} pts</span>
                    </div>
                </div>
                
                <div class="bg-white p-4 rounded-xl border border-gray-100">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-3">Today's Prayers Status</span>
                    <div class="grid grid-cols-5 gap-2 text-center">
                        @foreach(['fajr', 'dhuhr', 'asr', 'maghrib', 'isha'] as $prayerName)
                            @php 
                                $status = $partner->today_tracker ? $partner->today_tracker->$prayerName : 'missed';
                                $colorClass = 'bg-gray-100 text-gray-400';
                                if($status === 'jamaah_mosque') $colorClass = 'bg-emerald-500 text-white shadow-sm';
                                elseif($status === 'jamaah_home') $colorClass = 'bg-teal-500 text-white shadow-sm';
                                elseif($status === 'alone') $colorClass = 'bg-blue-500 text-white shadow-sm';
                                elseif($status === 'qada') $colorClass = 'bg-orange-400 text-white shadow-sm';
                            @endphp
                            <div class="p-2 rounded-lg text-[10px] font-black uppercase {{ $colorClass }}">
                                {{ substr($prayerName, 0, 3) }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="space-y-6">
        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
            <h2 class="text-2xl font-extrabold text-gray-900 flex items-center gap-2">
                <span class="w-2 h-7 bg-indigo-600 rounded-full"></span> Find an Accountability Partner
            </h2>
            <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-4 py-1.5 rounded-full border border-indigo-100 uppercase tracking-wider">Suggestions</span>
        </div>
        
        @if($suggestedPartners->count() > 0)
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($suggestedPartners as $partner)
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex flex-col items-center text-center transition duration-300 hover:shadow-md hover:border-indigo-100">
                @if($partner->image)
                    <img src="{{ asset('storage/' . $partner->image) }}" class="h-20 w-20 rounded-full object-cover border-4 border-indigo-50 shadow-md mb-4" alt="Profile">
                @else
                    <div class="h-20 w-20 bg-gradient-to-tr from-indigo-500 to-purple-500 text-white rounded-full flex items-center justify-center font-black text-3xl uppercase mb-4 shadow-inner">
                        {{ substr($partner->name, 0, 1) }}
                    </div>
                @endif
                <h3 class="text-lg font-bold text-gray-900 tracking-tight">{{ $partner->name }}</h3>
                <p class="text-xs font-black text-indigo-600 mt-1 mb-2 bg-indigo-50 px-3 py-1 rounded-md">{{ $partner->level }}</p>
                <p class="text-xs text-gray-400 font-bold mb-6">{{ $partner->total_points }} Total Points</p>
                
                <form action="{{ route('partner.request', $partner->id) }}" method="POST" class="w-full">
                    @csrf
                    <button class="w-full bg-gray-50 border border-gray-200 hover:bg-indigo-600 hover:border-indigo-600 hover:text-white text-indigo-700 font-bold py-3 rounded-xl transition text-sm shadow-sm">
                        Send Request
                    </button>
                </form>
            </div>
            @endforeach
        </div>
        @else
        <div class="bg-white p-12 rounded-3xl border border-gray-100 text-center flex flex-col items-center justify-center">
            <div class="h-16 w-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-1">No New Suggestions</h3>
            <p class="text-gray-500 font-medium text-sm">There are no new partners to suggest right now. Keep leveling up!</p>
        </div>
        @endif
    </div>

    <div class="space-y-6">
        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
            <h2 class="text-2xl font-extrabold text-gray-900 flex items-center gap-2">
                <svg class="w-7 h-7 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path></svg>
                Global Leaderboard
            </h2>
            <span class="text-xs font-black text-emerald-700 bg-emerald-50 px-4 py-1.5 rounded-full border border-emerald-200 uppercase tracking-wider">Top 10 Competitors</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-end pt-8 pb-4">
            
            @if($leaderboard->count() > 1)
                @php $partner2 = $leaderboard[1]; $isUser2 = $partner2->id == auth()->id(); @endphp
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex flex-col items-center text-center relative order-2 md:order-1 transition duration-300 hover:shadow-md {{ $isUser2 ? 'ring-2 ring-indigo-500 bg-indigo-50/10' : '' }}">
                    <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 bg-slate-400 text-white text-[10px] font-black px-3 py-1 rounded-full shadow-sm tracking-wider uppercase">2nd Place</div>
                    <div class="relative mb-4 mt-2">
                        @if($partner2->image)
                            <img src="{{ asset('storage/' . $partner2->image) }}" class="h-20 w-20 rounded-full object-cover border-4 border-slate-200 shadow-md" alt="Profile">
                        @else
                            <div class="h-20 w-20 bg-slate-100 text-slate-700 rounded-full flex items-center justify-center font-black text-2xl uppercase shadow-sm border-4 border-slate-200">
                                {{ substr($partner2->name, 0, 1) }}
                            </div>
                        @endif
                        <span class="absolute -bottom-2 -right-2 bg-slate-400 text-white text-xs font-black h-6 w-6 rounded-full flex items-center justify-center shadow ring-2 ring-white">2</span>
                    </div>
                    <h3 class="text-lg font-extrabold text-gray-900 tracking-tight">{{ $partner2->name }}</h3>
                    <p class="text-[10px] font-black text-slate-600 bg-slate-50 px-2.5 py-1 rounded-md mt-1 uppercase tracking-wider">{{ $partner2->level }}</p>
                    <p class="text-sm font-black text-indigo-600 mt-3">{{ $partner2->total_points }} <span class="text-[10px] text-gray-400 font-bold uppercase">pts</span></p>
                </div>
            @endif

            @if($leaderboard->count() > 0)
                @php $partner1 = $leaderboard[0]; $isUser1 = $partner1->id == auth()->id(); @endphp
                <div class="bg-gradient-to-b from-amber-50/40 to-white p-8 rounded-3xl border-2 border-amber-200 shadow-md flex flex-col items-center text-center relative order-1 md:order-2 transform md:scale-105 transition duration-300 hover:shadow-lg {{ $isUser1 ? 'ring-4 ring-indigo-500' : '' }}">
                    <div class="absolute -top-7 left-1/2 transform -translate-x-1/2 bg-amber-400 text-white text-xs font-black px-4 py-1.5 rounded-full shadow flex items-center gap-1.5 ring-4 ring-white animate-bounce">
                        👑 <span>CHAMPION</span>
                    </div>
                    <div class="relative mb-4 mt-2">
                        @if($partner1->image)
                            <img src="{{ asset('storage/' . $partner1->image) }}" class="h-24 w-24 rounded-full object-cover border-4 border-amber-300 shadow-lg" alt="Profile">
                        @else
                            <div class="h-24 w-24 bg-amber-100 text-amber-700 rounded-full flex items-center justify-center font-black text-3xl uppercase shadow-md border-4 border-amber-300">
                                {{ substr($partner1->name, 0, 1) }}
                            </div>
                        @endif
                        <span class="absolute -bottom-2 -right-2 bg-amber-500 text-white text-sm font-black h-7 w-7 rounded-full flex items-center justify-center shadow ring-2 ring-white">1</span>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 tracking-tight">{{ $partner1->name }}</h3>
                    <p class="text-[10px] font-black text-amber-800 bg-amber-100/60 px-3 py-1 rounded-md mt-1 uppercase tracking-wider">{{ $partner1->level }}</p>
                    <p class="text-xl font-black text-indigo-700 mt-4">{{ $partner1->total_points }} <span class="text-xs text-gray-400 font-bold uppercase">pts</span></p>
                </div>
            @endif

            @if($leaderboard->count() > 2)
                @php $partner3 = $leaderboard[2]; $isUser3 = $partner3->id == auth()->id(); @endphp
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex flex-col items-center text-center relative order-3 transition duration-300 hover:shadow-md {{ $isUser3 ? 'ring-2 ring-indigo-500 bg-indigo-50/10' : '' }}">
                    <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 bg-amber-700 text-white text-[10px] font-black px-3 py-1 rounded-full shadow-sm tracking-wider uppercase">3rd Place</div>
                    <div class="relative mb-4 mt-2">
                        @if($partner3->image)
                            <img src="{{ asset('storage/' . $partner3->image) }}" class="h-20 w-20 rounded-full object-cover border-4 border-amber-600/20 shadow-md" alt="Profile">
                        @else
                            <div class="h-20 w-20 bg-amber-50 text-amber-800 rounded-full flex items-center justify-center font-black text-2xl uppercase shadow-sm border-4 border-amber-600/20">
                                {{ substr($partner3->name, 0, 1) }}
                            </div>
                        @endif
                        <span class="absolute -bottom-2 -right-2 bg-amber-700 text-white text-xs font-black h-6 w-6 rounded-full flex items-center justify-center shadow ring-2 ring-white">3</span>
                    </div>
                    <h3 class="text-lg font-extrabold text-gray-900 tracking-tight">{{ $partner3->name }}</h3>
                    <p class="text-[10px] font-black text-amber-800 bg-amber-50 px-2.5 py-1 rounded-md mt-1 uppercase tracking-wider">{{ $partner3->level }}</p>
                    <p class="text-sm font-black text-indigo-600 mt-3">{{ $partner3->total_points }} <span class="text-[10px] text-gray-400 font-bold uppercase">pts</span></p>
                </div>
            @endif

        </div>

        @if($leaderboard->count() > 3)
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mt-6">
            <div class="hidden md:flex items-center justify-between bg-gray-50/75 px-8 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100">
                <div class="flex items-center gap-6 w-1/2">
                    <span class="w-10 text-center">Rank</span>
                    <span>Worshiper</span>
                </div>
                <div class="w-1/4 text-center">Spiritual Rank</div>
                <div class="w-1/4 text-right">Total Score</div>
            </div>

            <div class="divide-y divide-gray-100">
                @foreach($leaderboard->slice(3) as $index => $boardUser)
                    @php
                        $actualRank = $index + 4;
                        $isCurrentUser = $boardUser->id == auth()->id();
                        $rowStyle = $isCurrentUser ? 'bg-indigo-600 text-white shadow-lg' : 'bg-white hover:bg-indigo-50/30 text-gray-900';
                    @endphp

                    <div class="flex flex-col md:flex-row md:items-center justify-between p-5 md:px-8 transition-all duration-200 {{ $rowStyle }}">
                        
                        <div class="flex items-center gap-6 w-full md:w-1/2">
                            <div class="h-8 w-8 rounded-full flex items-center justify-center text-xs font-bold shrink-0 bg-gray-100 text-gray-500">
                                {{ $actualRank }}
                            </div>

                            <div class="shrink-0">
                                @if($boardUser->image)
                                    <img src="{{ asset('storage/' . $boardUser->image) }}" class="h-11 w-11 rounded-full object-cover border-2 {{ $isCurrentUser ? 'border-white/50' : 'border-indigo-100' }} shadow-sm" alt="Avatar">
                                @else
                                    <div class="h-11 w-11 rounded-full flex items-center justify-center font-black text-sm uppercase shadow-inner {{ $isCurrentUser ? 'bg-white/20 text-white' : 'bg-indigo-50 text-indigo-700' }}">
                                        {{ substr($boardUser->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>

                            <div>
                                <h4 class="font-extrabold text-base tracking-tight flex items-center gap-2">
                                    {{ $boardUser->name }}
                                    @if($isCurrentUser) 
                                        <span class="text-[10px] bg-white/20 px-2.5 py-0.5 rounded-full font-semibold uppercase tracking-wider">You</span> 
                                    @endif
                                </h4>
                                <p class="text-xs md:hidden font-medium mt-0.5 {{ $isCurrentUser ? 'text-indigo-200' : 'text-gray-400' }}">
                                    {{ $boardUser->level }} • {{ $boardUser->total_points }} pts
                                </p>
                            </div>
                        </div>

                        <div class="hidden md:block w-1/4 text-center font-bold text-sm {{ $isCurrentUser ? 'text-white' : 'text-indigo-600' }}">
                            <span class="{{ $isCurrentUser ? 'bg-white/10' : 'bg-indigo-50' }} px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-wide">
                                {{ $boardUser->level }}
                            </span>
                        </div>

                        <div class="hidden md:block w-1/4 text-right">
                            <span class="font-black text-xl tracking-tight">{{ $boardUser->total_points }}</span>
                            <span class="text-[10px] uppercase font-bold tracking-wider ml-1 {{ $isCurrentUser ? 'text-indigo-200' : 'text-gray-400' }}">pts</span>
                        </div>

                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    
</div>
@endsection