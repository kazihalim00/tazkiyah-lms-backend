@extends('layouts.app')

@section('title', 'Manage Hadiths')
@section('header_title', 'Admin - Manage Hadiths')

@section('content')
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-black text-gray-900">Manage Hadiths</h1>
                <p class="text-sm font-bold text-gray-500 mt-1">Search, review and remove weak/unnecessary hadiths.</p>
            </div>

            <div class="flex items-center gap-3 w-full md:w-auto">
                <form action="{{ route('admin.hadiths.index') }}" method="GET" class="w-full md:w-80 flex">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search word, number, grade..."
                        class="w-full bg-white px-4 py-2 rounded-l-xl text-sm border border-gray-200 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-r-xl transition font-bold text-sm">
                        Search
                    </button>
                </form>

                @if(request('search'))
                    <a href="{{ route('admin.hadiths.index') }}"
                        class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-4 py-2 rounded-xl transition font-bold text-sm whitespace-nowrap">
                        Clear
                    </a>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded-xl shadow-sm mb-6 font-bold">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-black text-gray-500 uppercase tracking-wider">No.
                            </th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-black text-gray-500 uppercase tracking-wider w-1/2">
                                Text (Bangla)</th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-black text-gray-500 uppercase tracking-wider">
                                Category</th>
                            <th scope="col"
                                class="px-6 py-4 text-center text-xs font-black text-gray-500 uppercase tracking-wider">
                                Grade</th>
                            <th scope="col"
                                class="px-6 py-4 text-right text-xs font-black text-gray-500 uppercase tracking-wider">
                                Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($hadiths as $hadith)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                    {{ $hadith->hadith_number }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <p class="line-clamp-2">{{ $hadith->bangla_text }}</p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-lg bg-indigo-50 text-indigo-700">
                                        {{ $hadith->category ? $hadith->category->name_bn : 'Uncategorized' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if(str_contains(strtolower($hadith->grade), 'sahih') || str_contains(strtolower($hadith->grade), 'সহীহ'))
                                        <span
                                            class="px-2 py-1 text-xs font-bold rounded bg-emerald-100 text-emerald-800">{{ $hadith->grade }}</span>
                                    @elseif(str_contains(strtolower($hadith->grade), 'daif') || str_contains(strtolower($hadith->grade), 'যইফ') || str_contains(strtolower($hadith->grade), 'দুর্বল'))
                                        <span
                                            class="px-2 py-1 text-xs font-bold rounded bg-red-100 text-red-800">{{ $hadith->grade }}</span>
                                    @else
                                        <span
                                            class="px-2 py-1 text-xs font-bold rounded bg-gray-100 text-gray-800">{{ $hadith->grade }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <form action="{{ route('hadiths.destroy', $hadith->id) }}" method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this Hadith?');"
                                        class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="bg-red-50 text-red-600 hover:bg-red-600 hover:text-white px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-400 font-bold">
                                    No hadiths found matching your search.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($hadiths->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                    {{ $hadiths->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection