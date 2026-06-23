@extends('layouts.app')

@section('title', 'Advanced Ibadah Tracker')

@section('content')
    <div class="max-w-5xl mx-auto" x-data="{
                                        fajr: '{{ $tracker->fajr ?? 'missed' }}',
                                        dhuhr: '{{ $tracker->dhuhr ?? 'missed' }}',
                                        asr: '{{ $tracker->asr ?? 'missed' }}',
                                        maghrib: '{{ $tracker->maghrib ?? 'missed' }}',
                                        isha: '{{ $tracker->isha ?? 'missed' }}',
                                        khushu: {{ $tracker->khushu_level ?? 5 }}
                                    }">

        <div
            class="flex flex-col md:flex-row justify-between items-center mb-8 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900">Today's Journey</h1>
                <p class="text-gray-500">Track your worship, build accountability, and grow your Imaan.</p>
            </div>
            <div class="bg-indigo-50 text-indigo-700 px-6 py-3 rounded-xl font-bold border border-indigo-100">
                {{ \Carbon\Carbon::now()->format('l, j F Y') }}
            </div>
        </div>

        <div
            class="mb-8 p-6 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl text-white shadow-lg flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold">Accountability Partner</h3>
                <p class="text-indigo-100 text-sm">Your current level: {{ auth()->user()->level ?? 'Beginner' }}</p>
            </div>

            <a href="{{ route('community.index') }}"
                class="bg-white/20 hover:bg-white/30 px-5 py-2.5 rounded-lg font-bold transition text-sm">
                Find Partner
            </a>
        </div>

        @if(isset($spiritualLesson))
            <div
                class="mb-8 p-6 rounded-3xl bg-gradient-to-r from-indigo-50 to-purple-50 border border-indigo-100/60 shadow-sm flex items-start gap-4">
                <div
                    class="h-10 w-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-lg shadow-md shrink-0">
                    📖</div>
                <div>
                    <h4 class="text-xs font-black text-indigo-600 uppercase tracking-wider">Spiritual Lesson of the Day</h4>
                    <p class="text-gray-700 font-medium text-sm mt-1 leading-relaxed italic">{{ $spiritualLesson }}</p>
                </div>
            </div>
        @endif

        <form action="{{ url('/tracker') }}" method="POST" class="space-y-6">
            @csrf

            <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                <h2 class="text-xl font-bold mb-6">Fardh Salah (Obligatory)</h2>
                @foreach(['Fajr', 'Dhuhr', 'Asr', 'Maghrib', 'Isha'] as $p)
                    @php $m = strtolower($p); @endphp
                    <div
                        class="flex flex-col md:flex-row items-center justify-between py-4 border-b border-gray-50 last:border-0">
                        <span class="font-bold text-gray-800 w-24">{{ $p }}</span>
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
                            <p class="text-xs text-gray-500 mt-1">To be recited between Fajr and Sunrise.</p>
                        </div>
                        <div class="mt-4">
                            <label
                                class="flex items-center gap-3 bg-white p-3 rounded-xl border border-amber-200/60 cursor-pointer shadow-sm">
                                <input type="checkbox" name="morning_adhkar" value="1" {{ (isset($tracker) && $tracker->morning_adhkar) ? 'checked' : '' }}
                                    class="w-5 h-5 rounded text-amber-600 focus:ring-amber-500 border-gray-300">
                                <span class="text-sm font-bold text-gray-700">Completed (আলহামদুলিল্লাহ, আজ পড়েছি)</span>
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
                            <p class="text-xs text-gray-500 mt-1">To be recited between Asr and Maghrib.</p>
                        </div>
                        <div class="mt-4">
                            <label
                                class="flex items-center gap-3 bg-white p-3 rounded-xl border border-indigo-200/60 cursor-pointer shadow-sm">
                                <input type="checkbox" name="evening_adhkar" value="1" {{ (isset($tracker) && $tracker->evening_adhkar) ? 'checked' : '' }}
                                    class="w-5 h-5 rounded text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                <span class="text-sm font-bold text-gray-700">Completed (আলহামদুলিল্লাহ, আজ পড়েছি)</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 flex flex-col">
                    <h3 class="font-bold mb-6">Sunnah & Sadaqah</h3>
                    <div class="grid grid-cols-1 gap-3">
                        @foreach(['tahajjud' => 'Tahajjud', 'witr' => 'Witr', 'sadaqah' => 'Sadaqah (Charity)', 'duwa' => 'Daily Duwa'] as $key => $label)
                            <label
                                class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl cursor-pointer hover:bg-indigo-50 transition border border-transparent hover:border-indigo-100">
                                <input type="checkbox" name="{{ $key }}" value="1" {{ (isset($tracker) && $tracker->$key) ? 'checked' : '' }} class="w-5 h-5 rounded text-indigo-600 border-gray-300">
                                <span class="text-sm font-bold text-gray-700">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 flex flex-col justify-between">
                    <h3 class="font-bold text-xl mb-8">Focus & Quran</h3>

                    <div class="mb-10">
                        <label class="block text-base font-bold text-gray-800 mb-5">
                            Khushu Level: <span x-text="khushu" class="text-indigo-600 text-2xl"></span>/10
                        </label>
                        <input type="range" name="khushu_level" x-model="khushu" min="1" max="10"
                            class="w-full h-3 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
                    </div>

                    <div>
                        <label class="block text-base font-bold text-gray-800 mb-4">Quran Recitation (Pages)</label>
                        <input type="number" name="quran_pages" value="{{ $tracker->quran_pages ?? 0 }}"
                            class="w-full p-5 bg-gray-50 rounded-xl border border-gray-200 text-lg font-semibold focus:border-indigo-500 outline-none">
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
        <div
            class="bg-white rounded-3xl max-w-2xl w-full max-h-[85vh] flex flex-col shadow-2xl border border-gray-100 animate-in fade-in zoom-in-95 duration-200">

            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-3xl">
                <div>
                    <h3 id="modal-title" class="text-xl font-black text-gray-900">Morning Adhkar</h3>
                    <p class="text-xs text-gray-400 font-bold mt-0.5 uppercase tracking-wide">Authentic Prophetic
                        Supplications</p>
                </div>
                <button type="button" onclick="closeAdhkarModal()"
                    class="text-gray-400 hover:text-gray-600 bg-gray-200/60 p-2 rounded-full transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-6 space-y-6 custom-scrollbar" id="adhkar-content">
            </div>

            <div class="p-4 bg-gray-50 border-t border-gray-100 flex justify-end rounded-b-3xl">
                <button type="button" onclick="closeAdhkarModal()"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl font-black text-sm transition shadow-sm">
                    Got it, Close
                </button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        const adhkarData = {
            morning: [
                {
                    name: "১. আয়াতুল কুরসি (১ বার)",
                    arabic: "اللَّهُ لَا إِلَٰهَ إِلَّا هُوَ الْحَيُّ الْقَيُّومُ ۚ لَا تَأْخُذُهُ سِنَةٌ وَلَا نَوْمٌ ۚ لَّهُ مَا فِي السَّمَاوَاتِ وَمَا فِي الْأَرْضِ ۗ مَن ذَا الَّذِي يَشْفَعُ عِندَهُ إِلَّا بِإِذْنِهِ ۚ يَعْلَمُ مَا بَيْنَ أَيْدِيهِمْ وَمَا خَلْفَهُمْ ۖ وَلَا يُحِيطُونَ بِشَيْءٍ مِّنْ عِلْمِهِ إِلَّا بِمَا شَاءَ ۚ وَسِعَ كُرْسِيُّهُ السَّمَاوَاتِ وَالْأَرْضَ ۖ وَلَا يَئُودُهُ حِفْظُهُمَا ۚ وَهُوَ الْعَلِيُّ الْعَظِيمُ",
                    pronunciation: "Allahu la ilaha illa Huwa, Al-Haiyul-Qaiyum. La ta'khudhuhu sinatun wa la nawm, lahu ma fis-samawati wa ma fil-'ard. Man dhal-ladhi yashfa'u 'indahu illa bi-idhnihi. Ya'lamu ma bayna aydihim wa ma khalfahum, wa la yuhituna bi shai'im-min 'ilmihi illa bima sha'a. Wasi'a kursiyuhus-samawati wal-ard, wa la ya'uduhu hifzhuhuma Wa Huwal 'Aliyul-Azheem.",
                    meaning_en: "Allah! There is no deity except Him, the Ever-Living, the Sustainer of [all] existence. Neither drowsiness overtakes Him nor sleep. To Him belongs whatever is in the heavens and whatever is on the earth. Who is it that can intercede with Him except by His permission? He knows what is [presently] before them and what will be after them, and they encompass not a thing of His knowledge except for what He wills. His Kursi extends over the heavens and the earth, and their preservation tires Him not. And He is the Most High, the Most Great.",
                    meaning_bn: "আল্লাহ, তিনি ছাড়া কোনো সত্য উপাস্য নেই। তিনি চিরঞ্জীব, সর্বসত্তার ধারক। তাঁকে তন্দ্রাও স্পর্শ করতে পারে না, নিদ্রাও নয়। আসমান ও জমিনে যা কিছু রয়েছে সবকিছু তাঁরই। কে সে, যে তাঁর অনুমতি ব্যতীত তাঁর কাছে সুপারিশ করবে? তাদের সামনে ও পেছনে যা কিছু আছে তা তিনি জানেন। আর যা তিনি ইচ্ছে করেন তা ছাড়া তাঁর জ্ঞানের কিছুই তারা আয়ত্ত করতে পারে না। তাঁর 'কুরসী' আসমান ও জমিন পরিব্যাপ্ত করে আছে; আর এ দুটোর রক্ষণাবেক্ষণ তাঁকে ক্লান্ত করে না। তিনি সর্বোচ্চ, সর্বাপেক্ষা মহান।",
                    reward: "ফজিলত: সকালে পড়লে সন্ধ্যা পর্যন্ত শয়তানের হাত থেকে নিরাপদে থাকবে। (হাকিম ১/৫৬২, সহীহ আত-তারগীব: ৬৬২)"
                },
                {
                    name: "২. সাইয়্যিদুল ইস্তিগফার (১ বার)",
                    arabic: "اللَّهُمَّ أَنْتَ رَبِّي لَا إِلَٰهَ إِلَّا أَنْتَ خَلَقْتَنِي وَأَنَا عَبْدُكَ وَأَنَا عَلَى عَهْدِكَ وَوَعْدِكَ مَا اسْتَطَعْتُ أَعُوذُ بِكَ مِنْ شَرِّ مَا صَنَعْتُ أَبُوءُ لَكَ بِنِعْمَتِكَ عَلَيَّ وَأَبُوءُ بِذَنْبِي فَاغْفِرْ لِي فَإِنَّهُ لَا يَغْفِرُ الذُّنُوبَ إِلَّا أَنْتَ",
                    pronunciation: "Allahumma anta Rabbi la ilaha illa anta, Khalaqtani wa ana 'Abduka, wa ana 'ala 'ahdika wa wa'dika mastata'tu, A'udhu bika min sharri ma sana'tu, abu'u Laka bini'matika 'alaiya, wa abu'u bidhanbi faghfirli fa innahu la yaghfirudh-dhunuba illa anta.",
                    meaning_en: "O Allah, You are my Lord, none has the right to be worshipped except You, You created me and I am Your servant and I abide to Your covenant and promise as best as I can, I take refuge in You from the evil of which I have committed. I acknowledge Your favour upon me and I acknowledge my sin, so forgive me, for verily none can forgive sin except You.",
                    meaning_bn: "হে আল্লাহ! আপনিই আমার রব। আপনি ছাড়া কোনো সত্য উপাস্য নেই। আপনি আমাকে সৃষ্টি করেছেন এবং আমি আপনার বান্দা। আমি আমার সাধ্যমতো আপনার দেওয়া ওয়াদা ও প্রতিশ্রুতির ওপর কায়েম আছি। আমি আমার কৃতকর্মের অনিষ্ট থেকে আপনার কাছে আশ্রয় চাই। আমার ওপর আপনার যে নেয়ামত আছে তার স্বীকৃতি দিচ্ছি এবং আমার পাপগুলোও স্বীকার করছি। সুতরাং আপনি আমাকে ক্ষমা করে দিন। কারণ আপনি ছাড়া আর কেউ পাপ ক্ষমা করতে পারে না।",
                    reward: "ফজিলত: দিনে বিশ্বাসের সাথে পড়লে এবং সন্ধ্যার আগে মারা গেলে সে জান্নাতী হবে। (সহীহ বুখারী: ৬৩০৬)"
                },
                {
                    name: "৩. ক্ষতি থেকে হেফাজতের দু'আ (৩ বার)",
                    arabic: "بِسْمِ اللَّهِ الَّذِي لَا يَضُرُّ مَعَ اسْمِهِ شَيْءٌ فِي الْأَرْضِ وَلَا فِي السَّمَاءِ وَهُوَ السَّمِيعُ الْعَلِيمُ",
                    pronunciation: "Bismillahil-ladhi la yadurru ma'as-mihi shai'un fil-ardi wa la fis-sama'i, wa Huwas-Sami'ul-'Alim.",
                    meaning_en: "In the Name of Allah with Whose Name there is protection against every kind of harm in the earth or in the heaven, and He is the All-Hearing and All-Knowing.",
                    meaning_bn: "আল্লাহর নামে, যাঁর নামের বরকতে আসমান ও জমিনের কোনো কিছুই কোনো ক্ষতি করতে পারে না এবং তিনি সর্বশ্রোতা, সর্বজ্ঞ।",
                    reward: "ফজিলত: সকাল-সন্ধ্যা ৩ বার পড়লে কোনো ক্ষতিকর বস্তু ক্ষতি করতে পারবে না। (আবু দাউদ: ৫০৮৮, তিরমিযী: ৩৩৮৮)"
                }
            ],
            evening: [
                {
                    name: "১. আয়াতুল কুরসি (১ বার)",
                    arabic: "اللَّهُ لَا إِلَٰهَ إِلَّا هُوَ الْحَيُّ الْقَيُّومُ ۚ لَا تَأْخُذُهُ سِنَةٌ وَلَا نَوْمٌ ۚ لَّهُ مَا فِي السَّمَاوَاتِ وَمَا فِي الْأَرْضِ ۗ مَن ذَا الَّذِي يَشْفَعُ عِندَهُ إِلَّا بِإِذْنِهِ ۚ يَعْلَمُ مَا بَيْنَ أَيْدِيهِمْ وَمَا خَلْفَهُمْ ۖ وَلَا يُحِيطُونَ بِشَيْءٍ مِّنْ عِلْمِهِ إِلَّا بِمَا شَاءَ ۚ وَسِعَ كُرْسِيُّهُ السَّمَاوَاتِ وَالْأَرْضَ ۖ وَلَا يَئُودُهُ حِفْظُهُمَا ۚ وَهُوَ الْعَلِيُّ الْعَظِيمُ",
                    pronunciation: "Allahu la ilaha illa Huwa, Al-Haiyul-Qaiyum. La ta'khudhuhu sinatun wa la nawm, lahu ma fis-samawati wa ma fil-'ard. Man dhal-ladhi yashfa'u 'indahu illa bi-idhnihi. Ya'lamu ma bayna aydihim wa ma khalfahum, wa la yuhituna bi shai'im-min 'ilmihi illa bima sha'a. Wasi'a kursiyuhus-samawati wal-ard, wa la ya'uduhu hifzhuhuma Wa Huwal 'Aliyul-Azheem.",
                    meaning_en: "Allah! There is no deity except Him, the Ever-Living, the Sustainer of [all] existence. Neither drowsiness overtakes Him nor sleep. To Him belongs whatever is in the heavens and whatever is on the earth. Who is it that can intercede with Him except by His permission? He knows what is [presently] before them and what will be after them, and they encompass not a thing of His knowledge except for what He wills. His Kursi extends over the heavens and the earth, and their preservation tires Him not. And He is the Most High, the Most Great.",
                    meaning_bn: "আল্লাহ, তিনি ছাড়া কোনো সত্য উপাস্য নেই। তিনি চিরঞ্জীব, সর্বসত্তার ধারক। তাঁকে তন্দ্রাও স্পর্শ করতে পারে আরা, নিদ্রাও নয়। আসমান ও জমিনে যা কিছু রয়েছে সবকিছু তাঁরই। কে সে, যে তাঁর অনুমতি ব্যতীত তাঁর কাছে সুপারিশ করবে? তাদের সামনে ও পেছনে যা কিছু আছে তা তিনি জানেন। আর যা তিনি ইচ্ছে করেন তা ছাড়া তাঁর জ্ঞানের কিছুই তারা আয়ত্ত করতে পারে না। তাঁর 'কুরসী' আসমান ও জমিন পরিব্যাপ্ত করে আছে; আর এ দুটোর রক্ষণাবেক্ষণ তাঁকে ক্লান্ত করে না। তিনি সর্বোচ্চ, সর্বাপেক্ষা মহান।",
                    reward: "ফজিলত: সন্ধ্যায় পড়লে সকাল পর্যন্ত শয়তানের হাত থেকে নিরাপদে থাকবে। (সহীহ আত-তারগীব: ৬৬২)"
                },
                {
                    name: "২. সাইয়্যিদুল ইস্তিগফার (১ বার)",
                    arabic: "اللَّهُمَّ أَنْتَ رَبِّي لَا إِلَٰهَ إِلَّا أَنْتَ خَلَقْتَنِي وَأَنَا عَبْدُكَ وَأَنَا عَلَى عَهْدِكَ وَوَعْدِكَ مَا اسْتَطَعْتُ أَعُوذُ بِكَ مِنْ شَرِّ مَا صَنَعْتُ أَبُوءُ لَكَ بِنِعْمَتِكَ عَلَيَّ وَأَبُوءُ بِذَنْبِي فَاغْفِرْ لِي فَإِنَّهُ لَا يَغْفِرُ الذُّنُوبَ إِلَّا أَنْتَ",
                    pronunciation: "Allahumma anta Rabbi la ilaha illa anta, Khalaqtani wa ana 'Abduka, wa ana 'ala 'ahdika wa wa'dika mastata'tu, A'udhu bika min sharri ma sana'tu, abu'u Laka bini'matika 'alaiya, wa abu'u bidhanbi faghfirli fa innahu la yaghfirudh-dhunuba illa anta.",
                    meaning_en: "O Allah, You are my Lord, none has the right to be worshipped except You, You created me and I am Your servant and I abide to Your covenant and promise as best as I can, I take refuge in You from the evil of which I have committed. I acknowledge Your favour upon me and I acknowledge my sin, so forgive me, for verily none can forgive sin except You.",
                    meaning_bn: "হে আল্লাহ! আপনিই আমার রব। আপনি ছাড়া কোনো সত্য উপাস্য নেই। আপনি আমাকে সৃষ্টি করেছেন এবং আমি আপনার বান্দা। আমি আমার সাধ্যমতো আপনার দেওয়া ওয়াদা ও প্রতিশ্রুতির ওপর কায়েম আছি। আমি আমার কৃতকর্মের অনিষ্ট থেকে আপনার কাছে আশ্রয় চাই। আমার ওপর আপনার যে নেয়ামত আছে তার স্বীকৃতি দিচ্ছি এবং আমার পাপগুলোও স্বীকার করছি। সুতরাং আপনি আমাকে ক্ষমা করে দিন। কারণ আপনি ছাড়া আর কেউ পাপ ক্ষমা করতে পারে না।",
                    reward: "ফজিলত: সন্ধ্যায় পড়ার পর রাতে মারা গেলে সে জান্নাতী হবে। (সহীহ বুখারী: ৬৩০৬)"
                },
                {
                    name: "৩. অনিষ্ট থেকে আশ্রয়ের দু'আ (৩ বার)",
                    arabic: "أَعُوذُ بِكَلِمَاتِ اللَّهِ التَّامَّاتِ مِنْ شَرِّ مَا خَلَقَ",
                    pronunciation: "A'udhu bikalimatillahit-tammati min sharri ma khalaq.",
                    meaning_en: "I seek refuge in the Perfect Words of Allah from the evil of what He has created.",
                    meaning_bn: "আল্লাহর নিখুঁত বাণীসমূহের উসিলায় আমি তাঁর সৃষ্টির সকল অনিষ্ট ও ক্ষতি থেকে আশ্রয় চাচ্ছি।",
                    reward: "ফজিলত: সন্ধ্যায় ৩ বার পড়লে ওই রাতে তাকে কোনো বিষাক্ত কীট বা প্রাণী ক্ষতি করতে পারবে না। (সহীহ মুসলিম: ২৭০৯, তিরমিযী: ৩৬০৪)"
                }
            ]
        };
        function openAdhkarModal(type) {
            const modal = $('#adhkar-modal');
            const title = $('#modal-title');
            const content = $('#adhkar-content');

            title.text(type === 'morning' ? 'Morning Adhkar (সকালের জিকির)' : 'Evening Adhkar (সন্ধ্যার জিকির)');
            content.empty();

            adhkarData[type].forEach(item => {
                let html = `
                            <div class="bg-gray-50/80 p-5 rounded-2xl border border-gray-100 space-y-4 shadow-inner">
                                <h4 class="font-black text-indigo-600 text-[16px]">${item.name}</h4>
                                <p class="text-2xl text-right font-bold text-gray-800 leading-relaxed font-arabic" dir="rtl" style="line-height: 1.8;">${item.arabic}</p>

                                <div class="bg-white p-3 rounded-xl border border-gray-100 space-y-2">
                                    <p class="text-xs font-bold text-gray-500"><span class="text-gray-400">Pronunciation:</span> ${item.pronunciation}</p>
                                    <p class="text-sm text-gray-700 font-medium"><span class="font-bold text-gray-900">Eng:</span> ${item.meaning_en}</p>
                                    <p class="text-sm text-gray-700 font-medium"><span class="font-bold text-gray-900">বাংলা:</span> ${item.meaning_bn}</p>
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