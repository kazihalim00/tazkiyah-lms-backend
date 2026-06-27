@extends('layouts.app') @section('content')
    <div class="max-w-3xl mx-auto mt-10 px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-gray-900 to-indigo-800 text-white text-center py-5 shadow-inner">
                <h4 class="text-xl sm:text-2xl font-bold mb-0 tracking-wide">Sadaqah & Deeni Fund</h4>
            </div>

            <div class="p-6 sm:p-8">
                @if(session('success'))
                    <div class="bg-indigo-50 border-l-4 border-indigo-600 text-indigo-800 p-4 mb-6 rounded shadow-sm font-semibold text-center"
                        role="alert">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="bg-indigo-50/50 border border-indigo-100 text-center p-6 rounded-xl mb-8 shadow-sm">
                    <p class="text-indigo-900 font-semibold mb-3">Please Send Money to our personal bKash account:</p>

                    <div
                        class="flex flex-col sm:flex-row items-center justify-center space-y-3 sm:space-y-0 sm:space-x-4 mb-2">
                        <h3 class="text-3xl sm:text-4xl text-indigo-700 font-black tracking-wider" id="bkashNumber">
                            01848486570</h3>

                        <button type="button" onclick="copyNumber()"
                            class="text-sm bg-white border border-indigo-200 hover:bg-indigo-100 text-indigo-700 font-bold py-1.5 px-4 rounded-full transition-all inline-flex items-center focus:outline-none shadow-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                                </path>
                            </svg>
                            <span id="copyText">Copy</span>
                        </button>
                    </div>

                    <p class="text-sm text-gray-600 mt-4">Use <span
                            class="font-bold text-gray-900 bg-gray-200 px-2 py-1 rounded">"Tazkiyah"</span> as reference.
                        After sending, please fill out the form below.</p>
                </div>

                <form action="{{ route('donate.pay') }}" method="POST">
                    @csrf

                    <div class="mb-5">
                        <label for="donation_sector" class="block text-gray-700 text-sm font-bold mb-2">Select Sector <span
                                class="text-rose-500">*</span></label>
                        <select id="donation_sector" name="donation_sector" required
                            class="shadow-sm border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-600 focus:border-indigo-600 block w-full p-3 outline-none transition duration-200">
                            <option value="" disabled selected>Where would you like to donate?</option>
                            <option value="Sadaqah Jariyah">Sadaqah Jariyah</option>
                            <option value="System Development">System Development Fund</option>
                            <option value="Orphan Help">Help for Orphans & Needy</option>
                            <option value="Dawah Work">Dawah & Other Islamic Activities</option>
                        </select>
                    </div>

                    <div class="mb-5">
                        <label for="amount" class="block text-gray-700 text-sm font-bold mb-2">Amount (BDT) <span
                                class="text-rose-500">*</span></label>
                        <input type="number" id="amount" name="amount" min="10" placeholder="e.g. 500" required
                            class="shadow-sm border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-600 focus:border-indigo-600 block w-full p-3 outline-none transition duration-200">
                    </div>

                    <div class="mb-5">
                        <label for="sender_number" class="block text-gray-700 text-sm font-bold mb-2">Your bKash Number
                            <span class="text-rose-500">*</span></label>
                        <input type="text" id="sender_number" name="sender_number" placeholder="e.g. 017XXXXXXXX" required
                            class="shadow-sm border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-600 focus:border-indigo-600 block w-full p-3 outline-none transition duration-200">
                    </div>

                    <div class="mb-8">
                        <label for="trx_id" class="block text-gray-700 text-sm font-bold mb-2">Transaction ID (TrxID) <span
                                class="text-rose-500">*</span></label>
                        <input type="text" id="trx_id" name="trx_id" placeholder="e.g. 8N34567890" required
                            class="shadow-sm border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-600 focus:border-indigo-600 block w-full p-3 uppercase outline-none transition duration-200">
                    </div>

                    <button type="submit"
                        class="w-full text-white bg-gradient-to-r from-gray-900 to-indigo-800 hover:from-gray-800 hover:to-indigo-700 focus:ring-4 focus:ring-indigo-300 font-bold rounded-xl text-lg px-5 py-4 text-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        Submit Donation Info
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function copyNumber() {
            // Get the number text
            var numberText = document.getElementById("bkashNumber").innerText;

            // Copy to clipboard
            navigator.clipboard.writeText(numberText).then(function () {
                var copyTextElement = document.getElementById("copyText");

                // Change text to 'Copied!'
                copyTextElement.innerText = "Copied!";

                // Revert back to 'Copy' after 2 seconds
                setTimeout(function () {
                    copyTextElement.innerText = "Copy";
                }, 2000);
            }).catch(function (err) {
                console.error('Failed to copy: ', err);
            });
        }
    </script>
@endsection