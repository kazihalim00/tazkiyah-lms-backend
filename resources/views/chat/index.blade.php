@extends('layouts.app')

@section('title', 'Messages')
@section('header_title', 'Partner Chat')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="max-w-6xl mx-auto bg-white md:rounded-3xl shadow-sm border-x md:border border-gray-100 overflow-hidden flex h-[calc(100vh-8rem)] md:h-[calc(100vh-10rem)] relative">

        <div class="w-full md:w-1/3 lg:w-[30%] border-r border-gray-100 flex-col bg-gray-50/50 {{ $selectedPartner ? 'hidden md:flex' : 'flex' }}">

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
                                <div class="h-12 w-12 rounded-full flex items-center justify-center font-black text-base uppercase shadow-sm {{ $isActive ? 'bg-white/20 text-white border border-white/30' : 'bg-indigo-50 text-indigo-700 border border-white' }}">
                                    {{ substr($partner->name, 0, 1) }}
                                </div>
                            @endif
                            <div class="min-w-0 flex-1">
                                <h4 class="font-extrabold text-sm truncate {{ $isActive ? 'text-white' : 'text-gray-900' }}">
                                    {{ $partner->name }}
                                </h4>
                                <p class="text-[12px] mt-0.5 truncate {{ $isActive ? 'text-indigo-200' : 'text-gray-500' }}">
                                    @if($partner->latest_message)
                                        {{ \Illuminate\Support\Str::limit($partner->latest_message, 30) }}
                                    @else
                                        <span class="italic text-[11px] opacity-70">Say salam to start...</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="text-center py-10 px-4 flex flex-col items-center">
                        <div class="h-12 w-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </div>
                        <p class="text-sm font-bold text-gray-400">No active partners</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="w-full md:w-2/3 lg:w-[70%] flex-col bg-white {{ $selectedPartner ? 'flex' : 'hidden md:flex' }}">
            @if($selectedPartner)

                <div class="p-4 md:p-5 border-b border-gray-100 flex items-center gap-3 bg-white/95 backdrop-blur-sm z-10 sticky top-0">
                    <button onclick="history.back()" class="md:hidden p-2 -ml-2 text-gray-500 hover:bg-gray-100 rounded-full transition cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>

                    @if($selectedPartner->image)
                        <img src="{{ str_starts_with($selectedPartner->image, 'http') ? $selectedPartner->image : asset('storage/' . $selectedPartner->image) }}"
                            class="h-10 w-10 rounded-full object-cover border border-gray-100" alt="Profile">
                    @else
                        <div class="h-10 w-10 rounded-full flex items-center justify-center font-black text-xs uppercase bg-indigo-50 text-indigo-700 shadow-sm border border-indigo-100">
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

                        <div class="relative group flex {{ $isMe ? 'justify-end' : 'justify-start' }} mb-2">

                            <div class="hidden group-hover:flex items-center gap-2 px-2 {{ $isMe ? 'order-1 mr-1' : 'order-2 ml-1' }}">
                                <button type="button" onclick="sendReaction({{ $msg->id }})"
                                    class="text-gray-400 hover:text-amber-500 text-[16px] transition-transform hover:scale-110" title="React">
                                    😀
                                </button>
                                <button type="button" onclick="setReply({{ $msg->id }}, '{{ addslashes($msg->message) }}')" class="text-gray-400 hover:text-indigo-600 transition" title="Reply">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                </button>
                                <button type="button" class="text-gray-400 hover:text-indigo-600 transition" title="Forward">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </button>
                            </div>

                            <div class="{{ $isMe ? 'order-2 bg-indigo-600 text-white rounded-br-sm' : 'order-1 bg-white text-gray-800 border border-gray-100 rounded-bl-sm' }} max-w-[85%] md:max-w-[70%] rounded-2xl p-3.5 shadow-sm text-[15px] leading-relaxed relative">

                                @if($msg->reply_to_id && $msg->repliedMessage)
                                    <div class="mb-2 p-2 rounded-lg text-xs {{ $isMe ? 'bg-indigo-700/50 border-l-2 border-indigo-300' : 'bg-gray-50 border-l-2 border-indigo-500' }}">
                                        <p class="truncate opacity-80">{{ $msg->repliedMessage->message }}</p>
                                    </div>
                                @endif

                                <p>{{ $msg->message }}</p>

                                @if($msg->reaction)
                                    <div class="absolute -bottom-2.5 {{ $isMe ? 'right-2' : 'left-2' }} bg-white rounded-full shadow-sm border border-gray-100 px-1.5 py-0.5 text-[10px]">
                                        {{ $msg->reaction }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="h-full flex flex-col items-center justify-center opacity-60">
                            <div class="bg-white p-4 rounded-full shadow-sm border border-gray-100 mb-3">
                                <svg class="w-8 h-8 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                            </div>
                            <p class="font-bold text-gray-500 text-sm">Say salam to start the conversation!</p>
                        </div>
                    @endforelse
                </div>

                <div class="bg-white border-t border-gray-100 flex flex-col">

                    <div id="reply-preview-container" class="hidden px-4 py-2 bg-gray-50/80 border-b border-gray-100 flex justify-between items-center">
                        <div class="flex-1 min-w-0 pr-4">
                            <p class="text-[10px] font-bold text-indigo-600 uppercase tracking-wide mb-0.5">Replying to message</p>
                            <p class="text-xs text-gray-500 truncate border-l-2 border-indigo-400 pl-2" id="reply-preview-text"></p>
                        </div>
                        <button type="button" onclick="cancelReply()" class="text-gray-400 hover:text-red-500 transition p-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="p-3 md:p-4">
                        <form id="chat-form" action="{{ route('chat.send', $selectedPartner->id) }}" method="POST" class="flex items-center gap-2 md:gap-3">
                            @csrf
                            <input type="hidden" name="reply_to_id" id="reply_to_id" value="">

                            <div class="relative flex-1">
                                <input type="text" name="message" id="message-input" required autocomplete="off"
                                    placeholder="Type a message..."
                                    class="w-full bg-gray-50/70 px-5 py-3 md:py-3.5 rounded-full text-[15px] text-gray-800 placeholder-gray-400 border border-gray-200 focus:outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-50/50 transition shadow-inner">
                            </div>

                            <button type="submit" id="send-btn"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white p-3 md:p-3.5 rounded-full transition shadow-md hover:shadow-lg hover:-translate-y-0.5 flex shrink-0 items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed group">
                                <svg class="w-5 h-5 ml-0.5 group-hover:translate-x-1 transition-transform" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>

            @else
                <div class="h-full flex flex-col items-center justify-center bg-gray-50/50">
                    <div class="bg-white p-6 rounded-full shadow-sm border border-gray-100 mb-4">
                        <svg class="w-12 h-12 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-black text-gray-800">Your Messages</h3>
                    <p class="text-gray-400 mt-2 font-bold text-sm">Select an accountability partner to start chatting.</p>
                </div>
            @endif
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #e5e7eb; border-radius: 10px; }
        .custom-scrollbar:hover::-webkit-scrollbar-thumb { background-color: #d1d5db; }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Reply feature logic
        function setReply(messageId, messageText) {
            $('#reply_to_id').val(messageId);
            $('#reply-preview-text').text(messageText);
            $('#reply-preview-container').removeClass('hidden');
            $('#message-input').focus();
        }

        function cancelReply() {
            $('#reply_to_id').val('');
            $('#reply-preview-container').addClass('hidden');
        }

        $(document).ready(function () {
            // CSRF Setup
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

            // ==========================================
            // Auto-refresh Chat (Real-time Polling)
            // ==========================================
            setInterval(function () {
                let chatBox = $('#chat-stream-box');
                if (chatBox.length > 0) {
                    // Check if user is scrolled to the bottom
                    let isAtBottom = (chatBox[0].scrollHeight - chatBox.scrollTop() <= chatBox.outerHeight() + 50);

                    // Silently reload only the chatbox content in the background
                    chatBox.load(location.href + " #chat-stream-box > *", function () {
                        // Auto-scroll to bottom if the user was already at the bottom
                        if (isAtBottom) {
                            scrollToBottom();
                        }
                    });
                }
            }, 3000);

            // ==========================================
            // Send Message (AJAX)
            // ==========================================
            $('#chat-form').on('submit', function (e) {
                e.preventDefault();

                let form = $(this);
                let input = $('#message-input');
                let msg = input.val().trim();
                let btn = $('#send-btn');
                let replyText = $('#reply-preview-text').text(); // Capture reply text for UI

                // Save form data before disabling inputs
                let formData = form.serialize();

                let sendIcon = '<svg class="w-5 h-5 ml-0.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>';
                let spinnerIcon = '<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

                if (msg === "") return;

                input.prop('disabled', true);
                btn.prop('disabled', true).html(spinnerIcon);

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: formData, 
                    dataType: 'json',
                    success: function (response) {
                        if (response.success === true) {
                            $('#chat-stream-box').find('.opacity-60').remove();

                            // Build the new message bubble for immediate UI update
                            let replyHtml = replyText ? `<div class="mb-2 p-2 rounded-lg text-xs bg-indigo-700/50 border-l-2 border-indigo-300"><p class="truncate opacity-80">${replyText}</p></div>` : '';

                            let html = `
                                <div class="flex justify-end opacity-0 transform translate-y-4" style="transition: all 0.3s ease; margin-bottom: 0.5rem;">
                                    <div class="max-w-[85%] md:max-w-[70%] rounded-2xl p-3.5 shadow-sm text-[15px] leading-relaxed bg-indigo-600 text-white rounded-br-sm">
                                        ${replyHtml}
                                        <p>${msg}</p>
                                    </div>
                                </div>`;

                            let newElement = $(html).appendTo('#chat-stream-box');

                            setTimeout(() => {
                                newElement.removeClass('opacity-0 translate-y-4');
                            }, 50);

                            // Reset input and reply preview
                            input.val('');
                            cancelReply();
                            scrollToBottom(true);
                        } else {
                            alert("Message failed: " + response.error);
                        }
                    },
                    error: function (xhr) {
                        alert("Network error. Please try again.");
                    },
                    complete: function () {
                        input.prop('disabled', false);
                        if (window.innerWidth > 768) input.focus();
                        btn.prop('disabled', false).html(sendIcon);
                    }
                });
            });
        }); 

        function sendReaction(messageId) {
                let emoji = prompt("Enter an emoji (e.g., ❤️, 👍, 😂, 😢):");
                if (emoji) {
                    $.ajax({
                        url: `/messages/${messageId}/react`,
                        method: 'POST',
                        data: { reaction: emoji },
                        success: function () {
                        }
                    });
                }
            }
    </script>
@endsection