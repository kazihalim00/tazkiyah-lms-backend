@extends('layouts.app')

@section('title', 'Advanced Ibadah Tracker')

@section('content')
    @php
        // Helper function to map database values properly to the frontend buttons
        function mapPrayer($val)
        {
            if (!$val)
                return 'missed';
            $val = strtolower($val);
            if (in_array($val, ['jamaah', 'jamaah_mosque']))
                return 'jamaah_mosque';
            if ($val === 'jamaah_home')
                return 'jamaah_home';
            if (in_array($val, ['individual', 'alone']))
                return 'alone';
            if ($val === 'qada')
                return 'qada';
            return 'missed';
        }
    @endphp

    <div class="max-w-5xl mx-auto" x-data="{
                                        fajr: '{{ mapPrayer($tracker->fajr ?? '') }}',
                                        dhuhr: '{{ mapPrayer($tracker->dhuhr ?? '') }}',
                                        asr: '{{ mapPrayer($tracker->asr ?? '') }}',
                                        maghrib: '{{ mapPrayer($tracker->maghrib ?? '') }}',
                                        isha: '{{ mapPrayer($tracker->isha ?? '') }}'
                                    }">

        <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-8 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900">Today's Journey</h1>
                    <p class="text-gray-500">Track your worship, build accountability, and grow your Imaan.</p>
                </div>
                <div class="bg-indigo-50 text-indigo-700 px-6 py-3 rounded-xl font-bold border border-indigo-100 mt-4 md:mt-0">
                    {{ \Carbon\Carbon::now()->format('l, j F Y') }}
                </div>
            </div>

            <!-- ========================================== -->
            <!-- 1. IBADAH TRACKER FORM                     -->
            <!-- ========================================== -->
            <form action="{{ url('/tracker') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Fardh Salah Tracking -->
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                    <h2 class="text-xl font-bold mb-6">Fardh Salah (Obligatory)</h2>
                    @foreach(['Fajr', 'Dhuhr', 'Asr', 'Maghrib', 'Isha'] as $p)
                        @php $m = strtolower($p); @endphp
                        <div class="flex flex-col md:flex-row items-center justify-between py-4 border-b border-gray-50 last:border-0">
                            <span class="font-bold text-gray-800 w-24 mb-3 md:mb-0">{{ $p }}</span>
                            <input type="hidden" name="{{ $m }}" x-model="{{ $m }}">
                            <div class="flex flex-wrap gap-2">
                                @foreach(['jamaah_mosque' => 'Mosque', 'jamaah_home' => 'Home', 'alone' => 'Alone', 'qada' => 'Qada', 'missed' => 'Missed'] as $val => $label)
                                    <button type="button" @click="{{ $m }} = '{{ $val }}'" :class="{
                                                                                            'bg-emerald-600 text-white shadow-md': {{ $m }} === '{{ $val }}' && '{{ $val }}' === 'jamaah_mosque',
                                                                                            'bg-teal-500 text-white shadow-md': {{ $m }} === '{{ $val }}' && '{{ $val }}' === 'jamaah_home',
                                                                                            'bg-blue-500 text-white shadow-md': {{ $m }} === '{{ $val }}' && '{{ $val }}' === 'alone',
                                                                                            'bg-orange-500 text-white shadow-md': {{ $m }} === '{{ $val }}' && '{{ $val }}' === 'qada',
                                                                                            'bg-red-500 text-white shadow-md': {{ $m }} === '{{ $val }}' && '{{ $val }}' === 'missed',
                                                                                            'bg-gray-100 text-gray-600': {{ $m }} !== '{{ $val }}'
                                                                                        }"
                                        class="px-4 py-2 text-xs font-bold rounded-lg transition-all">{{ $label }}</button>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Daily Adhkar Tracking -->
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                    <h2 class="text-xl font-bold mb-6">Daily Adhkar (Morning & Evening)</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Morning Adhkar -->
                        <div class="bg-amber-50/40 p-5 rounded-2xl border border-amber-100 flex flex-col justify-between">
                            <div>
                                <div class="flex justify-between items-center mb-3">
                                    <span class="bg-amber-100 text-amber-800 text-xs font-black px-2.5 py-1 rounded-full uppercase">Morning</span>
                                    <button type="button" onclick="openAdhkarModal('morning')" class="text-xs font-bold text-indigo-600 hover:underline flex items-center gap-1">
                                        📖 View Adhkar
                                    </button>
                                </div>
                                <h3 class="text-lg font-black text-gray-800">Morning Adhkar (সকালের জিকির)</h3>
                            </div>
                            <div class="mt-4">
                                <label class="flex items-center gap-3 bg-white p-3 rounded-xl border border-amber-200/60 cursor-pointer shadow-sm">
                                    <input type="checkbox" name="morning_adhkar" value="1" {{ (isset($tracker) && $tracker->morning_adhkar) ? 'checked' : '' }} class="w-5 h-5 rounded text-amber-600 border-gray-300">
                                    <span class="text-sm font-bold text-gray-700">Completed (আলহামদুলিল্লাহ, আজ পড়েছি)</span>
                                </label>
                            </div>
                        </div>

                        <!-- Evening Adhkar -->
                        <div class="bg-indigo-50/40 p-5 rounded-2xl border border-indigo-100 flex flex-col justify-between">
                            <div>
                                <div class="flex justify-between items-center mb-3">
                                    <span class="bg-indigo-100 text-indigo-800 text-xs font-black px-2.5 py-1 rounded-full uppercase">Evening</span>
                                    <button type="button" onclick="openAdhkarModal('evening')" class="text-xs font-bold text-indigo-600 hover:underline flex items-center gap-1">
                                        📖 View Adhkar
                                    </button>
                                </div>
                                <h3 class="text-lg font-black text-gray-800">Evening Adhkar (সন্ধ্যার জিকির)</h3>
                            </div>
                            <div class="mt-4">
                                <label class="flex items-center gap-3 bg-white p-3 rounded-xl border border-indigo-200/60 cursor-pointer shadow-sm">
                                    <input type="checkbox" name="evening_adhkar" value="1" {{ (isset($tracker) && $tracker->evening_adhkar) ? 'checked' : '' }} class="w-5 h-5 rounded text-indigo-600 border-gray-300">
                                    <span class="text-sm font-bold text-gray-700">Completed (আলহামদুলিল্লাহ, আজ পড়েছি)</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sunnah, Khushu & Book Tracking -->
                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Sunnah Deeds -->
                    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 flex flex-col">
                        <h3 class="font-bold mb-6">Sunnah, Sadaqah & Du'a</h3>
                        <div class="grid grid-cols-1 gap-3">
                            @foreach(['tahajjud' => 'Tahajjud', 'witr' => 'Witr', 'sadaqah' => 'Sadaqah (Charity)', 'duwa' => 'Daily Duwa'] as $key => $label)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl hover:bg-indigo-50 transition border border-transparent hover:border-indigo-100">
                                    <label class="flex items-center gap-3 cursor-pointer w-full">
                                        <input type="checkbox" name="{{ $key }}" value="1" {{ (isset($tracker) && $tracker->$key) ? 'checked' : '' }} class="w-5 h-5 rounded text-indigo-600 border-gray-300">
                                        <span class="text-sm font-bold text-gray-700">{{ $label }}</span>
                                    </label>

                                    @if($key === 'duwa')
                                        <button type="button" onclick="openAdhkarModal('duwa')" class="text-xs font-bold text-indigo-600 hover:underline flex items-center gap-1 shrink-0">
                                            📖 View
                                        </button>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Focus & Book Reading -->
                    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 space-y-6">
                        <h3 class="text-lg font-black text-gray-800 tracking-tight">Focus & Islamic Book</h3>
                        <div class="space-y-2">
                            <label class="flex justify-between text-sm font-bold text-gray-700">
                                <span>Khushu Level</span>
                                <span class="text-indigo-600 font-black"><span id="khushu-val">{{ $tracker->khushu_level ?? 5 }}</span>/10</span>
                            </label>
                            <input type="range" name="khushu_level" min="1" max="10" value="{{ $tracker->khushu_level ?? 5 }}"
                                class="w-full h-2 bg-gray-100 rounded-lg appearance-none cursor-pointer accent-indigo-600"
                                oninput="document.getElementById('khushu-val').innerText = this.value">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700">Islamic Book Reading (Pages)</label>
                            <input type="number" name="quran_pages" min="0" value="{{ $tracker->quran_pages ?? 0 }}"
                                class="w-full bg-gray-50/70 px-4 py-3 rounded-xl text-sm text-gray-800 border border-gray-200 focus:outline-none focus:border-indigo-300 transition font-bold">
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-gray-900 text-white py-5 rounded-2xl font-bold hover:bg-indigo-600 transition shadow-xl text-lg">
                    Save Today's Progress
                </button>
            </form>
            <!-- END IBADAH TRACKER FORM -->

            <!-- ========================================== -->
            <!-- 2. ACCOUNTABILITY PARTNER SECTION          -->
            <!-- ========================================== -->
            <div class="mt-12 bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h2 class="text-2xl font-extrabold text-gray-900">Accountability Partners</h2>
                        <p class="text-sm text-gray-500 mt-1">Connect with brothers/sisters to stay consistent on your journey.</p>
                    </div>
                    <!-- Link to full community page -->
                    <a href="{{ route('community.index') }}" class="text-indigo-600 font-bold text-sm hover:underline bg-indigo-50 px-4 py-2 rounded-lg">View Community</a>
                </div>

                <div class="grid md:grid-cols-2 gap-8">

                    <!-- Pending Requests Block -->
                    <div class="bg-indigo-50/40 p-6 rounded-2xl border border-indigo-100">
                        <h3 class="font-bold text-gray-800 mb-5 flex items-center gap-2">
                            📥 Pending Requests
                        </h3>

                        <!-- Check if there are any pending requests passed from the controller -->
                        @if(isset($pendingRequests) && $pendingRequests->count() > 0)
                            <div class="space-y-3">
                                @foreach($pendingRequests as $request)
                                    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <!-- User Avatar Placeholder -->
                                            <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-700 font-bold">
                                                {{ strtoupper(substr($request->user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-sm text-gray-800">{{ $request->user->name }}</h4>
                                                <p class="text-xs text-gray-500">Level: {{ $request->user->badge['name'] ?? 'Beginner' }}</p>
                                            </div>
                                        </div>
                                        <div class="flex gap-2">
                                            <!-- Accept Request Button -->
                                            <form action="{{ route('partner.accept', $request->user->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white w-8 h-8 rounded-lg transition flex items-center justify-center font-bold" title="Accept">
                                                    ✓
                                                </button>
                                            </form>
                                            <!-- Reject Request Button -->
                                            <form action="{{ route('partner.reject', $request->user->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white w-8 h-8 rounded-lg transition flex items-center justify-center font-bold" title="Reject">
                                                    ✕
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <!-- Empty State for Pending Requests -->
                            <div class="text-center py-8 text-gray-500 text-sm font-medium bg-white rounded-xl border border-dashed border-gray-200">
                                No pending requests right now.
                            </div>
                        @endif
                    </div>

                    <!-- Suggested Partners Block -->
                    <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100">
                        <h3 class="font-bold text-gray-800 mb-5 flex items-center gap-2">
                            🔍 Suggested Partners
                        </h3>

                        <!-- Check if there are suggested users passed from the controller -->
                        @if(isset($suggestedUsers) && $suggestedUsers->count() > 0)
                            <div class="space-y-3">
                                @foreach($suggestedUsers as $user)
                                    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <!-- User Avatar Placeholder -->
                                            <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-700 font-bold">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-sm text-gray-800">{{ $user->name }}</h4>
                                                <p class="text-xs text-gray-500">Points: {{ $user->total_points ?? 0 }}</p>
                                            </div>
                                        </div>
                                        <!-- Send Request Button -->
                                        <form action="{{ route('partner.request', $user->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="bg-gray-900 hover:bg-indigo-600 text-white text-xs font-bold px-4 py-2.5 rounded-lg transition">
                                                Connect
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <!-- Empty State for Suggested Partners -->
                            <div class="text-center py-8 text-gray-500 text-sm font-medium bg-white rounded-xl border border-dashed border-gray-200">
                                Explore the community to find partners.
                            </div>
                        @endif
                    </div>

                </div>
            </div>
            <!-- END ACCOUNTABILITY PARTNER SECTION -->

        </div>

        <!-- ========================================== -->
        <!-- 3. ADHKAR MODAL COMPONENT                  -->
        <!-- ========================================== -->
        <div id="adhkar-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-2xl w-full max-h-[85vh] flex flex-col shadow-2xl border border-gray-100">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-3xl">
                    <div>
                        <h3 id="modal-title" class="text-xl font-black text-gray-900">Morning Adhkar</h3>
                    </div>
                    <button type="button" onclick="closeAdhkarModal()" class="text-gray-400 hover:text-gray-600 bg-gray-200/60 p-2 rounded-full transition">
                        ✖
                    </button>
                </div>
                <div class="flex-1 overflow-y-auto p-6 space-y-6" id="adhkar-content"></div>
                <div class="p-4 bg-gray-50 border-t border-gray-100 flex justify-end rounded-b-3xl">
                    <button type="button" onclick="closeAdhkarModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl font-black text-sm">Got it, Close</button>
                </div>
            </div>
        </div>

        <!-- Scripts -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            const adhkarData = {
                morning: [
                    {
                        name: "১. আয়াতুল কুরসি (১ বার)",
                        arabic: "اللَّهُ لَا إِلَٰهَ إِلَّا هُوَ الْحَيُّ الْقَيُّومُ ۚ لَا تَأْخُذُهُ سِنَةٌ وَلَا نَوْمٌ ۚ لَّهُ مَا فِي السَّمَاوَاتِ وَمَا فِي الْأَرْضِ ۗ مَن ذَا الَّذِي يَشْفَعُ عِندَهُ إِلَّا بِإِذْنِهِ ۚ يَعْلَمُ مَا بَيْنَ أَيْدِيهِمْ وَمَا خَلْفَهُمْ ۖ وَلَا يُحِيطُونَ بِشَيْءٍ مِّنْ عِلْمِهِ إِلَّا بِمَا شَاءَ ۚ وَسِعَ كُرْسِيُّهُ السَّمَاوَاتِ وَالْأَرْضَ ۖ وَلَا يَئُودُهُ حِفْظُهُمَا ۚ وَهُوَ الْعَلِيُّ الْعَظِيمُ",
                        pronunciation: "Allahu la ilaha illa Huwa, Al-Haiyul-Qaiyum. La ta'khudhuhu sinatun wa la nawm, lahu ma fis-samawati wa ma fil-'ard. Man dhal-ladhi yashfa'u 'indahu illa bi-idhnihi. Ya'lamu ma bayna aydihim wa ma khalfahum, wa la yuhituna bi shai'im-min 'ilmihi illa bima sha'a. Wasi'a kursiyuhus-samawati wal-ard, wa la ya'uduhu hifzhuhuma Wa Huwal 'Aliyul-Azheem.",
                        meaning_en: "Allah! There is no deity except Him, the Ever-Living, the Sustainer of [all] existence. Neither drowsiness overtakes Him nor sleep. To Him belongs whatever is in the heavens and whatever is on the earth...",
                        meaning_bn: "আল্লাহ, তিনি ছাড়া কোনো সত্য উপাস্য নেই। তিনি চিরঞ্জীব, সর্বসত্তার ধারক। তাঁকে তন্দ্রাও স্পর্শ করতে পারে বায় না, নিদ্রাও নয়। আসমান ও জমিনে যা কিছু রয়েছে সবকিছু তাঁরই...",
                        reward: "দলিল: যে ব্যক্তি সকালে এটি পাঠ করবে, সে সন্ধ্যা পর্যন্ত শয়তানের হাত থেকে নিরাপদে থাকবে। (হাকিম ১/৫৬২, সহীহ আত-তারগীব: ৬৬২)"
                    },
                    // ... (আপনার আগের সব জিকিরের ডাটা এখানে থাকবে, আমি স্পেস বাঁচানোর জন্য ছোট করে দিলাম)
                ],
                evening: [
                    {
                        name: "১. আয়াতুল কুরসি (১ বার)",
                        arabic: "اللَّهُ لَا إِلَٰهَ إِلَّا هُوَ الْحَيُّ الْقَيُّومُ ۚ لَا تَأْخُذُهُ سِنَةٌ وَلَا نَوْمٌ ۚ لَّهُ مَا فِي السَّمَاوَاتِ وَمَا فِي الْأَرْضِ ۗ مَن ذَا الَّذِي يَشْفَعُ عِندَهُ إِلَّا بِإِذْنِهِ ۚ يَعْلَمُ مَا بَيْنَ أَيْدِيهِمْ وَمَا خَلْفَهُمْ ۖ وَلَا يُحِيطُونَ بِشَيْءٍ مِّنْ عِلْمِهِ إِلَّا بِمَا شَاءَ ۚ وَسِعَ كُرْسِيُّهُ السَّمَاوَاتِ وَالْأَرْضَ ۖ وَلَا يَئُودُهُ حِفْظُهُمَا ۚ وَهُوَ الْعَلِيُّ الْعَظِيمُ",
                        pronunciation: "Allahu la ilaha illa Huwa, Al-Haiyul-Qaiyum. La ta'khudhuhu sinatun wa la nawm, lahu ma fis-samawati wa ma fil-'ard...",
                        meaning_en: "Allah! There is no deity except Him, the Ever-Living, the Sustainer of [all] existence. Neither drowsiness overtakes Him nor sleep...",
                        meaning_bn: "আল্লাহ, তিনি ছাড়া কোনো সত্য উপাস্য নেই। তিনি চিরঞ্জীব, সর্বসত্তার ধারক। তাঁকে তন্দ্রাও স্পর্শ করতে পারে না, নিদ্রাও নয়...",
                        reward: "দলিল: সন্ধ্যায় পড়লে সকাল পর্যন্ত শয়তানের হাত থেকে নিরাপদে থাকবে। (সহীহ আত-তারগীব: ৬৬২)"
                    },
                ],
                duwa: [
                    {
                        name: "১. ঘুম থেকে ওঠার পর",
                        arabic: "الْحَمْدُ لِلَّهِ الَّذِي أَحْيَانَا بَعْدَ مَا أَمَاتَنَا وَإِلَيْهِ النُّشُورُ",
                        pronunciation: "Alhamdu lillahil-ladhi ahyana ba'da ma amatana wa ilaihin-nushur.",
                        meaning_en: "All praise is for Allah who gave us life after having taken it from us and unto Him is the resurrection.",
                        meaning_bn: "সকল প্রশংসা আল্লাহর জন্য, যিনি আমাদেরকে মৃত্যু (ঘুম) দেওয়ার পর পুনরায় জীবিত করেছেন এবং তাঁরই দিকে আমাদের প্রত্যাবর্তন।",
                        reward: "দলিল: সহীহ বুখারী (৬৩১২)"
                    }
                ]
            };

            // Modal Functionality Logic
            function openAdhkarModal(type) {
                const modal = $('#adhkar-modal');
                const title = $('#modal-title');
                const content = $('#adhkar-content');

                // Dynamic Title Logic
                if (type === 'morning') {
                    title.text('Morning Adhkar (সকালের জিকির)');
                } else if (type === 'evening') {
                    title.text('Evening Adhkar (সন্ধ্যার জিকির)');
                } else if (type === 'duwa') {
                    title.text('Essential Sunnah Du\'as (প্রয়োজনীয় মাসনূন দুআ)');
                }

                content.empty();

                adhkarData[type].forEach(item => {
                    let html = `
                        <div class="bg-gray-50/80 p-5 rounded-2xl border border-gray-100 space-y-4 shadow-inner">
                            <h4 class="font-black text-indigo-600 text-[16px]">${item.name}</h4>
                            <p class="text-2xl text-right font-bold text-gray-800 font-arabic leading-relaxed" dir="rtl" style="line-height: 1.8;">${item.arabic}</p>
                            <div class="bg-white p-3 rounded-xl border border-gray-100 space-y-2">
                                <p class="text-xs font-bold text-gray-500">Pronunciation: ${item.pronunciation}</p>
                                <p class="text-sm text-gray-700 font-medium">Eng: ${item.meaning_en}</p>
                                <p class="text-sm text-gray-700 font-medium">বাংলা: ${item.meaning_bn}</p>
                            </div>
                            <div class="text-[11px] font-black text-emerald-700 bg-emerald-50 px-3 py-2 rounded-lg border border-emerald-100 inline-block mt-2">
                                ${item.reward}
                            </div>
                        </div>
                    `;
                    content.append(html);
                });
                modal.removeClass('hidden');
            }

            function closeAdhkarModal() {
                $('#adhkar-modal').addClass('hidden');
            }
        </script>
@endsection