@extends('layouts.app')
@section('title', 'Noor AI - Tazkiyah')
@section('header_title', 'AI Companion')

@section('content')
    <!-- Adjusted container: full height and wider -->
    <div class="h-full flex flex-col max-w-5xl mx-auto">
        <!-- Noor AI Chat Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col flex-1 overflow-hidden">
            <!-- Chat Header -->
            <div class="bg-indigo-600 p-5 text-white flex items-center gap-4">
                <div class="h-10 w-10 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-lg">Noor AI Companion</h2>
                    <p class="text-indigo-200 text-xs">Always here to listen and guide</p>
                </div>
            </div>

            <!-- Messages Area: Now expands to fill space -->
            <div id="chat-messages" class="flex-1 p-8 overflow-y-auto bg-gray-50 flex flex-col gap-6">
                <!-- Messages go here -->
            </div>

            <!-- Input Area: Width 100% -->
            <div class="p-6 bg-white border-t border-gray-100">
                <form id="chat-form" class="flex gap-4">
                    <input type="text" id="chat-input" required
                        class="flex-1 px-5 py-4 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        placeholder="Type your message here...">
                    <button type="submit"
                        class="bg-indigo-600 text-white px-8 py-4 rounded-xl font-bold hover:bg-indigo-700 transition">
                        Send
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const chatForm = document.getElementById('chat-form');
        const chatInput = document.getElementById('chat-input');
        const chatMessages = document.getElementById('chat-messages');
        const csrfToken = '{{ csrf_token() }}';

        chatForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const message = chatInput.value.trim();
            if (!message) return;

            chatMessages.innerHTML += `
                                <div class="flex gap-3 justify-end">
                                    <div class="bg-indigo-600 text-white p-4 rounded-2xl rounded-tr-none shadow-sm max-w-[80%] text-sm leading-relaxed">
                                        ${message}
                                    </div>
                                </div>
                            `;
            chatInput.value = '';
            chatMessages.scrollTop = chatMessages.scrollHeight;

            const typingId = 'typing-' + Date.now();
            chatMessages.innerHTML += `
                                <div id="${typingId}" class="flex gap-3">
                                    <div class="bg-white p-4 rounded-2xl rounded-tl-none shadow-sm border border-gray-100 text-gray-400 text-sm italic">
                                        Noor AI is typing...
                                    </div>
                                </div>
                            `;
            chatMessages.scrollTop = chatMessages.scrollHeight;

            try {
                const response = await fetch('/web-chat', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ message: message })
                });
                const result = await response.json();

                document.getElementById(typingId).remove();
                chatMessages.innerHTML += `
                                    <div class="flex gap-3">
                                        <div class="bg-white p-4 rounded-2xl rounded-tl-none shadow-sm border border-gray-100 max-w-[80%] text-gray-700 text-sm leading-relaxed">
                                            ${result.reply}
                                        </div>
                                    </div>
                                `;
                chatMessages.scrollTop = chatMessages.scrollHeight;
            } catch (error) {
                document.getElementById(typingId).remove();
                chatMessages.innerHTML += `
                                    <div class="flex gap-3">
                                        <div class="bg-red-50 text-red-600 p-4 rounded-2xl rounded-tl-none shadow-sm border border-red-100 max-w-[80%] text-sm">
                                            Connection Error. Please check your Python server.
                                        </div>
                                    </div>
                                `;
            }
        });
    </script>
@endpush