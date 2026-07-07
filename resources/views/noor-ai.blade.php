@extends('layouts.app')

@section('title', 'Noor AI - Spiritual Mentor')
@section('header_title', 'Noor AI Mentor')

@section('content')
    <div
        class="max-w-4xl mx-auto h-[calc(100vh-140px)] flex flex-col bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden relative">

        <!-- Top Header with Crescent Moon Logo -->
        <div
            class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4 flex items-center justify-between shrink-0 z-10 shadow-md">
            <div class="flex items-center gap-4">
                <div class="relative">
                    <!-- Golden Crescent Moon Logo -->
                    <div
                        class="h-12 w-12 bg-amber-500/20 rounded-2xl flex items-center justify-center border border-amber-400/40 shadow-inner">
                        <span class="text-2xl">🌙</span>
                    </div>
                    <div class="absolute bottom-0 right-0 h-3.5 w-3.5 bg-emerald-400 border-2 border-white rounded-full">
                    </div>
                </div>
                <div>
                    <h2 class="text-white font-bold text-lg leading-tight">Noor AI</h2>
                    <p class="text-indigo-100 text-xs font-medium">Your 24/7 Spiritual Companion</p>
                </div>
            </div>

            <div
                class="hidden sm:flex items-center gap-2 bg-white/20 backdrop-blur-md px-3 py-1.5 rounded-lg text-white text-xs font-semibold border border-white/30">
                Private & Secure
            </div>
        </div>

        <!-- Chat Container -->
        <div id="chat-container" class="flex-1 overflow-y-auto p-6 space-y-6 bg-gray-50/50 scroll-smooth relative">
            <div class="flex items-start gap-4">
                <!-- AI Initial Message Avatar with Crescent Moon -->
                <div
                    class="h-10 w-10 shrink-0 bg-gradient-to-br from-amber-500 to-amber-700 rounded-full flex items-center justify-center text-white shadow-md">
                    <span class="text-lg">🌙</span>
                </div>
                <div
                    class="bg-white p-4 rounded-2xl rounded-tl-none shadow-sm border border-gray-100 max-w-[80%] md:max-w-[70%]">
                    <p class="text-gray-700 leading-relaxed">
                        Assalamu Alaikum, {{ auth()->user()->name }}! 👋<br><br>
                        How can I assist your Tazkiyah journey today?
                    </p>
                    <span class="text-[10px] text-gray-400 mt-2 block uppercase font-semibold">Just Now</span>
                </div>
            </div>
        </div>

        <div id="typing-indicator" class="hidden px-6 py-2 bg-gray-50 flex items-center gap-3">
            <div
                class="h-8 w-8 shrink-0 bg-amber-100 rounded-full flex items-center justify-center text-amber-700 font-bold text-xs">
                <span>🌙</span>
            </div>
            <div
                class="bg-white px-4 py-2 rounded-2xl rounded-tl-none border border-gray-100 shadow-sm flex items-center gap-1">
                <div class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce"></div>
                <div class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                <div class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
            </div>
        </div>

        <div class="p-4 bg-white border-t border-gray-100 shrink-0 z-10">
            <form id="chat-form" class="relative flex items-end gap-2">
                <textarea id="user-input" rows="1"
                    class="w-full bg-gray-50 border border-gray-200 text-gray-800 text-sm rounded-2xl focus:ring-indigo-500 focus:border-indigo-500 block p-4 pr-14 resize-none shadow-inner transition"
                    placeholder="Type your message..." required></textarea>
                <button type="submit" id="send-btn"
                    class="absolute right-2 bottom-2 h-10 w-10 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl flex items-center justify-center transition shadow-md">
                    <svg class="w-5 h-5 -ml-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chatForm = document.getElementById('chat-form');
            const userInput = document.getElementById('user-input');
            const chatContainer = document.getElementById('chat-container');
            const typingIndicator = document.getElementById('typing-indicator');
            const sendBtn = document.getElementById('send-btn');
            const csrfToken = '{{ csrf_token() }}';

            const userImage = "{{ auth()->user()->image ?? '' }}";
            const userName = "{{ auth()->user()->name ?? 'U' }}";

            async function loadChatHistory() {
                try {
                    const response = await fetch('{{ route("web.chat.history") }}', {
                        method: 'GET',
                        headers: { 'Accept': 'application/json' }
                    });
                    const result = await response.json();
                    if (result.success && result.data.length > 0) {
                        chatContainer.innerHTML = '';
                        result.data.forEach(chat => {
                            appendMessage('user', chat.user_message);
                            if (chat.ai_response) appendMessage('ai', chat.ai_response);
                        });
                        chatContainer.scrollTop = chatContainer.scrollHeight;
                    }
                } catch (error) { }
            }
            loadChatHistory();

            userInput.addEventListener('input', function () {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight < 120 ? this.scrollHeight : 120) + 'px';
            });

            userInput.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    if (this.value.trim() !== '') {
                        chatForm.dispatchEvent(new Event('submit'));
                    }
                }
            });

            chatForm.addEventListener('submit', async function (e) {
                e.preventDefault();
                const message = userInput.value.trim();
                if (!message) return;

                appendMessage('user', message);
                userInput.value = '';
                userInput.style.height = 'auto';
                userInput.disabled = true;
                sendBtn.disabled = true;
                typingIndicator.classList.remove('hidden');
                chatContainer.scrollTop = chatContainer.scrollHeight;

                try {
                    const response = await fetch('{{ url("/web-chat") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ message: message })
                    });
                    const responseData = await response.json();
                    typingIndicator.classList.add('hidden');
                    if (responseData.success && responseData.data && responseData.data.ai_response) {
                        appendMessage('ai', responseData.data.ai_response);
                    } else {
                        appendMessage('ai', '⚠️ Connection failed.');
                    }
                } catch (error) {
                    typingIndicator.classList.add('hidden');
                    appendMessage('ai', 'Oops! Could not reach the server.');
                } finally {
                    userInput.disabled = false;
                    sendBtn.disabled = false;
                    userInput.focus();
                    chatContainer.scrollTop = chatContainer.scrollHeight;
                }
            });

            function appendMessage(sender, text) {
                let formattedText = sender === 'ai' ? marked.parse(text) : text.replace(/\n/g, '<br>');
                let userAvatarContent = userImage
                    ? `<img src="${userImage}" class="h-full w-full object-cover rounded-full" />`
                    : `<span class="font-bold text-gray-700">${userName.charAt(0)}</span>`;

                let messageHTML = '';
                if (sender === 'user') {
                    messageHTML = `
                                <div class="flex items-start justify-end gap-4 animate-[fadeIn_0.3s_ease-out] mb-4">
                                    <div class="bg-indigo-600 p-4 rounded-2xl rounded-tr-none shadow-md text-white max-w-[80%] md:max-w-[70%]">
                                        <p class="leading-relaxed text-sm md:text-base">${formattedText}</p>
                                    </div>
                                    <div class="h-10 w-10 shrink-0 bg-gray-200 rounded-full flex items-center justify-center overflow-hidden">
                                        ${userAvatarContent}
                                    </div>
                                </div>`;
                } else {
                    messageHTML = `
                                <div class="flex items-start gap-4 animate-[fadeIn_0.3s_ease-out] mb-4">
                                    <!-- AI Message Avatar with Crescent Moon Logo -->
                                    <div class="h-10 w-10 shrink-0 bg-gradient-to-br from-amber-500 to-amber-700 rounded-full flex items-center justify-center text-white shadow-md">
                                        <span class="text-lg">🌙</span>
                                    </div>
                                    <div class="bg-white p-4 rounded-2xl rounded-tl-none shadow-sm border border-gray-100 max-w-[80%] md:max-w-[70%] text-gray-700 leading-relaxed text-sm md:text-base">
                                        ${formattedText}
                                    </div>
                                </div>`;
                }
                chatContainer.insertAdjacentHTML('beforeend', messageHTML);
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }
        });
    </script>
@endpush