@extends('layouts.app')
@section('title', $surah->name_bangla . ' - Al-Quran')
@section('header_title', 'Surah ' . $surah->name_en)

@section('content')
    <!-- Global Uthmanic Font for proper Arabic rendering -->
    <style>
        @font-face {
            font-family: 'KFGQPC Uthmanic Script';
            src: url('https://fonts.cdnfonts.com/s/73253/KFGQPC_Uthmanic_Script_HAFS_Regular.woff') format('woff');
        }

        .quran-text {
            font-family: 'KFGQPC Uthmanic Script', Arial, sans-serif;
            font-size: 2.5rem;
            line-height: 2.5;
            direction: rtl;
        }

        /* Smooth scrolling for the whole page to avoid jumps */
        html {
            scroll-behavior: smooth;
        }
    </style>

    <div class="max-w-4xl mx-auto py-8">
        <!-- Sticky Header with Total Points -->
        <div
            class="sticky top-0 z-50 bg-gradient-to-r from-emerald-600 to-teal-600 rounded-2xl shadow-lg p-6 text-white mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold">{{ $surah->name_bangla }}</h1>
                <p class="text-emerald-100 mt-1">{{ $surah->name_en }} • {{ $surah->ayahs_count }} Ayahs</p>
            </div>
            <div class="bg-white/20 px-4 py-2 rounded-xl font-bold flex flex-col items-end">
                <span class="text-xs text-emerald-100 uppercase tracking-widest">Total Points</span>
                <span class="text-xl" id="nav-total-points">{{ auth()->user()->total_points }}</span>
            </div>
        </div>

        <div class="space-y-8">
            <!-- Loop through ayahs related to this surah -->
            <!-- Ensure your column names (arabic_text, bangla_text, tafsir_sadi) match your HadithBD database structure -->
            @foreach($surah->ayahs as $ayah)
                <div id="ayah-{{ $ayah->id }}"
                    class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 transition-all hover:shadow-md">

                    <div class="flex justify-between items-start gap-4">
                        <span class="bg-emerald-100 text-emerald-700 font-bold px-3 py-1 rounded-lg text-sm shrink-0">
                            {{ $surah->surah_no }}:{{ $ayah->ayah_no }}
                        </span>
                        <!-- Render Arabic Text -->
                        <p class="quran-text text-gray-800 text-right w-full">{{ $ayah->arabic_text }}</p>
                    </div>

                    <!-- Render Translation -->
                    <p class="text-gray-600 mt-6 text-lg">{{ $ayah->bangla_text }}</p>

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap items-center gap-3 mt-6 pt-4 border-t border-gray-50">

                        <!-- Toggle Tafseer Button -->
                        <button onclick="toggleTafseer({{ $ayah->id }})"
                            class="text-sm font-bold text-indigo-600 bg-indigo-50 px-4 py-2 rounded-xl hover:bg-indigo-100 transition">
                            📖 তাফসীর আস-সাদী
                        </button>

                        <!-- AJAX Point Claim Button -->
                        <!-- AJAX Point Claim Button -->
                        <button id="btn-claim-{{ $ayah->id }}" onclick="claimPoints({{ $ayah->id }}, this)"
                            class="text-sm font-bold text-white bg-emerald-600 px-4 py-2 rounded-xl hover:bg-emerald-700 transition flex items-center gap-2 ml-auto shadow-sm">
                            <span>Claim 5 Points</span>
                        </button>
                    </div>

                    <!-- Tafseer Container (Hidden by default) -->
                    <div id="tafseer-{{ $ayah->id }}" class="hidden mt-4 p-5 bg-amber-50 border border-amber-100 rounded-xl">
                        <h4 class="font-bold text-amber-900 mb-2">তাফসীর আস-সাদী:</h4>
                        <!-- Render Tafseer Text -->
                        <p class="text-gray-700 text-sm leading-relaxed">
                            {{ $ayah->tafsir_sadi ?? 'এই আয়াতের তাফসীর পাওয়া যায়নি।' }}
                        </p>
                    </div>

                </div>
            @endforeach
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const csrfToken = '{{ csrf_token() }}';

        // Auto-scroll to last read Ayah smoothly on page load
        document.addEventListener("DOMContentLoaded", function () {
            const lastReadId = {{ $lastReadAyahId ?? 'null' }};
            if (lastReadId) {
                const targetAyah = document.getElementById('ayah-' + lastReadId);
                if (targetAyah) {
                    setTimeout(() => {
                        targetAyah.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        // Highlight the saved Ayah briefly
                        targetAyah.classList.add('ring-2', 'ring-emerald-400', 'bg-emerald-50/50');
                    }, 800);
                }
            }
        });

        // Toggles visibility of Tafseer without reloading the page
        function toggleTafseer(ayahId) {
            const tafseerDiv = document.getElementById('tafseer-' + ayahId);
            tafseerDiv.classList.toggle('hidden');
        }

        // Fetch API to claim points and update UI instantly
        async function claimPoints(ayahId, btnElement) {

            // Disable button and change state to loading
            btnElement.disabled = true;
            const originalText = btnElement.innerHTML;
            btnElement.innerHTML = 'Please wait... ⏳';
            btnElement.classList.replace('bg-emerald-600', 'bg-gray-400');

            try {
                // Send AJAX request to claim points
                const response = await fetch(`/quran/ayah/${ayahId}/read`, {
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
                    btnElement.innerHTML = '5 Points Earned ✅';
                    btnElement.classList.replace('bg-gray-400', 'bg-teal-500');
                    btnElement.classList.remove('hover:bg-emerald-700');

                    // Update top bar total points text
                    document.getElementById('nav-total-points').innerText = data.new_total;

                    // Save this Ayah as the last read position
                    saveLastReadPosition(ayahId);
                } else {
                    // Revert button UI if failed or already claimed
                    btnElement.disabled = false;
                    btnElement.innerHTML = originalText;
                    btnElement.classList.replace('bg-gray-400', 'bg-emerald-600');
                    alert(data.message || 'Error claiming points.');
                }
            } catch (error) {
                console.error('Fetch Error:', error);
                btnElement.disabled = false;
                btnElement.innerHTML = originalText;
                btnElement.classList.replace('bg-gray-400', 'bg-emerald-600');
            }
        }

        // Send AJAX request to save last read position
        function saveLastReadPosition(ayahId) {
            fetch('{{ route("quran.save_last_read") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ ayah_id: ayahId })
            });
        }
    </script>
@endpush