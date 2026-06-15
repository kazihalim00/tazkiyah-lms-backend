<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Tazkiyah Dashboard</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Chart.js Library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen p-6 md:p-10">

    <div class="max-w-4xl mx-auto">
        <!-- Header Section -->
        <div class="bg-white rounded-2xl shadow-sm p-8 mb-6 flex justify-between items-center border border-gray-100">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Assalamu Alaikum, {{ $user->name }}! 👋</h1>
                <p class="text-gray-500 mt-2">Welcome to your spiritual journey dashboard.</p>
            </div>
            <div class="flex items-center gap-4">
                <div
                    class="h-12 w-12 bg-indigo-100 rounded-full flex items-center justify-center text-xl border-2 border-white shadow-sm">
                    🕌
                </div>
                <!-- Logout Button -->
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="text-sm bg-red-50 text-red-600 px-4 py-2 rounded-lg font-medium hover:bg-red-100 transition">
                        Logout
                    </button>
                </form>
            </div>
        </div>

        <!-- Stats Grid Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div
                class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl shadow-lg p-6 text-white relative overflow-hidden">
                <div class="relative z-10">
                    <h2 class="text-blue-100 font-semibold text-lg uppercase tracking-wider">Total Points</h2>
                    <p class="text-5xl font-bold mt-2">{{ $points }}</p>
                </div>
                <div class="absolute -bottom-10 -right-10 h-40 w-40 bg-white opacity-10 rounded-full"></div>
            </div>

            <div
                class="bg-gradient-to-br from-emerald-400 to-teal-500 rounded-2xl shadow-lg p-6 text-white relative overflow-hidden">
                <div class="relative z-10">
                    <h2 class="text-emerald-100 font-semibold text-lg uppercase tracking-wider">Current Rank</h2>
                    <p class="text-4xl font-bold mt-2">{{ $badge }} 🏆</p>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="bg-white rounded-2xl shadow-sm p-8 border border-gray-100 mb-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Last 7 Days Activity</h2>
            <!-- Chart Canvas (This was missing) -->
            <canvas id="ibadahChart" height="100"></canvas>
        </div>

        <!-- Noor AI Chat Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col h-96 overflow-hidden">
            <!-- Chat Header -->
            <div class="bg-indigo-600 p-4 text-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 bg-white rounded-full flex items-center justify-center text-xl shadow-sm">
                        🤖
                    </div>
                    <div>
                        <h2 class="font-bold">Noor AI Companion</h2>
                        <p class="text-indigo-200 text-xs">Always here to listen and guide</p>
                    </div>
                </div>
            </div>

            <!-- Chat Messages Area -->
            <div id="chat-messages" class="flex-1 p-6 overflow-y-auto bg-gray-50 flex flex-col gap-4">
                <!-- AI Welcome Message -->
                <div class="flex gap-3">
                    <div
                        class="bg-white p-3 rounded-2xl rounded-tl-none shadow-sm border border-gray-100 max-w-[80%] text-gray-700 text-sm">
                        Assalamu Alaikum! How is your heart feeling today?
                    </div>
                </div>
            </div>

            <!-- Chat Input Area -->
            <div class="p-4 bg-white border-t border-gray-100">
                <form id="chat-form" class="flex gap-2">
                    <input type="text" id="chat-input" required
                        class="flex-1 px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm"
                        placeholder="Type your message here...">
                    <button type="submit"
                        class="bg-indigo-600 text-white px-6 py-3 rounded-xl font-medium hover:bg-indigo-700 transition flex items-center justify-center">
                        Send
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // --- 1. Chart Configuration ---
        const ctx = document.getElementById('ibadahChart').getContext('2d');
        const chartLabels = @json($chartLabels);
        const chartData = @json($chartData);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Daily Ibadah Score',
                    data: chartData,
                    borderColor: '#4F46E5',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#4F46E5',
                    pointRadius: 5
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, suggestedMax: 20 } }
            }
        });

        // --- 2. Chat Logic ---
        const chatForm = document.getElementById('chat-form');
        const chatInput = document.getElementById('chat-input');
        const chatMessages = document.getElementById('chat-messages');
        const csrfToken = '{{ csrf_token() }}';

        chatForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const message = chatInput.value.trim();
            if (!message) return;

            // Append User Message
            chatMessages.innerHTML += `
                <div class="flex gap-3 justify-end">
                    <div class="bg-indigo-600 text-white p-3 rounded-2xl rounded-tr-none shadow-sm max-w-[80%] text-sm">
                        ${message}
                    </div>
                </div>
            `;
            chatInput.value = '';
            chatMessages.scrollTop = chatMessages.scrollHeight;

            // Append Typing Indicator
            const typingId = 'typing-' + Date.now();
            chatMessages.innerHTML += `
                <div id="${typingId}" class="flex gap-3">
                    <div class="bg-white p-3 rounded-2xl rounded-tl-none shadow-sm border border-gray-100 text-gray-400 text-sm italic">
                        Noor AI is typing...
                    </div>
                </div>
            `;
            chatMessages.scrollTop = chatMessages.scrollHeight;

            try {
                // Send Request
                const response = await fetch('/web-chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ message: message })
                });

                const result = await response.json();

                // Remove Typing Indicator & Append AI Response
                document.getElementById(typingId).remove();
                chatMessages.innerHTML += `
                    <div class="flex gap-3">
                        <div class="bg-white p-3 rounded-2xl rounded-tl-none shadow-sm border border-gray-100 max-w-[80%] text-gray-700 text-sm">
                            ${result.reply}
                        </div>
                    </div>
                `;
                chatMessages.scrollTop = chatMessages.scrollHeight;

            } catch (error) {
                document.getElementById(typingId).remove();
                chatMessages.innerHTML += `
                    <div class="flex gap-3">
                        <div class="bg-red-50 text-red-600 p-3 rounded-2xl rounded-tl-none shadow-sm border border-red-100 max-w-[80%] text-sm">
                            Oops! Something went wrong.
                        </div>
                    </div>
                `;
            }
        });
    </script>
</body>

</html>