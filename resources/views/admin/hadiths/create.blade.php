@extends('layouts.app')

@section('title', 'Add New Hadith')
@section('header_title', 'Admin Panel - Add Hadith')

@section('content')
    <div class="max-w-4xl mx-auto py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Add New Hadith</h1>
            <a href="{{ route('admin.hadiths.index') ?? '#' }}" class="text-indigo-600 font-bold hover:underline">&larr;
                Back</a>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded-xl shadow-sm mb-6 font-bold">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.hadiths.store') }}" method="POST"
            class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 space-y-6">
            @csrf

            <div class="bg-gray-50 p-5 rounded-2xl border border-gray-200">
                <h3 class="font-bold text-gray-800 mb-4">Category Selection</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Select Existing Category</label>
                        <select name="category_id" id="category-select"
                            class="w-full border-gray-200 rounded-xl p-3 focus:ring-indigo-500">
                            <option value="">-- Choose Category --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name_bn }} ({{ $category->name_en }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="border-l-2 border-indigo-100 pl-4">
                        <label class="block text-sm font-bold text-indigo-600 mb-2">OR Create New Category</label>
                        <input type="text" name="new_category_bn" placeholder="Category Name (Bangla) e.g. আক্বীদা"
                            class="w-full border-gray-200 rounded-xl p-3 mb-2 focus:ring-indigo-500">
                        <input type="text" name="new_category_en" placeholder="Category Name (English) e.g. Aqeeda"
                            class="w-full border-gray-200 rounded-xl p-3 focus:ring-indigo-500">
                    </div>
                </div>
            </div>

            <div class="bg-blue-50/50 p-5 rounded-2xl border border-blue-100">
                <h3 class="font-bold text-blue-800 mb-4">Chapter / Sub Category (পরিচ্ছেদ)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Select Existing Chapter</label>
                        <select name="sub_category_id" id="sub-category-select"
                            class="w-full border-gray-200 rounded-xl p-3 focus:ring-blue-500">
                            <option value="">-- Choose Chapter --</option>
                        </select>
                    </div>
                    <div class="border-l-2 border-blue-200 pl-4">
                        <label class="block text-sm font-bold text-blue-600 mb-2">OR Create New Chapter</label>
                        <input type="text" name="new_sub_category_bn"
                            placeholder="Chapter Name (Bangla) e.g. সাধারণ হাদিসসমূহ"
                            class="w-full border-gray-200 rounded-xl p-3 mb-2 focus:ring-blue-500">
                        <input type="text" name="new_sub_category_en" placeholder="Chapter Name (English)"
                            class="w-full border-gray-200 rounded-xl p-3 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Arabic Text <span
                        class="text-red-500">*</span></label>
                <textarea name="arabic_text" rows="4" required dir="rtl" placeholder="الحديث..."
                    class="w-full border-gray-200 rounded-xl p-4 text-2xl text-right font-arabic focus:ring-indigo-500"></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Bangla Translation <span
                            class="text-red-500">*</span></label>
                    <textarea name="bangla_text" rows="4" required
                        class="w-full border-gray-200 rounded-xl p-3 focus:ring-indigo-500"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">English Translation (Optional)</label>
                    <textarea name="english_text" rows="4"
                        class="w-full border-gray-200 rounded-xl p-3 focus:ring-indigo-500"></textarea>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Hadith Number</label>
                    <input type="text" name="hadith_number" placeholder="e.g. 1"
                        class="w-full border-gray-200 rounded-xl p-3 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Reference <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="reference" required placeholder="e.g. সহীহ বুখারী"
                        class="w-full border-gray-200 rounded-xl p-3 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Grade / Status</label>
                    <input type="text" name="grade" value="সহীহ"
                        class="w-full border-gray-200 rounded-xl p-3 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Points</label>
                    <input type="number" name="points" value="5"
                        class="w-full border-gray-200 rounded-xl p-3 focus:ring-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Authentic Explanation / Ulamas Opinion
                    (Optional)</label>
                <textarea name="explanation" rows="4" placeholder="নির্ভরযোগ্য স্কলারদের ব্যাখ্যা এখানে দিন..."
                    class="w-full border-gray-200 rounded-xl p-3 focus:ring-indigo-500"></textarea>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Source URL (Optional)</label>
                <input type="url" name="source_url" placeholder="https://..."
                    class="w-full border-gray-200 rounded-xl p-3 focus:ring-indigo-500">
            </div>

            <div class="pt-4 border-t border-gray-100 flex justify-end">
                <button type="submit"
                    class="bg-indigo-600 text-white px-10 py-3.5 rounded-xl font-black hover:bg-indigo-700 transition shadow-md">
                    Save Hadith
                </button>
            </div>
        </form>

        <script>
            const categoriesData = @json($categories);

            document.getElementById('category-select').addEventListener('change', function () {
                const catId = this.value;
                const subSelect = document.getElementById('sub-category-select');
                subSelect.innerHTML = '<option value="">-- Choose Chapter --</option>';

                if (catId) {
                    const selectedCat = categoriesData.find(c => c.id == catId);
                    if (selectedCat && selectedCat.sub_categories) {
                        selectedCat.sub_categories.forEach(sub => {
                            const opt = document.createElement('option');
                            opt.value = sub.id;
                            opt.textContent = sub.name_bn + (sub.name_en ? ` (${sub.name_en})` : '');
                            subSelect.appendChild(opt);
                        });
                    }
                }
            });
        </script>
    </div>
@endsection