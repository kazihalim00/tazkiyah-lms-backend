@extends('layouts.app')
@section('title', 'Dashboard - Tazkiyah')
@section('header_title', 'Overview')

@section('content')
    @php
        $user = auth()->user()->fresh();

        $badgeData = $user->badge;
        $badgeName = $badgeData['name'];
        $badgeIcon = $badgeData['icon'];
        $treeStage = $badgeData['tree_stage'];
        $totalPoints = $user->total_points;


        $circleSize = 70 + ($treeStage * 12);
        $emojiSize = 40 + ($treeStage * 8);   
    @endphp

    <style>
        @keyframes gentle-pulse {
            0% {
                transform: scale(1);
                box-shadow: 0 0 15px rgba(74, 222, 128, 0.2);
            }

            50% {
                transform: scale(1.05);
                box-shadow: 0 0 30px rgba(74, 222, 128, 0.5);
            }

            100% {
                transform: scale(1);
                box-shadow: 0 0 15px rgba(74, 222, 128, 0.2);
            }
        }

        .animated-tree-ring {
            border-radius: 50%;
            background: linear-gradient(145deg, #ffffff, #f0fdf4);
            border: 2px solid rgba(74, 222, 128, 0.2);
            animation: gentle-pulse 3s infinite ease-in-out;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Assalamu Alaikum! 👋</h1>
        <p class="text-gray-500 mt-2">Here is your recent spiritual progress.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 mb-8">

        <div
            class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl shadow-lg p-6 text-white relative overflow-hidden flex flex-col justify-center min-h-[160px]">
            <div class="relative z-10">
                <h2 class="text-blue-100 font-semibold text-sm md:text-lg uppercase tracking-wider">Total Points</h2>
                <p class="text-4xl md:text-5xl font-bold mt-2">{{ $totalPoints }}</p>
            </div>
            <div class="absolute -bottom-10 -right-10 h-32 w-32 md:h-40 md:w-40 bg-white opacity-10 rounded-full"></div>
        </div>

        <div
            class="bg-gradient-to-br from-emerald-400 to-teal-500 rounded-2xl shadow-lg p-6 text-white relative overflow-hidden flex flex-col justify-center min-h-[160px]">
            <div class="relative z-10">
                <h2 class="text-emerald-100 font-semibold text-sm md:text-lg uppercase tracking-wider">Current Rank</h2>
                <div class="flex items-center gap-3 mt-2">
                    <span class="text-3xl md:text-4xl drop-shadow-md">{{ $badgeIcon }}</span>
                    <p class="text-2xl md:text-3xl font-bold drop-shadow-md truncate">{{ $badgeName }}</p>
                </div>
            </div>
            <div class="absolute -top-10 -right-10 h-24 w-24 md:h-32 md:w-32 bg-white opacity-10 rounded-full"></div>
        </div>

        <div
            class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col items-center text-center min-h-[160px]">
            <h2 class="text-[10px] md:text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Spiritual Growth</h2>

            <div class="flex-1 flex items-center justify-center py-2 w-full">
                <div class="animated-tree-ring"
                    style="width: {{ $circleSize * 0.8 }}px; height: {{ $circleSize * 0.8 }}px; md:width: {{ $circleSize }}px; md:height: {{ $circleSize }}px;">
                    <span
                        style="font-size: {{ $emojiSize * 0.8 }}px; line-height: 1; md:font-size: {{ $emojiSize }}px; text-shadow: 0px 8px 15px rgba(0,0,0,0.1);">
                        @php
                            $icons = [1 => '🌱', 2 => '🌿', 3 => '🍃', 4 => '🪴', 5 => '🌳', 6 => '🌳', 7 => '🌲', 8 => '👑'];
                            echo $icons[$treeStage] ?? '🌱';
                        @endphp
                    </span>
                </div>
            </div>

            <p
                class="text-[9px] md:text-[10px] font-black text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full uppercase mt-auto">
                Stage {{ $treeStage }} of 8
            </p>
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
        const chartLabels = @json($chartLabels ?? []);
        const chartData = @json($chartData ?? []);

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