@extends('layouts.app')
@section('title', 'Dashboard - Tazkiyah')
@section('header_title', 'Overview')

@section('content')
    @php
        // User মডেল থেকে ডায়নামিক ব্যাজ এবং ট্রি-স্টেজ নেওয়া হচ্ছে
        $badgeData = auth()->user()->badge;
        $badgeName = $badgeData['name'];
        $badgeIcon = $badgeData['icon'];
        $treeStage = $badgeData['tree_stage'];

        // গাছের সাইজ এবং গ্লো (Glow) ডায়নামিক করার লজিক
        $emojiSize = 35 + ($treeStage * 8);
        $glowSpread = 5 + ($treeStage * 3);
    @endphp

    <style>
        @keyframes pulse-glow {
            0% {
                box-shadow: 0 0
                    {{ $glowSpread }}
                    px rgba(74, 222, 128, 0.2);
                transform: scale(1);
            }

            50% {
                box-shadow: 0 0
                    {{ $glowSpread * 2.5 }}
                    px rgba(74, 222, 128, 0.5);
                transform: scale(1.05);
            }

            100% {
                box-shadow: 0 0
                    {{ $glowSpread }}
                    px rgba(74, 222, 128, 0.2);
                transform: scale(1);
            }
        }

        .tree-circle {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: pulse-glow 3s infinite ease-in-out;
            margin: 0 auto;
            border: 2px solid rgba(74, 222, 128, 0.15);
        }

        .tree-emoji {
            font-size:
                {{ $emojiSize }}
                px;
            transition: all 0.5s ease;
            text-shadow: 0px 10px 15px rgba(0, 0, 0, 0.1);
        }
    </style>

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Assalamu Alaikum! 👋</h1>
        <p class="text-gray-500 mt-2">Here is your recent spiritual progress.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

        <div
            class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl shadow-lg p-6 text-white relative overflow-hidden flex flex-col justify-center">
            <div class="relative z-10">
                <h2 class="text-blue-100 font-semibold text-lg uppercase tracking-wider">Total Points</h2>
                <p class="text-5xl font-bold mt-2">{{ $points ?? auth()->user()->total_points }}</p>
            </div>
            <div class="absolute -bottom-10 -right-10 h-40 w-40 bg-white opacity-10 rounded-full"></div>
        </div>

        <div
            class="bg-gradient-to-br from-emerald-400 to-teal-500 rounded-2xl shadow-lg p-6 text-white relative overflow-hidden flex flex-col justify-center">
            <div class="relative z-10">
                <h2 class="text-emerald-100 font-semibold text-lg uppercase tracking-wider">Current Rank</h2>
                <div class="flex items-center gap-3 mt-2">
                    <span class="text-4xl drop-shadow-md">{{ $badgeIcon }}</span>
                    <p class="text-3xl font-bold drop-shadow-md">{{ $badgeName }}</p>
                </div>
            </div>
            <div class="absolute -top-10 -right-10 h-32 w-32 bg-white opacity-10 rounded-full"></div>
        </div>

        <div
            class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col items-center justify-center text-center">
            <div class="tree-circle mb-3">
                <span class="tree-emoji">{{ $badgeIcon }}</span>
            </div>
            <h2 class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-2">Spiritual Growth</h2>
        </div>

    </div>

    <div class="bg-white rounded-2xl shadow-sm p-8 border border-gray-100">
        <h2 class="text-xl font-bold text-gray-800 mb-6">Last 7 Days Activity</h2>
        <canvas id="ibadahChart" height="80"></canvas>
    </div>

@endsection

@push('scripts')
    <script>
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
    </script>
@endpush