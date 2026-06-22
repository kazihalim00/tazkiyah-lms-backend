@extends('layouts.app')

@section('title', 'Messages')
@section('header_title', 'Partner Chat')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div
        class="max-w-6xl mx-auto bg-white md:rounded-3xl shadow-sm border-x md:border border-gray-100 overflow-hidden flex h-[calc(100vh-8rem)] md:h-[calc(100vh-10rem)] relative">

        <div
            class="w-full md:w-1/3 lg:w-[30%] border-r border-gray-100 flex-col bg-gray-50/50 {{ $selectedPartner ? 'hidden md:flex' : 'flex' }}">

            <div class="p-5 md:p-6 border-b border-gray-100 bg-white">
                <h2 class="text-xl font-black text-gray-900 tracking-tight">Chats</h2>
                <p class="text-xs text-gray-400 font-bold mt-1 uppercase tracking-wider">Accountability Partners</p>
            </div>

            <div class="flex-1 overflow-y-auto p-3 space-y-1 custom-scrollbar">
                @forelse($activePartners as $partner)
                    @php $isActive = $selectedPartner && $selectedPartner->id == $partner->id; @endphp

                    <a href="{{ route('chat.index', $partner->id) }}"
                        class="flex items-center justify-between p-3 rounded-2xl transition duration-200 {{ $isActive ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200' : 'bg-transparent hover:bg-white hover:shadow-sm border border-transparent hover:border-gray-100' }}">

                        <div class="flex items-center gap-3.5 min-w-0">
                            @if($partner->image)
                                <img src="{{ str_starts_with($partner->image, 'http') ? $partner->image : asset('storage/' . $partner->image) }}"
                                    class="h-12 w-12 rounded-full object-cover border-2 {{ $isActive ? 'border-white/30' : 'border-white shadow-sm' }}"
                                    alt="Profile">
                            @else
                                <div
                                    class="h-12 w-12 rounded-full flex items-center justify-center font-black text-base uppercase shadow-sm {{ $isActive ? 'bg-white/20 text-white border border-white/30' : 'bg-indigo-50 text-indigo-700 border border-white' }}">
                                    {{ substr($partner->name, 0, 1) }}
                                </div>
                            @endif
                            <div class="min-w-0">
                                <h4 class="font-extrabold text-sm truncate {{ $isActive ? 'text-white' : 'text-gray-900' }}">
                                    {{ $partner->name }}
                                </h4>
                                <p
                                    class="text-[10px] font-bold mt-0.5 uppercase tracking-wide {{ $isActive ? 'text-indigo-200' : 'text-gray-400' }}">
                                    {{ $partner->level ?? 'Member' }}
                                </p>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="text-center py-10 px-4 flex flex-col items-center">
                        <div class="h-12 w-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                                </path>
                            </svg>
                        </div>
                        <p class="text-sm font-bold text-gray-400">No active partners</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="w-full md:w-2/3 lg:w-[70%] flex-col bg-white {{ $selectedPartner ? 'flex' : 'hidden md:flex' }}">
            @if($selectedPartner)

                <div
                    class="p-4 md:p-5 border-b border-gray-100 flex items-center gap-3 bg-white/95 backdrop-blur-sm z-10 sticky top-0">
                    <button onclick="history.back()"
                        class="md:hidden p-2 -ml-2 text-gray-500 hover:bg-gray-100 rounded-full transition cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>

                    @if($selectedPartner->image)
                        <img src="{{ str_starts_with($selectedPartner->image, 'http') ? $selectedPartner->image : asset('storage/' . $selectedPartner->image) }}"
                            class="h-10 w-10 rounded-full object-cover border border-gray-100" alt="Profile">
                    @else
                        <div
                            class="h-10 w-10 rounded-full flex items-center justify-center font-black text-xs uppercase bg-indigo-50 text-indigo-700 shadow-sm border border-indigo-100">
                            {{ substr($selectedPartner->name, 0, 1) }}
                        </div>
                    @endif

                    <div>
                        <h3 class="font-extrabold text-gray-900 text-base leading-tight">{{ $selectedPartner->name }}</h3>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">Connected</span>
                        </div>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto p-4 md:p-6 bg-[#f8f9fa] space-y-4 custom-scrollbar" id="chat-stream-box">
                    @forelse($messages as $msg)
                        @php $isMe = $msg->sender_id == auth()->id(); @endphp
                        <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                            <div
                                class="max-w-[85%] md:max-w-[70%] rounded-2xl p-3.5 shadow-sm text-[15px] leading-relaxed {{ $isMe ? 'bg-indigo-600 text-white rounded-br-sm' : 'bg-white text-gray-800 border border-gray-100 rounded-bl-sm' }}">
                                <p>{{ $msg->message }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="h-full flex flex-col items-center justify-center opacity-60">
                            <div class="bg-white p-4 rounded-full shadow-sm border border-gray-100 mb-3">
                                <svg class="w-8 h-8 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                            </div>
                            <p class="font-bold text-gray-500 text-sm">Say salam to start the conversation!</p>
                        </div>
                    @endforelse
                </div>

                <div class="p-3 md:p-4 bg-white border-t border-gray-100">
                    <form id="chat-form" action="{{ route('chat.send', $selectedPartner->id) }}" method="POST"
                        class="flex items-center gap-2 md:gap-3">
                        @csrf
                        <div class="relative flex-1">
                            <input type="text" name="message" id="message-input" required autocomplete="off"
                                placeholder="Type a message..."
                                class="w-full bg-gray-50/70 px-5 py-3 md:py-3.5 rounded-full text-[15px] text-gray-800 placeholder-gray-400 border border-gray-200 focus:outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-50/50 transition shadow-inner">
                        </div>

                        <button type="submit" id="send-btn"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white p-3 md:p-3.5 rounded-full transition shadow-md hover:shadow-lg hover:-translate-y-0.5 flex shrink-0 items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed group">
                            <svg class="w-5 h-5 ml-0.5 group-hover:translate-x-1 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                        </button>
                    </form>
                </div>

            @else
                <div class="h-full flex flex-col items-center justify-center bg-gray-50/50">
                    <div class="bg-white p-6 rounded-full shadow-sm border border-gray-100 mb-4">
                        <svg class="w-12 h-12 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-black text-gray-800">Your Messages</h3>
                    <p class="text-gray-400 mt-2 font-bold text-sm">Select an accountability partner to start chatting.</p>
                </div>
            @endif
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #e5e7eb;
            border-radius: 10px;
        }

        .custom-scrollbar:hover::-webkit-scrollbar-thumb {
            background-color: #d1d5db;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

            function scrollToBottom(animate = false) {
                let box = $('#chat-stream-box');
                if (box.length > 0) {
                    if (animate) {
                        box.animate({ scrollTop: box[0].scrollHeight }, 300);
                    } else {
                        box.scrollTop(box[0].scrollHeight);
                    }
                }
            }

            scrollToBottom();

            if (window.innerWidth > 768 && $('#message-input').length > 0) {
                $('#message-input').focus();
            }

            $('#chat-form').on('submit', function (e) {
                e.preventDefault();

                let form = $(this);
                let input = $('#message-input');
                let msg = input.val().trim();
                let btn = $('#send-btn');

                let sendIcon = '<svg class="w-5 h-5 ml-0.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>';
                let spinnerIcon = '<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

                if (msg === "") return;

                input.prop('disabled', true);
                btn.prop('disabled', true).html(spinnerIcon);

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    dataType: 'json', // লারাভেলকে বাধ্য করা হচ্ছে JSON রেসপন্স দিতে
                    success: function (response) {
                        if (response.success === true) {
                            // Remove the 'empty state' message if it exists
                            $('#chat-stream-box').find('.opacity-60').remove();

                            // Generate new message HTML with smooth slide-up animation effect
                            let html = `
                                            <div class="flex justify-end opacity-0 transform translate-y-4" style="transition: all 0.3s ease;">
                                                <div class="max-w-[85%] md:max-w-[70%] rounded-2xl p-3.5 shadow-sm text-[15px] leading-relaxed bg-indigo-600 text-white rounded-br-sm">
                                                    <p>${msg}</p>
                                                </div>
                                            </div>`;

                            let newElement = $(html).appendTo('#chat-stream-box');

                            // Trigger reflow and animate
                            setTimeout(() => {
                                newElement.removeClass('opacity-0 translate-y-4');
                            }, 50);

                            input.val('');
                            scrollToBottom(true);
                        } else {
            
                            alert("Message failed: " + response.error);
                        }
                    },
                    error: function (xhr) {
                        alert("Network error or session expired. Please refresh the page.");
                    },
                    complete: function () {
                        // Restore state
                        input.prop('disabled', false);
                        if (window.innerWidth > 768) input.focus();
                        btn.prop('disabled', false).html(sendIcon);
                    }
                });
            });
    </script>
@endsection