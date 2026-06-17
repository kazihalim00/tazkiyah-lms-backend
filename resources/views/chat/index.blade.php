@extends('layouts.app')

@section('title', 'Messages')
@section('header_title', 'Partner Chat')

@section('content')
    <div class="max-w-6xl mx-auto bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden flex h-[calc(100vh-12rem)]">

        <div class="w-1/3 border-r border-gray-100 flex flex-col bg-gray-50/50">
            <div class="p-6 border-b border-gray-100 bg-white">
                <h2 class="text-xl font-extrabold text-gray-900 tracking-tight">Accountability Partners</h2>
                <p class="text-xs text-gray-400 font-bold mt-1 uppercase tracking-wider">Active Conversations</p>
            </div>

            <div class="flex-1 overflow-y-auto p-4 space-y-2">
                @forelse($activePartners as $partner)
                    @php 
                        $isActive = $selectedPartner && $selectedPartner->id == $partner->id;
                        
                        // Dynamically count unread messages from this specific partner
                        $partnerUnreadCount = \App\Models\PartnerMessage::where('sender_id', $partner->id)
                            ->where('receiver_id', auth()->id())
                            ->where('is_read', false)
                            ->count();
                    @endphp
                    <a href="{{ route('chat.index', $partner->id) }}"
                        class="flex items-center justify-between p-3.5 rounded-2xl transition duration-200 {{ $isActive ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100' : 'bg-white hover:bg-gray-100 border border-gray-50' }}">

                        <div class="flex items-center gap-4 min-w-0">
                            @if($partner->image)
                                <img src="{{ asset('storage/' . $partner->image) }}"
                                    class="h-11 w-11 rounded-full object-cover border-2 {{ $isActive ? 'border-white/50' : 'border-indigo-100' }}"
                                    alt="Profile">
                            @else
                                <div class="h-11 w-11 rounded-full flex items-center justify-center font-black text-sm uppercase {{ $isActive ? 'bg-white/20 text-white' : 'bg-indigo-50 text-indigo-700' }}">
                                    {{ substr($partner->name, 0, 1) }}
                                </div>
                            @endif

                            <div class="min-w-0">
                                <h4 class="font-bold text-sm truncate {{ $isActive ? 'text-white' : 'text-gray-900' }}">
                                    {{ $partner->name }}</h4>
                                <p class="text-[10px] font-bold mt-0.5 uppercase {{ $isActive ? 'text-indigo-200' : 'text-indigo-600' }}">
                                    {{ $partner->level }}</p>
                            </div>
                        </div>

                        @if($partnerUnreadCount > 0 && !$isActive)
                            <span class="bg-indigo-600 text-white text-[10px] font-black h-5 w-5 rounded-full flex items-center justify-center shadow-sm shrink-0 ml-2 animate-pulse">
                                {{ $partnerUnreadCount }}
                            </span>
                        @endif
                    </a>
                @empty
                    <div class="text-center py-12 px-4">
                        <p class="text-sm text-gray-400 font-medium">No active partners found. Connect with someone from the
                            leaderboard to start cheering each other up!</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="w-2/3 flex flex-col bg-white">
            @if($selectedPartner)
                <div class="p-5 border-b border-gray-100 flex items-center gap-4 bg-white shadow-sm z-10">
                    @if($selectedPartner->image)
                        <img src="{{ asset('storage/' . $selectedPartner->image) }}"
                            class="h-10 w-10 rounded-full object-cover border" alt="Avatar">
                    @else
                        <div class="h-10 w-10 bg-indigo-50 text-indigo-700 rounded-full flex items-center justify-center font-black text-sm uppercase">
                            {{ substr($selectedPartner->name, 0, 1) }}</div>
                    @endif
                    <div>
                        <h3 class="font-extrabold text-gray-900 text-base tracking-tight">{{ $selectedPartner->name }}</h3>
                        <p class="text-[10px] font-black text-emerald-600 uppercase tracking-wide">Connected Partner</p>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto p-6 bg-gray-50/50 space-y-4" id="chat-stream-box">
                    @forelse($messages as $msg)
                        @php $isMe = $msg->sender_id == auth()->id(); @endphp
                        <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[70%] rounded-2xl p-4 shadow-sm text-sm font-medium leading-relaxed {{ $isMe ? 'bg-indigo-600 text-white rounded-tr-none' : 'bg-white text-gray-800 border border-gray-100 rounded-tl-none' }}">
                                <p>{{ $msg->message }}</p>
                                <span class="block text-[9px] font-bold text-right mt-1.5 uppercase {{ $isMe ? 'text-indigo-200' : 'text-gray-400' }}">
                                    {{ $msg->created_at->format('h:i A') }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="h-full flex flex-col items-center justify-center text-center p-12 opacity-60">
                            <span class="text-3xl mb-2">💬</span>
                            <h4 class="font-bold text-gray-700">Start of a blessed conversation</h4>
                            <p class="text-xs text-gray-400 mt-1">Send a message to remind your partner about today's Adhkar or Quran targets!</p>
                        </div>
                    @endforelse
                </div>

                <div class="p-4 border-t border-gray-100 bg-white">
                    <form action="{{ route('chat.send', $selectedPartner->id) }}" method="POST" class="flex gap-3">
                        @csrf
                        <input type="text" name="message" required autocomplete="off"
                            placeholder="Type a message to motivate your partner..."
                            class="w-full bg-gray-50 px-5 py-3 rounded-xl text-sm border border-gray-100 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-gray-700 font-medium">
                        <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-black px-6 py-3 rounded-xl text-sm transition shadow-md shrink-0">
                            Send
                        </button>
                    </form>
                </div>
            @else
                <div class="flex-1 flex flex-col items-center justify-center text-center p-12 bg-gray-50/20">
                    <div class="h-16 w-16 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center text-2xl shadow-inner mb-4">✉️</div>
                    <h3 class="text-lg font-black text-gray-900">Your Inbox</h3>
                    <p class="text-gray-400 font-medium text-sm mt-1 max-w-sm">Select an active accountability partner from the left menu sidebar to start chatting and tracking goals together!</p>
                </div>
            @endif
        </div>

    </div>

    <script>
        const streamBox = document.getElementById('chat-stream-box');
        if (streamBox) {
            streamBox.scrollTop = streamBox.scrollHeight;
        }
    </script>
@endsection