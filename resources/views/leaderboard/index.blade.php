@extends('layouts.app')

@section('title', 'Global Leaderboard')
@section('header_title', 'Rankings')

@section('content')

<div class="max-w-6xl mx-auto space-y-12">
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-end pt-8 pb-4">
        @foreach($leaderboard->take(3) as $index => $rankUser)
            @php 
                $isSelf = $rankUser->id == auth()->id();
                $podiumClasses = [
                    0 => 'from-amber-50/40 to-white border-amber-200 order-1 md:order-2 transform md:scale-105',
                    1 => 'border-gray-100 order-2 md:order-1',
                    2 => 'border-gray-100 order-3'
                ][$index];
            @endphp
            <div class="bg-white p-6 rounded-3xl border shadow-sm flex flex-col items-center text-center relative transition {{ $podiumClasses }} {{ $isSelf ? 'ring-4 ring-indigo-500' : '' }}">
                @if($index == 0)
                    <div class="absolute -top-7 left-1/2 transform -translate-x-1/2 bg-amber-400 text-white text-xs font-black px-4 py-1.5 rounded-full shadow flex items-center gap-1.5 ring-4 ring-white animate-bounce">👑 CHAMPION</div>
                @endif
                <div class="relative mb-4 mt-2">
                    @if($rankUser->image)
                        <img src="{{ asset('storage/' . $rankUser->image) }}" class="h-20 w-20 rounded-full object-cover border-4 border-indigo-50" alt="Avatar">
                    @else
                        <div class="h-20 w-20 bg-indigo-600 text-white rounded-full flex items-center justify-center font-black text-2xl uppercase shadow-sm">
                            {{ substr($rankUser->name, 0, 1) }}
                        </div>
                    @endif
                </div>
                <h3 class="text-lg font-black text-gray-900">{{ $rankUser->name }}</h3>
                <p class="text-xs text-indigo-600 font-bold bg-indigo-50 px-2.5 py-1 rounded-md mt-1">{{ $rankUser->level }}</p>
                <p class="text-base font-black text-gray-700 mt-2">{{ $rankUser->total_points }} pts</p>

                <div class="w-full mt-4">
                    @if($isSelf)
                        <span class="text-xs text-gray-400 font-bold">That's You!</span>
                    @elseif(in_array($rankUser->id, $connectedUserIds))
                        <span class="text-xs text-emerald-600 font-black bg-emerald-50 px-3 py-1.5 rounded-xl block">✓ Active Partner</span>
                    @elseif(in_array($rankUser->id, $pendingSentIds))
                        <span class="text-xs text-amber-600 font-bold bg-amber-50 px-3 py-1.5 rounded-xl block">Pending Sent</span>
                    @else
                        <form action="{{ route('partner.request', $rankUser->id) }}" method="POST">
                            @csrf
                            <button class="w-full bg-indigo-50 hover:bg-indigo-600 hover:text-white text-indigo-700 font-bold py-2 rounded-xl text-xs transition">Connect</button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    @if($leaderboard->count() > 3)
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="divide-y divide-gray-100">
            @foreach($leaderboard->slice(3) as $index => $boardUser)
                @php $isCurrentUser = $boardUser->id == auth()->id(); @endphp
                <div class="flex flex-col md:flex-row md:items-center justify-between p-5 md:px-8 transition-all {{ $isCurrentUser ? 'bg-indigo-600 text-white shadow-lg' : 'bg-white hover:bg-indigo-50/30' }}">
                    <div class="flex items-center gap-6 w-full md:w-1/2">
                        <div class="h-8 w-8 rounded-full flex items-center justify-center text-xs font-bold bg-gray-100 text-gray-500">{{ $index + 4 }}</div>
                        @if($boardUser->image)
                            <img src="{{ asset('storage/' . $boardUser->image) }}" class="h-10 w-10 rounded-full object-cover border" alt="Avatar">
                        @else
                            <div class="h-10 w-10 rounded-full flex items-center justify-center font-black text-sm uppercase {{ $isCurrentUser ? 'bg-white/20' : 'bg-indigo-50 text-indigo-700' }}">{{ substr($boardUser->name, 0, 1) }}</div>
                        @endif
                        <h4 class="font-extrabold text-base">{{ $boardUser->name }}</h4>
                    </div>
                    <div class="md:w-1/4 text-center font-bold text-xs uppercase tracking-wider">{{ $boardUser->level }}</div>
                    <div class="md:w-1/4 flex items-center justify-end gap-6">
                        <span class="font-black text-lg">{{ $boardUser->total_points }} pts</span>
                        @if(!$isCurrentUser)
                            @if(in_array($boardUser->id, $connectedUserIds))
                                <span class="text-xs text-emerald-500 font-bold">Partner</span>
                            @elseif(in_array($boardUser->id, $pendingSentIds))
                                <span class="text-xs text-amber-500 font-bold">Pending</span>
                            @else
                                <form action="{{ route('partner.request', $boardUser->id) }}" method="POST">
                                    @csrf
                                    <button class="bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white font-bold px-3 py-1.5 rounded-xl text-xs transition">Connect</button>
                                </form>
                            @endif
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection