<div class="bg-white p-6 md:p-8 rounded-2xl shadow-sm border border-gray-100 transition-all hover:shadow-md mb-6">

    <!-- Hadith Numbering & Grade -->
    <div class="flex justify-between items-start mb-6 border-b border-gray-50 pb-4">
        <span class="bg-blue-100 text-blue-700 font-black px-4 py-1.5 rounded-lg text-sm shrink-0">
            Hadith No: {{ $hadith->hadith_number ?? 'N/A' }}
        </span>
        <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-lg">
            {{ $hadith->grade ?? 'Sahih' }}
        </span>
    </div>

    <!-- Arabic Text (Using the global class) -->
    <div dir="rtl" class="w-full border-b border-gray-50 mb-6 pb-6">
        <p class="arabic-text">
            {{ $hadith->arabic_text }}
        </p>
    </div>

    <!-- 🟢 UPDATED: Bangla Translation Text -->
    <p class="text-gray-800 text-[20px] md:text-[22px] leading-[2.2] font-medium mb-4">
        {{ $hadith->bangla_text }}
    </p>

    <!-- Action Buttons -->
    <div class="flex flex-wrap items-center gap-3 mt-8 pt-5 border-t border-gray-50">
        @if(!$hadith->isReadBy(auth()->id()))
            <!-- AJAX Point Claim Button (No Form!) -->
            <button type="button" id="btn-hadith-claim-{{ $hadith->id }}"
                onclick="claimHadithPoints({{ $hadith->id }}, this, {{ $hadith->points ?? 5 }})"
                class="text-sm font-bold text-white bg-blue-600 px-6 py-3 rounded-xl hover:bg-blue-700 transition flex items-center gap-2 ml-auto shadow-sm">
                <span>Claim {{ $hadith->points ?? 5 }} Points</span>
            </button>
        @else
            <!-- Already Claimed State -->
            <button disabled
                class="text-sm font-bold text-white bg-teal-500 px-6 py-3 rounded-xl flex items-center gap-2 ml-auto shadow-sm cursor-not-allowed">
                <span>{{ $hadith->points ?? 5 }} Points Earned ✅</span>
            </button>
        @endif
    </div>
</div>