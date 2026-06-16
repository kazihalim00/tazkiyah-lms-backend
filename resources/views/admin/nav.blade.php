<div class="max-w-6xl mx-auto mt-8 mb-4">
    <div class="bg-white p-2 rounded-xl shadow-sm border border-gray-100 flex gap-2">
        <a href="{{ route('admin.courses.index') }}"
            class="px-6 py-2.5 rounded-lg font-bold transition-all duration-200 {{ request()->routeIs('admin.courses.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }}">
            📚 Courses
        </a>

        <a href="{{ route('admin.modules.index') }}"
            class="px-6 py-2.5 rounded-lg font-bold transition-all duration-200 {{ request()->routeIs('admin.modules.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }}">
            📁 Modules
        </a>

        <a href="{{ route('admin.lessons.index') }}"
            class="px-6 py-2.5 rounded-lg font-bold transition-all duration-200 {{ request()->routeIs('admin.lessons.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }}">
            📝 Lessons
        </a>
    </div>
</div>