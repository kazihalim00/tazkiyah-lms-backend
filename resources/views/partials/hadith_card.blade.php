<div class="bg-white p-6 md:p-8 rounded-3xl shadow-sm border border-gray-100 relative mb-6">

    {{-- Grade Badge --}}
    <div
        class="absolute top-6 right-6 bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-xs font-black tracking-wide border border-emerald-200">
        {{ $hadith->grade ?? 'সহীহ' }}
    </div>

    {{-- Hadith Number --}}
    <div class="mb-4">
        <span class="bg-indigo-50 text-indigo-600 px-3 py-1 rounded-lg text-xs font-bold border border-indigo-100">
            হাদিস নং: {{ $hadith->hadith_number }}
        </span>
    </div>

    {{-- Arabic Text --}}
    <div class="mt-6 mb-8 text-right">
        <p class="text-2xl md:text-3xl leading-loose text-gray-800 font-bold" dir="rtl" style="line-height: 2.2;">
            {{ $hadith->arabic_text }}
        </p>
    </div>

    <hr class="border-gray-50 my-6">

    {{-- Translations --}}
    <div class="space-y-4">
        <div>
            <h4 class="text-xs font-black text-emerald-600 uppercase tracking-wider mb-2">বাংলা অনুবাদ</h4>
            <p class="text-gray-700 text-lg leading-relaxed">{{ $hadith->bangla_text }}</p>
        </div>

        @if($hadith->english_text)
            <div class="pt-4">
                <h4 class="text-xs font-black text-indigo-400 uppercase tracking-wider mb-2">English Translation</h4>
                <p class="text-gray-600 leading-relaxed">{{ $hadith->english_text }}</p>
            </div>
        @endif
    </div>

    {{-- Scholar Explanation --}}
    @if($hadith->explanation)
        <div class="mt-8 bg-amber-50/50 p-6 rounded-2xl border border-amber-100/50">
            <h4 class="flex items-center gap-2 text-sm font-black text-amber-700 uppercase tracking-wide mb-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                স্কলারদের ব্যাখ্যা (Explanation)
            </h4>
            <p class="text-gray-800 leading-relaxed text-[15px] whitespace-pre-line">{{ $hadith->explanation }}</p>
        </div>
    @endif

    {{-- Footer: Reference & Action --}}
    <div class="mt-8 pt-6 border-t border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3 text-sm font-bold text-gray-500">
            <span class="bg-gray-100 px-3 py-1.5 rounded-lg text-gray-700">{{ $hadith->reference }}</span>
        </div>

        <form action="{{ route('hadiths.read', $hadith->id) }}" method="POST">
            @csrf
            @if($hadith->isReadBy(auth()->id()))
                <button type="button" disabled
                    class="bg-gray-100 text-gray-400 px-6 py-2.5 rounded-xl font-bold flex items-center gap-2 cursor-not-allowed w-full md:w-auto justify-center">
                    <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                            clip-rule="evenodd"></path>
                    </svg>
                    Read & Understood
                </button>
            @else
                <button type="submit"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-xl font-black transition shadow-md flex items-center gap-2 group w-full md:w-auto justify-center">
                    I have read this
                    <span class="bg-emerald-800 text-emerald-100 text-[10px] px-2 py-0.5 rounded-full ml-1">
                        +{{ $hadith->points }} Pts
                    </span>
                </button>
            @endif
        </form>
    </div>
</div>