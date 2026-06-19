<div
    class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 relative overflow-hidden transition-all hover:shadow-md">
    <div
        class="absolute top-6 right-6 bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-xs font-black tracking-wide border border-emerald-200">
        {{ $hadith->grade }}
    </div>

    <div class="mt-8 mb-8 text-right">
        <p class="text-3xl leading-relaxed text-gray-800 font-bold" dir="rtl" style="line-height: 2;">
            {{ $hadith->arabic_text }}
        </p>
    </div>

    <hr class="border-gray-50 my-6">

    <div class="space-y-4">
        <div>
            <h4 class="text-sm font-black text-indigo-600 uppercase tracking-wide mb-2">Bangla Translation</h4>
            <p class="text-gray-700 text-lg leading-relaxed">{{ $hadith->bangla_text }}</p>
        </div>

        @if($hadith->english_text)
            <div class="pt-4 border-t border-dashed border-gray-100 mt-4">
                <h4 class="text-sm font-black text-indigo-400 uppercase tracking-wide mb-2">English Translation</h4>
                <p class="text-gray-600 leading-relaxed">{{ $hadith->english_text }}</p>
            </div>
        @endif
    </div>

    @if($hadith->explanation)
        <div class="mt-8 bg-amber-50/50 p-6 rounded-2xl border border-amber-100/50">
            <h4 class="flex items-center gap-2 text-sm font-black text-amber-700 uppercase tracking-wide mb-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Authentic Explanation
            </h4>
            <p class="text-gray-800 leading-relaxed text-[15px] whitespace-pre-line">{{ $hadith->explanation }}</p>
        </div>
    @endif

    <div class="mt-8 pt-6 border-t border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3 text-sm font-bold text-gray-500">
            <span class="bg-gray-100 px-3 py-1.5 rounded-lg text-gray-700">{{ $hadith->reference }}</span>
            @if($hadith->source_url)
                <a href="{{ $hadith->source_url }}" target="_blank"
                    class="text-indigo-600 hover:underline flex items-center gap-1">
                    Source <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                </a>
            @endif
        </div>

        <form action="{{ route('hadiths.read', $hadith->id) }}" method="POST">
            @csrf
            @if($hadith->isReadBy(auth()->id()))
                <button type="button" disabled
                    class="bg-gray-100 text-gray-400 px-6 py-2.5 rounded-xl font-bold flex items-center gap-2 cursor-not-allowed">
                    <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                            clip-rule="evenodd"></path>
                    </svg>
                    Read & Understood
                </button>
            @else
                <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl font-black transition shadow-md flex items-center gap-2 group">
                    I have read & understood this
                    <span class="bg-indigo-800 text-indigo-100 text-[10px] px-2 py-0.5 rounded-full ml-1">
                        +{{ $hadith->points }} Pts
                    </span>
                </button>
            @endif
        </form>
    </div>
</div>