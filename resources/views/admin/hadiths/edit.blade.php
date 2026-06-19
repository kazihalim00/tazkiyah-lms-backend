@extends('layouts.app')
@section('title', 'Edit Hadith')
@section('header_title', 'Admin Panel - Edit Hadith')

@section('content')
    <div class="max-w-4xl mx-auto py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Edit Hadith</h1>
            <a href="{{ route('admin.hadiths.index') }}" class="text-indigo-600 font-bold hover:underline">&larr; Back</a>
        </div>

        <form action="{{ route('admin.hadiths.update', $hadith->id) }}" method="POST"
            class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 space-y-6">
            @csrf
            @method('PUT')

            <div class="bg-gray-50 p-5 rounded-2xl border border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Category</label>
                        <select name="category_id" id="category-select" class="w-full border-gray-200 rounded-xl p-3">
                            <option value="">-- Choose Category --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $hadith->category_id == $category->id ? 'selected' : '' }}>
                                    {{ $category->name_bn }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Chapter (পরিচ্ছেদ)</label>
                        <select name="sub_category_id" id="sub-category-select"
                            class="w-full border-gray-200 rounded-xl p-3">
                            <option value="">-- Choose Chapter --</option>
                        </select>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Arabic Text</label>
                <textarea name="arabic_text" rows="4" required dir="rtl"
                    class="w-full border-gray-200 rounded-xl p-4 text-2xl text-right font-arabic">{{ $hadith->arabic_text }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Bangla Translation</label>
                    <textarea name="bangla_text" rows="4" required
                        class="w-full border-gray-200 rounded-xl p-3">{{ $hadith->bangla_text }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">English Translation</label>
                    <textarea name="english_text" rows="4"
                        class="w-full border-gray-200 rounded-xl p-3">{{ $hadith->english_text }}</textarea>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Reference</label>
                    <input type="text" name="reference" value="{{ $hadith->reference }}" required
                        class="w-full border-gray-200 rounded-xl p-3">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Grade / Status</label>
                    <input type="text" name="grade" value="{{ $hadith->grade }}"
                        class="w-full border-gray-200 rounded-xl p-3">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Points</label>
                    <input type="number" name="points" value="{{ $hadith->points }}"
                        class="w-full border-gray-200 rounded-xl p-3">
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Explanation</label>
                <textarea name="explanation" rows="4"
                    class="w-full border-gray-200 rounded-xl p-3">{{ $hadith->explanation }}</textarea>
            </div>

            <div class="pt-4 border-t border-gray-100 flex justify-end">
                <button type="submit"
                    class="bg-indigo-600 text-white px-10 py-3.5 rounded-xl font-black hover:bg-indigo-700 shadow-md">
                    Update Hadith
                </button>
            </div>
        </form>

        <script>
            const categoriesData = @json($categories);
            const subSelect = document.getElementById('sub-category-select');
            const selectedSubId = "{{ $hadith->sub_category_id }}";

            function populateSubs(catId) {
                subSelect.innerHTML = '<option value="">-- Choose Chapter --</option>';
                if (catId) {
                    const selectedCat = categoriesData.find(c => c.id == catId);
                    if (selectedCat && selectedCat.sub_categories) {
                        selectedCat.sub_categories.forEach(sub => {
                            const opt = document.createElement('option');
                            opt.value = sub.id;
                            opt.textContent = sub.name_bn;
                            if (sub.id == selectedSubId) opt.selected = true;
                            subSelect.appendChild(opt);
                        });
                    }
                }
            }

            // On load
            populateSubs(document.getElementById('category-select').value);

            // On change
            document.getElementById('category-select').addEventListener('change', function () {
                populateSubs(this.value);
            });
        </script>
    </div>
@endsection