@extends('layouts.app')

@section('title', 'Messages')
@section('header_title', 'Partner Chat')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="max-w-6xl mx-auto bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden flex h-[calc(100vh-12rem)]">

    <!-- Sidebar: Partners List -->
    <div class="w-1/3 border-r border-gray-100 flex flex-col bg-gray-50/50">
        <div class="p-6 border-b border-gray-100 bg-white">
            <h2 class="text-xl font-extrabold text-gray-900 tracking-tight">Accountability Partners</h2>
            <p class="text-xs text-gray-400 font-bold mt-1 uppercase tracking-wider">Active Conversations</p>
        </div>

        <div class="flex-1 overflow-y-auto p-4 space-y-2">
            @forelse($activePartners as $partner)
                @php 
                    $isActive = $selectedPartner && $selectedPartner->id == $partner->id;
                @endphp
                <a href="{{ route('chat.index', $partner->id) }}"
                    class="flex items-center justify-between p-3.5 rounded-2xl transition duration-200 {{ $isActive ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100' : 'bg-white hover:bg-gray-100 border border-gray-50' }}">
                    <div class="flex items-center gap-4 min-w-0">
                        @if($partner->image)
                            <img src="{{ asset('storage/' . $partner->image) }}" class="h-11 w-11 rounded-full object-cover border-2 {{ $isActive ? 'border-white/50' : 'border-indigo-100' }}" alt="Profile">
                        @else
                            <div class="h-11 w-11 rounded-full flex items-center justify-center font-black text-sm uppercase {{ $isActive ? 'bg-white/20 text-white' : 'bg-indigo-50 text-indigo-700' }}">
                                {{ substr($partner->name, 0, 1) }}
                            </div>
                        @endif
                        <div class="min-w-0">
                            <h4 class="font-bold text-sm truncate {{ $isActive ? 'text-white' : 'text-gray-900' }}">{{ $partner->name }}</h4>
                            <p class="text-[10px] font-bold mt-0.5 uppercase {{ $isActive ? 'text-indigo-200' : 'text-indigo-600' }}">{{ $partner->level }}</p>
                        </div>
                    </div>
                </a>
            @empty
                <div class="text-center py-12 px-4"><p class="text-sm text-gray-400">No active partners found.</p></div>
            @endforelse
        </div>
    </div>

    <!-- Chat Area -->
    <div class="w-2/3 flex flex-col bg-white">
        @if($selectedPartner)
            <div class="p-5 border-b border-gray-100 flex items-center gap-4 bg-white shadow-sm z-10">
                <h3 class="font-extrabold text-gray-900 text-base">{{ $selectedPartner->name }}</h3>
            </div>

            <!-- Messages Stream -->
            <div class="flex-1 overflow-y-auto p-6 bg-gray-50/50 space-y-4" id="chat-stream-box">
                @forelse($messages as $msg)
                    @php $isMe = $msg->sender_id == auth()->id(); @endphp
                    <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[70%] rounded-2xl p-4 shadow-sm text-sm font-medium {{ $isMe ? 'bg-indigo-600 text-white rounded-tr-none' : 'bg-white text-gray-800 border border-gray-100 rounded-tl-none' }}">
                            <p>{{ $msg->message }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center p-12 opacity-60">Start a conversation!</div>
                @endforelse
            </div>

            <!-- Input Form -->
            <div class="p-4 border-t border-gray-100 bg-white">
                <form id="chat-form" action="{{ route('chat.send', $selectedPartner->id) }}" method="POST" class="flex gap-3">
                    @csrf
                    <input type="text" name="message" id="message-input" required autocomplete="off"
                        placeholder="Type a message..."
                        class="w-full bg-gray-50 px-5 py-3 rounded-xl text-sm border border-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <button type="submit" id="send-btn"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-black px-6 py-3 rounded-xl text-sm transition shadow-md">
                        Send
                    </button>
                </form>
            </div>
        @else
            <div class="flex-1 flex items-center justify-center">Select a partner to chat.</div>
        @endif
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // AJAX Setup for CSRF
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        // Scroll to bottom initially
        $('#chat-stream-box').scrollTop($('#chat-stream-box')[0].scrollHeight);

        // Form Submit Handler
        $('#chat-form').on('submit', function(e) {
            e.preventDefault();

            let form = $(this);
            let msg = $('#message-input').val();
            let btn = $('#send-btn');

            if(msg.trim() === "") return;

            btn.prop('disabled', true).text('Sending...');

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                success: function(response) {
                    // Append new message to UI
                    let html = `<div class="flex justify-end">
                                    <div class="max-w-[70%] rounded-2xl p-4 shadow-sm text-sm font-medium bg-indigo-600 text-white rounded-tr-none">
                                        <p>${msg}</p>
                                    </div>
                                </div>`;
                    $('#chat-stream-box').append(html);
                    $('#message-input').val('');
                    $('#chat-stream-box').scrollTop($('#chat-stream-box')[0].scrollHeight);
                },
                complete: function() {
                    btn.prop('disabled', false).text('Send');
                }
            });
        });
    });
</script>
@endsection