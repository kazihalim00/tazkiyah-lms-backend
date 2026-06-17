@extends('layouts.app')

@section('title', 'Noor AI - Spiritual Mentor')
@section('header_title', 'Noor AI Mentor')

@section('content')
    <div
        class="max-w-4xl mx-auto h-[calc(100vh-140px)] flex flex-col bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden relative">

        <div
            class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4 flex items-center justify-between shrink-0 z-10 shadow-md">
            <div class="flex items-center gap-4">
                <div class="relative">
                    <div
                        class="h-12 w-12 bg-white rounded-full flex items-center justify-center text-indigo-600 font-bold text-xl shadow-inner">
                        N
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
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                </svg>
                Private & Secure
            </div>
        </div>

        <div id="chat-container" class="flex-1 overflow-y-auto p-6 space-y-6 bg-gray-50/50 scroll-smooth relative">
            <div
                class="absolute inset-0 opacity-[0.03] bg-[url('https://www.transparenttextures.com/patterns/arabesque.png')] pointer-events-none">
            </div>

            <div class="flex items-start gap-4">
                <div
                    class="h-10 w-10 shrink-0 bg-indigo-600 rounded-full flex items-center justify-center text-white font-bold shadow-md">
                    N
                </div>
                <div
                    class="bg-white p-4 rounded-2xl rounded-tl-none shadow-sm border border-gray-100 max-w-[80%] md:max-w-[70%]">
                    <p class="text-gray-700 leading-relaxed">
                        Assalamu Alaikum, {{ auth()->user()->name }}! 👋<br><br>
                        I am Noor AI, your spiritual mentor. Whether you have a question about Fiqh, need motivation, or
                        just want to share your thoughts, I am here for you. How can I assist your Tazkiyah journey today?
                    </p>
                    <span class="text-[10px] text-gray-400 mt-2 block uppercase font-semibold">Just Now</span>
                </div>
            </div>
        </div>

        <div id="typing-indicator" class="hidden px-6 py-2 bg-gray-50 flex items-center gap-3">
            <div
                class="h-8 w-8 shrink-0 bg-indigo-200 rounded-full flex items-center justify-center text-indigo-500 font-bold text-xs">
                N</div>
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
                    placeholder="Type your message or question here... (Press Shift+Enter for new line)"
                    required></textarea>

                <button type="submit" id="send-btn"
                    class="absolute right-2 bottom-2 h-10 w-10 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl flex items-center justify-center transition shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
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
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chatForm = document.getElementById('chat-form');
            const userInput = document.getElementById('user-input');
            const chatContainer = document.getElementById('chat-container');
            const typingIndicator = document.getElementById('typing-indicator');
            const sendBtn = document.getElementById('send-btn');
            const csrfToken = '{{ csrf_token() }}';

            // Auto-resize textarea
            userInput.addEventListener('input', function () {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight < 120 ? this.scrollHeight : 120) + 'px';
            });

            // Submit on Enter (Prevent default to avoid new line, allow Shift+Enter for new line)
            userInput.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    if (this.value.trim() !== '') chatForm.dispatchEvent(new Event('submit'));
                }
            });

            chatForm.addEventListener('submit', async function (e) {
                e.preventDefault();

                const message = userInput.value.trim();
                if (!message) return;

                // 1. Append User Message to UI
                appendMessage('user', message);

                // Reset Input
                userInput.value = '';
                userInput.style.height = 'auto';

                // Disable input and show loading
                userInput.disabled = true;
                sendBtn.disabled = true;
                typingIndicator.classList.remove('hidden');
                chatContainer.scrollTop = chatContainer.scrollHeight;

                try {
                    // 2. Send POST request to Laravel Backend
                    const response = await fetch('{{ url("/web-chat") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ message: message })
                    });

                    const data = await response.json();

                    // 3. Append AI Response
                    typingIndicator.classList.add('hidden');
                    if (data.success) {
                        appendMessage('ai', data.reply);
                    } else {
                        appendMessage('ai', 'I am currently having trouble connecting to my knowledge base. Please try again later.');
                    }

                } catch (error) {
                    typingIndicator.classList.add('hidden');
                    appendMessage('ai', 'Oops! The connection to Noor AI failed. Make sure your Python Flask server is running.');
                } finally {
                    // Re-enable input
                    userInput.disabled = false;
                    sendBtn.disabled = false;
                    userInput.focus();
                    chatContainer.scrollTop = chatContainer.scrollHeight;
                }
            });

            function appendMessage(sender, text) {
                const timeString = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                let messageHTML = '';

                // Convert newlines to <br> for HTML rendering
                const formattedText = text.replace(/\n/g, '<br>');

                if (sender === 'user') {
                    messageHTML = `
                        <div class="flex items-start justify-end gap-4 animate-[fadeIn_0.3s_ease-out]">
                            <div class="bg-indigo-600 p-4 rounded-2xl rounded-tr-none shadow-md text-white max-w-[80%] md:max-w-[70%]">
                                <p class="leading-relaxed text-sm md:text-base">${formattedText}</p>
                                <span class="text-[10px] text-indigo-200 mt-2 block uppercase font-semibold text-right">${timeString}</span>
                            </div>
                            <div class="h-10 w-10 shrink-0 bg-gray-200 rounded-full overflow-hidden shadow-sm border-2 border-white">
                                @if(auth()->user()->image)
                                    <img src="{{ asset('storage/' . auth()->user()->image) }}" class="h-full w-full object-cover">
                                @else
                                    <div class="h-full w-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold uppercase">{{ substr(auth()->user()->name, 0, 1) }}</div>
                                @endif
                            </div>
                        </div>`;
                } else {
                    messageHTML = `
                        <div class="flex items-start gap-4 animate-[fadeIn_0.3s_ease-out]">
                            <div class="h-10 w-10 shrink-0 bg-indigo-600 rounded-full flex items-center justify-center text-white font-bold shadow-md">
                                N
                            </div>
                            <div class="bg-white p-4 rounded-2xl rounded-tl-none shadow-sm border border-gray-100 max-w-[80%] md:max-w-[70%]">
                                <p class="text-gray-700 leading-relaxed text-sm md:text-base">${formattedText}</p>
                                <span class="text-[10px] text-gray-400 mt-2 block uppercase font-semibold">${timeString}</span>
                            </div>
                        </div>`;
                }

                chatContainer.insertAdjacentHTML('beforeend', messageHTML);
            }
        });
    </script>

    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Custom Scrollbar for Chat */
        #chat-container::-webkit-scrollbar {
            width: 6px;
        }

        #chat-container::-webkit-scrollbar-track {
            background: transparent;
        }

        #chat-container::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 20px;
        }
    </style>
@endpush