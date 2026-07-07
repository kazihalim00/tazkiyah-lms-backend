@extends('layouts.app')

@section('title', $category->name_bn)
@section('header_title', $category->name_bn)

@section('content')
    <!-- 🟢 Arabic Font Styling -->
    <style>
        @font-face {
            font-family: 'KFGQPC Uthmanic Script';
            src: url('https://fonts.cdnfonts.com/s/73253/KFGQPC_Uthmanic_Script_HAFS_Regular.woff') format('woff');
        }

        .arabic-text {
            font-family: 'KFGQPC Uthmanic Script', Arial, sans-serif;
        }
    </style>

    <div class="max-w-4xl mx-auto py-8">

        {{-- Back Button & Category Title with Points --}}
        <div class="flex items-center gap-4 mb-6 bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
            <a href="{{ route('hadiths.index') }}"
                class="text-gray-400 hover:text-indigo-600 transition bg-gray-50 p-2 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
            </a>
            <h1 class="text-2xl font-black text-gray-900">{{ $category->name_bn }}</h1>

            <!-- 🟢 Live Points Display -->
            <div class="ml-auto bg-blue-50 px-4 py-2 rounded-xl font-bold flex flex-col items-end border border-blue-100">
                <span class="text-[10px] text-blue-500 uppercase tracking-widest">Total Points</span>
                <span class="text-lg text-blue-700" id="nav-total-points">{{ auth()->user()->total_points }}</span>
            </div>
        </div>

        {{-- Sub-categories (Chapters) Section --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-emerald-600 text-white p-5 font-bold flex justify-between items-center">
                <span class="flex items-center gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    {{ $category->name_bn }} ({{ $category->name_en ?? 'Chapters' }})
                </span>
                <span class="bg-emerald-800 px-3 py-1 rounded-full text-xs font-black">{{ $subCategories->count() }}
                    Chapters</span>
            </div>

            <div class="divide-y divide-gray-100">
                @foreach($subCategories as $sub)
                    <a href="{{ route('hadiths.chapter', $sub->id) }}"
                        class="block hover:bg-emerald-50/50 transition p-5 group">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-gray-300 group-hover:text-emerald-500" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path
                                        d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z">
                                    </path>
                                </svg>
                                <h3 class="font-bold text-gray-800 text-lg group-hover:text-emerald-700">{{ $sub->name_bn }}
                                </h3>
                            </div>
                            <span
                                class="text-xs font-bold text-gray-500 bg-gray-100 group-hover:bg-emerald-100 group-hover:text-emerald-700 px-3 py-1.5 rounded-lg">
                                {{ $sub->hadiths_count }} Hadiths
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Uncategorized Hadiths Section --}}
        @if($uncategorizedHadiths->count() > 0)
            <div class="mt-10 space-y-4">
                <h3
                    class="font-bold text-gray-500 uppercase tracking-wider text-sm mb-4 bg-white py-2 px-4 rounded-lg shadow-sm border border-gray-100 inline-block">
                    Hadiths in this Category
                </h3>

                <!-- 🟢 Include the updated partial -->
                @foreach($uncategorizedHadiths as $hadith)
                    @include('partials.hadith_card', ['hadith' => $hadith])
                @endforeach

                <div class="mt-8">
                    {{ $uncategorizedHadiths->links() }}
                </div>
            </div>
        @endif
    </div>
@endsection

<!-- 🟢 AJAX Script for Claiming Points without Reload -->
@push('scripts')
    <script>
        const csrfToken = '{{ csrf_token() }}';

        async function claimHadithPoints(hadithId, btnElement, points) {
            // Disable button and show loading state
            btnElement.disabled = true;
            const originalText = btnElement.innerHTML;
            btnElement.innerHTML = 'Wait... ⏳';
            btnElement.classList.replace('bg-blue-600', 'bg-gray-400');

            try {
                // Send AJAX request
                const response = await fetch(`/hadiths/${hadithId}/read`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    // Update button UI on success
                    btnElement.innerHTML = `${points} Points Earned ✅`;
                    btnElement.classList.replace('bg-gray-400', 'bg-teal-500');
                    btnElement.classList.remove('hover:bg-blue-700');

                    // Update top bar points immediately
                    const pointsDisplay = document.getElementById('nav-total-points');
                    if (pointsDisplay) {
                        pointsDisplay.innerText = data.new_total;
                    }
                } else {
                    btnElement.disabled = false;
                    btnElement.innerHTML = originalText;
                    btnElement.classList.replace('bg-gray-400', 'bg-blue-600');
                    alert(data.message || 'Error claiming points.');
                }
            } catch (error) {
                console.error('Fetch Error:', error);
                btnElement.disabled = false;
                btnElement.innerHTML = originalText;
                btnElement.classList.replace('bg-gray-400', 'bg-blue-600');
            }
        }
    </script>
@endpush