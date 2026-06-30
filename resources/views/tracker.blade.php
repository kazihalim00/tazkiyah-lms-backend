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

        <div
            class="flex flex-col md:flex-row justify-between items-center mb-8 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900">Today's Journey</h1>
                <p class="text-gray-500">Track your worship, build accountability, and grow your Imaan.</p>
            </div>
            <div class="bg-indigo-50 text-indigo-700 px-6 py-3 rounded-xl font-bold border border-indigo-100 mt-4 md:mt-0">
                {{ \Carbon\Carbon::now()->format('l, j F Y') }}
            </div>
        </div>

        <form action="{{ url('/tracker') }}" method="POST" class="space-y-6">
            @csrf

            <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                <h2 class="text-xl font-bold mb-6">Fardh Salah (Obligatory)</h2>
                @foreach(['Fajr', 'Dhuhr', 'Asr', 'Maghrib', 'Isha'] as $p)
                    @php $m = strtolower($p); @endphp
                    <div
                        class="flex flex-col md:flex-row items-center justify-between py-4 border-b border-gray-50 last:border-0">
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

            <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                <h2 class="text-xl font-bold mb-6">Daily Adhkar (Morning & Evening)</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-amber-50/40 p-5 rounded-2xl border border-amber-100 flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-center mb-3">
                                <span
                                    class="bg-amber-100 text-amber-800 text-xs font-black px-2.5 py-1 rounded-full uppercase">Morning</span>
                                <button type="button" onclick="openAdhkarModal('morning')"
                                    class="text-xs font-bold text-indigo-600 hover:underline flex items-center gap-1">
                                    📖 View Adhkar
                                </button>
                            </div>
                            <h3 class="text-lg font-black text-gray-800">Morning Adhkar (সকালের জিকির)</h3>
                        </div>
                        <div class="mt-4">
                            <label
                                class="flex items-center gap-3 bg-white p-3 rounded-xl border border-amber-200/60 cursor-pointer shadow-sm">
                                <input type="checkbox" name="morning_adhkar" value="1" {{ (isset($tracker) && $tracker->morning_adhkar) ? 'checked' : '' }}
                                    class="w-5 h-5 rounded text-amber-600 border-gray-300">
                                <span class="text-sm font-bold text-gray-700">Completed (আলহামদুলিল্লাহ, আজ পড়েছি)</span>
                            </label>
                        </div>
                    </div>

                    <div class="bg-indigo-50/40 p-5 rounded-2xl border border-indigo-100 flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-center mb-3">
                                <span
                                    class="bg-indigo-100 text-indigo-800 text-xs font-black px-2.5 py-1 rounded-full uppercase">Evening</span>
                                <button type="button" onclick="openAdhkarModal('evening')"
                                    class="text-xs font-bold text-indigo-600 hover:underline flex items-center gap-1">
                                    📖 View Adhkar
                                </button>
                            </div>
                            <h3 class="text-lg font-black text-gray-800">Evening Adhkar (সন্ধ্যার জিকির)</h3>
                        </div>
                        <div class="mt-4">
                            <label
                                class="flex items-center gap-3 bg-white p-3 rounded-xl border border-indigo-200/60 cursor-pointer shadow-sm">
                                <input type="checkbox" name="evening_adhkar" value="1" {{ (isset($tracker) && $tracker->evening_adhkar) ? 'checked' : '' }}
                                    class="w-5 h-5 rounded text-indigo-600 border-gray-300">
                                <span class="text-sm font-bold text-gray-700">Completed (আলহামদুলিল্লাহ, আজ পড়েছি)</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 flex flex-col">
                    <h3 class="font-bold mb-6">Sunnah, Sadaqah & Du'a</h3>
                    <div class="grid grid-cols-1 gap-3">
                        @foreach(['tahajjud' => 'Tahajjud', 'witr' => 'Witr', 'sadaqah' => 'Sadaqah (Charity)', 'duwa' => 'Daily Duwa'] as $key => $label)
                            <div
                                class="flex items-center justify-between p-3 bg-gray-50 rounded-xl hover:bg-indigo-50 transition border border-transparent hover:border-indigo-100">
                                <label class="flex items-center gap-3 cursor-pointer w-full">
                                    <input type="checkbox" name="{{ $key }}" value="1" {{ (isset($tracker) && $tracker->$key) ? 'checked' : '' }} class="w-5 h-5 rounded text-indigo-600 border-gray-300">
                                    <span class="text-sm font-bold text-gray-700">{{ $label }}</span>
                                </label>

                                @if($key === 'duwa')
                                    <button type="button" onclick="openAdhkarModal('duwa')"
                                        class="text-xs font-bold text-indigo-600 hover:underline flex items-center gap-1 shrink-0">
                                        📖 View
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 space-y-6">
                    <h3 class="text-lg font-black text-gray-800 tracking-tight">Focus & Islamic Book</h3>

                    <div class="space-y-2">
                        <label class="flex justify-between text-sm font-bold text-gray-700">
                            <span>Khushu Level</span>
                            <span class="text-indigo-600 font-black"><span
                                    id="khushu-val">{{ $tracker->khushu_level ?? 5 }}</span>/10</span>
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

            <button type="submit"
                class="w-full bg-gray-900 text-white py-5 rounded-2xl font-bold hover:bg-indigo-600 transition shadow-xl text-lg">
                Save Today's Progress
            </button>
        </form>
    </div>

    <div id="adhkar-modal"
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-2xl w-full max-h-[85vh] flex flex-col shadow-2xl border border-gray-100">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-3xl">
                <div>
                    <h3 id="modal-title" class="text-xl font-black text-gray-900">Morning Adhkar</h3>
                </div>
                <button type="button" onclick="closeAdhkarModal()"
                    class="text-gray-400 hover:text-gray-600 bg-gray-200/60 p-2 rounded-full transition">
                    ✖
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-6 space-y-6" id="adhkar-content"></div>
            <div class="p-4 bg-gray-50 border-t border-gray-100 flex justify-end rounded-b-3xl">
                <button type="button" onclick="closeAdhkarModal()"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl font-black text-sm">Got it,
                    Close</button>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        const adhkarData = {
            morning: [
                {
                    name: "১. আয়াতুল কুরসি (১ বার)",
                    arabic: "اللَّهُ لَا إِلَٰهَ إِلَّا هُوَ الْحَيُّ الْقَيُّومُ ۚ لَا تَأْخُذُهُ سِنَةٌ وَلَا نَوْمٌ ۚ لَّهُ مَا فِي السَّمَاوَاتِ وَمَا فِي الْأَرْضِ ۗ مَن ذَا الَّذِي يَشْفَعُ عِندَهُ إِلَّا بِإِذْنِهِ ۚ يَعْلَمُ مَا بَيْنَ أَيْدِيهِمْ وَمَا خَلْفَهُمْ ۖ وَلَا يُحِيطُونَ بِشَيْءٍ مِّنْ عِلْمِهِ إِلَّا بِمَا شَاءَ ۚ وَسِعَ كُرْسِيُّهُ السَّمَاوَاتِ وَالْأَرْضَ ۖ وَلَا يَئُودُهُ حِفْظُهُمَا ۚ وَهُوَ الْعَلِيُّ الْعَظِيمُ",
                    pronunciation: "Allahu la ilaha illa Huwa, Al-Haiyul-Qaiyum. La ta'khudhuhu sinatun wa la nawm, lahu ma fis-samawati wa ma fil-'ard. Man dhal-ladhi yashfa'u 'indahu illa bi-idhnihi. Ya'lamu ma bayna aydihim wa ma khalfahum, wa la yuhituna bi shai'im-min 'ilmihi illa bima sha'a. Wasi'a kursiyuhus-samawati wal-ard, wa la ya'uduhu hifzhuhuma Wa Huwal 'Aliyul-Azheem.",
                    meaning_en: "Allah! There is no deity except Him, the Ever-Living, the Sustainer of [all] existence. Neither drowsiness overtakes Him nor sleep. To Him belongs whatever is in the heavens and whatever is on the earth...",
                    meaning_bn: "আল্লাহ, তিনি ছাড়া কোনো সত্য উপাস্য নেই। তিনি চিরঞ্জীব, সর্বসত্তার ধারক। তাঁকে তন্দ্রাও স্পর্শ করতে পারে না, নিদ্রাও নয়। আসমান ও জমিনে যা কিছু রয়েছে সবকিছু তাঁরই...",
                    reward: "দলিল: যে ব্যক্তি সকালে এটি পাঠ করবে, সে সন্ধ্যা পর্যন্ত শয়তানের হাত থেকে নিরাপদে থাকবে। (হাকিম ১/৫৬২, সহীহ আত-তারগীব: ৬৬২)"
                },
                {
                    name: "২. সাইয়্যিদুল ইস্তিগফার (শ্রেষ্ঠ ক্ষমা প্রার্থনা) (১ বার)",
                    arabic: "اللَّهُمَّ أَنْتَ رَبِّي لَا إِلَٰهَ إِلَّا أَنْتَ خَلَقْتَنِي وَأَنَا عَبْدُكَ وَأَنَا عَلَى عَهْدِكَ وَوَعْدِكَ مَا اسْتَطَعْتُ أَعُوذُ بِكَ مِنْ شَرِّ مَا صَنَعْتُ أَبُوءُ لَكَ بِنِعْمَتِكَ عَلَيَّ وَأَبُوءُ بِذَنْبِي فَاغْفِرْ لِي فَإِنَّهُ لَا يَغْفِرُ الذُّنُوبَ إِلَّا أَنْتَ",
                    pronunciation: "Allahumma anta Rabbi la ilaha illa anta, Khalaqtani wa ana 'Abduka, wa ana 'ala 'ahdika wa wa'dika mastata'tu, A'udhu bika min sharri ma sana'tu, abu'u Laka bini'matika 'alaiya, wa abu'u bidhanbi faghfirli fa innahu la yaghfirudh-dhunuba illa anta.",
                    meaning_en: "O Allah, You are my Lord, none has the right to be worshipped except You, You created me and I am Your servant and I abide to Your covenant and promise as best as I can...",
                    meaning_bn: "হে আল্লাহ! আপনিই আমার রব। আপনি ছাড়া কোনো সত্য উপাস্য নেই। আপনি আমাকে সৃষ্টি করেছেন এবং আমি আপনার বান্দা। আমি আমার সাধ্যমতো আপনার দেওয়া ওয়াদা ও প্রতিশ্রুতির ওপর কায়েম আছি...",
                    reward: "দলিল: দিনে বিশ্বাসের সাথে পড়লে এবং সন্ধ্যার আগে মারা গেলে সে জান্নাতী হবে। (সহীহ বুখারী: ৬৩০৬)"
                },
                {
                    name: "৩. সকালের বিশেষ দুআ (১ বার)",
                    arabic: "اللَّهُمَّ بِكَ أَصْبَحْنَا، وَبِكَ أَمْسَيْنَا، وَبِكَ نَحْيَا، وَبِكَ نَمُوتُ وَإِلَيْكَ النُّشُورُ",
                    pronunciation: "Allahumma bika asbahna wa bika amsayna, wa bika nahya, wa bika namutu, wa ilaikan-nushur.",
                    meaning_en: "O Allah, by You we enter the morning and by You we enter the evening, by You we live and by You we die, and to You is the Final Return.",
                    meaning_bn: "হে আল্লাহ! আপনার হুকুমেই আমরা সকালে উপনীত হয়েছি, আপনার হুকুমেই আমরা সন্ধ্যায় উপনীত হয়েছি। আপনার হুকুমেই আমরা বেঁচে থাকি, আপনার হুকুমেই আমরা মৃত্যুবরণ করি এবং আপনার দিকেই আমাদের প্রত্যাবর্তন।",
                    reward: "দলিল: সুনান আবু দাউদ (৫০৬৮), তিরমিযী (৩৩৯১)"
                },
                {
                    name: "৪. ক্ষতি থেকে হেফাজতের দুআ (৩ বার)",
                    arabic: "بِسْمِ اللَّهِ الَّذِي لَا يَضُرُّ مَعَ اسْمِهِ شَيْءٌ فِي الْأَرْضِ وَلَا فِي السَّمَاءِ وَهُوَ السَّمِيعُ الْعَلِيمُ",
                    pronunciation: "Bismillahil-ladhi la yadurru ma'as-mihi shai'un fil-ardi wa la fis-sama'i, wa Huwas-Sami'ul-'Alim.",
                    meaning_en: "In the Name of Allah with Whose Name there is protection against every kind of harm in the earth or in the heaven, and He is the All-Hearing and All-Knowing.",
                    meaning_bn: "আল্লাহর নামে, যাঁর নামের বরকতে আসমান ও জমিনের কোনো কিছুই কোনো ক্ষতি করতে পারে না এবং তিনি সর্বশ্রোতা, সর্বজ্ঞ।",
                    reward: "দলিল: সকাল-সন্ধ্যা ৩ বার পড়লে কোনো ক্ষতিকর বস্তু ক্ষতি করতে পারবে না। (আবু দাউদ: ৫০৮৮)"
                },
                {
                    name: "৫. সন্তুষ্টির দুআ (৩ বার)",
                    arabic: "رَضِيتُ بِاللَّهِ رَبًّا، وَبِالْإِسْلَامِ دِينًا، وَبِمُحَمَّدٍ صَلَّى اللَّهُ عَلَيْهِ وَسَلَّمَ نَبِيًّا",
                    pronunciation: "Raditu billahi Rabban, wa bil-Islami dinan, wa bi-Muhammadin (sallallahu 'alaihi wa sallama) Nabiyyan.",
                    meaning_en: "I am pleased with Allah as my Lord, with Islam as my religion, and with Muhammad (peace and blessings be upon him) as my Prophet.",
                    meaning_bn: "আমি আল্লাহকে রব হিসেবে, ইসলামকে দ্বীন হিসেবে এবং মুহাম্মাদ (ﷺ)-কে নবী হিসেবে পেয়ে সন্তুষ্ট।",
                    reward: "দলিল: যে ব্যক্তি সকাল-সন্ধ্যায় এটি ৩ বার বলবে, কিয়ামতের দিন আল্লাহ তাকে সন্তুষ্ট করবেন। (তিরমিযী: ৩৩৮৯)"
                }
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
                {
                    name: "২. সাইয়্যিদুল ইস্তিগফার (শ্রেষ্ঠ ক্ষমা প্রার্থনা) (১ বার)",
                    arabic: "اللَّهُمَّ أَنْتَ رَبِّي لَا إِلَٰهَ إِلَّا أَنْتَ خَلَقْتَنِي وَأَنَا عَبْدُكَ وَأَنَا عَلَى عَهْدِكَ وَوَعْدِكَ مَا اسْتَطَعْتُ أَعُوذُ بِكَ مِنْ شَرِّ مَا صَنَعْتُ أَبُوءُ لَكَ بِنِعْمَتِكَ عَلَيَّ وَأَبُوءُ بِذَنْبِي فَاغْفِرْ لِي فَإِنَّهُ لَا يَغْفِرُ الذُّنُوبَ إِلَّا أَنْتَ",
                    pronunciation: "Allahumma anta Rabbi la ilaha illa anta, Khalaqtani wa ana 'Abduka, wa ana 'ala 'ahdika wa wa'dika mastata'tu...",
                    meaning_en: "O Allah, You are my Lord, none has the right to be worshipped except You, You created me and I am Your servant...",
                    meaning_bn: "হে আল্লাহ! আপনিই আমার রব। আপনি ছাড়া কোনো সত্য উপাস্য নেই। আপনি আমাকে সৃষ্টি করেছেন এবং আমি আপনার বান্দা...",
                    reward: "দলিল: সন্ধ্যায় পড়ার পর রাতে মারা গেলে সে জান্নাতী হবে। (সহীহ বুখারী: ৬৩০৬)"
                },
                {
                    name: "৩. সন্ধ্যার বিশেষ দুআ (১ বার)",
                    arabic: "اللَّهُمَّ بِكَ أَمْسَيْنَا، وَبِكَ أَصْبَحْنَا، وَبِكَ نَحْيَا، وَبِكَ نَمُوتُ وَإِلَيْكَ الْمَصِيرُ",
                    pronunciation: "Allahumma bika amsayna, wa bika asbahna, wa bika nahya, wa bika namutu, wa ilaikal-masir.",
                    meaning_en: "O Allah, by You we enter the evening and by You we enter the morning, by You we live and by You we die, and to You is the Final Destination.",
                    meaning_bn: "হে আল্লাহ! আপনার হুকুমেই আমরা সন্ধ্যায় উপনীত হয়েছি, আপনার হুকুমেই আমরা সকালে উপনীত হয়েছি। আপনার হুকুমেই আমরা বেঁচে থাকি, আপনার হুকুমেই আমরা মৃত্যুবরণ করি এবং আপনার দিকেই আমাদের ফিরে যেতে হবে।",
                    reward: "দলিল: সুনান আবু দাউদ (৫০৬৮), তিরমিযী (৩৩৯১)"
                },
                {
                    name: "৪. অনিষ্ট থেকে আশ্রয়ের দুআ (৩ বার)",
                    arabic: "أَعُوذُ بِكَلِمَاتِ اللَّهِ التَّامَّاتِ مِنْ شَرِّ مَا خَلَقَ",
                    pronunciation: "A'udhu bikalimatillahit-tammati min sharri ma khalaq.",
                    meaning_en: "I seek refuge in the Perfect Words of Allah from the evil of what He has created.",
                    meaning_bn: "আল্লাহর নিখুঁত বাণীসমূহের উসিলায় আমি তাঁর সৃষ্টির সকল অনিষ্ট ও ক্ষতি থেকে আশ্রয় চাচ্ছি।",
                    reward: "দলিল: সন্ধ্যায় ৩ বার পড়লে ওই রাতে তাকে কোনো বিষাক্ত কীট বা প্রাণী ক্ষতি করতে পারবে না। (সহীহ মুসলিম: ২৭০৯)"
                }
            ],
            // 🟢 ১০০% সহীহ ও প্রামাণ্য দুআ সমূহ
            duwa: [
                {
                    name: "১. ঘুম থেকে ওঠার পর",
                    arabic: "الْحَمْدُ لِلَّهِ الَّذِي أَحْيَانَا بَعْدَ مَا أَمَاتَنَا وَإِلَيْهِ النُّشُورُ",
                    pronunciation: "Alhamdu lillahil-ladhi ahyana ba'da ma amatana wa ilaihin-nushur.",
                    meaning_en: "All praise is for Allah who gave us life after having taken it from us and unto Him is the resurrection.",
                    meaning_bn: "সকল প্রশংসা আল্লাহর জন্য, যিনি আমাদেরকে মৃত্যু (ঘুম) দেওয়ার পর পুনরায় জীবিত করেছেন এবং তাঁরই দিকে আমাদের প্রত্যাবর্তন।",
                    reward: "দলিল: সহীহ বুখারী (৬৩১২)"
                },
                {
                    name: "২. ঘুমানোর আগে",
                    arabic: "بِاسْمِكَ اللَّهُمَّ أَمُوتُ وَأَحْيَا",
                    pronunciation: "Bismikallahumma amutu wa ahya.",
                    meaning_en: "In Your name, O Allah, I die and I live.",
                    meaning_bn: "হে আল্লাহ! আপনার নামেই আমি মৃত্যুবরণ করি (ঘুমাই) এবং আপনার নামেই জীবিত (জাগ্রত) হই।",
                    reward: "দলিল: সহীহ বুখারী (৬৩২৪)"
                },
                {
                    name: "৩. ঘর থেকে বের হওয়ার সময়",
                    arabic: "بِسْمِ اللَّهِ تَوَكَّلْتُ عَلَى اللَّهِ، وَلَا حَوْلَ وَلَا قُوَّةَ إِلَّا بِاللَّهِ",
                    pronunciation: "Bismillahi, tawakkaltu 'alal-lahi, wa la hawla wa la quwwata illa billah.",
                    meaning_en: "In the name of Allah, I place my trust in Allah, and there is no might nor power except with Allah.",
                    meaning_bn: "আল্লাহর নামে, আমি আল্লাহর ওপর ভরসা করলাম। আর আল্লাহর সাহায্য ছাড়া পাপ থেকে বাঁচার ও নেক কাজ করার কোনো শক্তি নেই।",
                    reward: "দলিল: সুনান আবু দাউদ (৫০৯৫)"
                },
                {
                    name: "৪. টয়লেটে প্রবেশের আগে",
                    arabic: "اللَّهُمَّ إِنِّي أَعُوذُ بِكَ مِنَ الْخُبُثِ وَالْخَبَائِثِ",
                    pronunciation: "Allahumma inni a'udhu bika minal-khubuthi wal-khaba'ith.",
                    meaning_en: "O Allah, I seek refuge in You from the evil male and female devils.",
                    meaning_bn: "হে আল্লাহ! আমি আপনার কাছে অপবিত্র পুরুষ ও নারী শয়তান থেকে আশ্রয় প্রার্থনা করছি।",
                    reward: "দলিল: সহীহ বুখারী (১৪২)"
                },
                {
                    name: "৫. দুনিয়া ও আখেরাতের সার্বিক কল্যাণের দুআ",
                    arabic: "رَبَّنَا آتِنَا فِي الدُّنْيَا حَسَنَةً وَفِي الآخِرَةِ حَسَنَةً وَقِنَا عَذَابَ النَّارِ",
                    pronunciation: "Rabbana atina fid-dunya hasanatan wa fil-akhirati hasanatan waqina 'adhaban-nar.",
                    meaning_en: "Our Lord, give us in this world [that which is] good and in the Hereafter [that which is] good and protect us from the punishment of the Fire.",
                    meaning_bn: "হে আমাদের রব! আমাদেরকে দুনিয়াতে কল্যাণ দিন এবং আখেরাতেও কল্যাণ দিন এবং আমাদেরকে জাহান্নামের আযাব থেকে রক্ষা করুন।",
                    reward: "দলিল: সহীহ বুখারী (৪৫২২)"
                },
                {
                    name: "৬. ঈমানের ওপর অবিচল থাকার দুআ",
                    arabic: "يَا مُقَلِّبَ الْقُلُوبِ ثَبِّتْ قَلْبِي عَلَى دِينِكَ",
                    pronunciation: "Ya Muqallibal-qulub, thabbit qalbi 'ala dinik.",
                    meaning_en: "O Turner of the hearts, keep my heart steadfast on Your religion.",
                    meaning_bn: "হে অন্তরসমূহ পরিবর্তনকারী! আমার অন্তরকে আপনার দ্বীনের ওপর অবিচল রাখুন।",
                    reward: "দলিল: সুনান আত-তিরমিযী (৩৫২২)"
                },
                {
                    name: "৭. ইলম, রিযিক ও আমল কবুলের দুআ (ফজরের পর)",
                    arabic: "اللَّهُمَّ إِنِّي أَسْأَلُكَ عِلْمًا نَافِعًا، وَرِزْقًا طَيِّبًا، وَعَمَلًا مُتَقَبَّلًا",
                    pronunciation: "Allahumma inni as'aluka 'ilman nafi'an, wa rizqan tayyiban, wa 'amalan mutaqabbalan.",
                    meaning_en: "O Allah, I ask You for beneficial knowledge, goodly provision and acceptable deeds.",
                    meaning_bn: "হে আল্লাহ! আমি আপনার কাছে উপকারী জ্ঞান, পবিত্র রিযিক এবং কবুলযোগ্য আমল প্রার্থনা করছি।",
                    reward: "দলিল: সুনান ইবনে মাজাহ (৯২৫)"
                }
            ]
        };

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