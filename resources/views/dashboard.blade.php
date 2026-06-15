@extends('layouts.app')
@section('title', 'Dashboard - Tazkiyah')
@section('header_title', 'Overview')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Assalamu Alaikum! 👋</h1>
        <p class="text-gray-500 mt-2">Here is your recent spiritual progress.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
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