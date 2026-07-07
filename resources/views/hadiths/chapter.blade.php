@extends('layouts.app')
@section('title', $subCategory->name_bn ?? 'Hadith Chapter')
@section('header_title', 'Hadith Collection')

@section('content')
    <!-- Global Arabic Font for beautiful typography -->
    <style>
        @font-face {
            font-family: 'KFGQPC Uthmanic Script';
            src: url('https://fonts.cdnfonts.com/s/73253/KFGQPC_Uthmanic_Script_HAFS_Regular.woff') format('woff');
        }

        .arabic-text {
            font-family: 'KFGQPC Uthmanic Script', Arial, sans-serif !important;
            font-size: 42px !important;
            line-height: 2.6 !important;
            direction: rtl;
            text-align: right;
            color: #111827 !important;
            padding: 10px 0;
        }
    </style>

    <div class="max-w-4xl mx-auto py-8">
        <!-- Sticky Header with Real-time Points Display -->
        <div
            class="sticky top-0 z-50 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl shadow-lg p-6 text-white mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold">{{ $subCategory->name_bn ?? 'Chapter' }}</h1>
                <p class="text-blue-100 mt-1">{{ $subCategory->category->name_bn ?? 'Category' }}</p>
            </div>
            <div class="bg-white/20 px-4 py-2 rounded-xl font-bold flex flex-col items-end">
                <span class="text-xs text-blue-100 uppercase tracking-widest">Total Points</span>
                <!-- Target ID for instant point updates -->
                <span class="text-xl" id="nav-total-points">{{ auth()->user()->total_points }}</span>
            </div>
        </div>

        <div class="space-y-6">
            @forelse($hadiths as $hadith)
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 transition-all hover:shadow-md">

                    <!-- Hadith Numbering & Grade -->
                    <div class="flex justify-between items-start mb-4 border-b border-gray-50 pb-4">
                        <span class="bg-blue-100 text-blue-700 font-black px-4 py-1.5 rounded-lg text-sm shrink-0">
                            Hadith No: {{ $hadith->hadith_number ?? $loop->iteration }}
                        </span>
                        <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-lg">
                            {{ $hadith->grade ?? 'Sahih' }}
                        </span>
                    </div>

                    <!-- Arabic & Bangla Texts -->
                    <p class="arabic-text text-gray-800 text-right w-full mb-6">{{ $hadith->arabic_text }}</p>
                    <p class="text-gray-600 text-lg leading-relaxed">{{ $hadith->bangla_text }}</p>

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap items-center gap-3 mt-6 pt-4 border-t border-gray-50">
                        @if(!$hadith->isReadBy(auth()->id()))
                            <!-- AJAX Point Claim Button -->
                            <button id="btn-hadith-claim-{{ $hadith->id }}"
                                onclick="claimHadithPoints({{ $hadith->id }}, this, {{ $hadith->points ?? 5 }})"
                                class="text-sm font-bold text-white bg-blue-600 px-5 py-2.5 rounded-xl hover:bg-blue-700 transition flex items-center gap-2 ml-auto shadow-sm">
                                <span>Claim {{ $hadith->points ?? 5 }} Points</span>
                            </button>
                        @else
                            <!-- Already Claimed State -->
                            <button disabled
                                class="text-sm font-bold text-white bg-teal-500 px-5 py-2.5 rounded-xl flex items-center gap-2 ml-auto shadow-sm cursor-not-allowed">
                                <span>{{ $hadith->points ?? 5 }} Points Earned ✅</span>
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-10 bg-white rounded-2xl border border-gray-100">
                    <p class="text-gray-500 font-bold">No hadiths found in this chapter.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection

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

                    // Update top bar points immediately without reloading
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