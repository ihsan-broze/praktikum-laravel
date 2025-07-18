<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Courses') }}
            </h2>
            <a href="{{ route('admin.courses.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Add New Course
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($courses->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($courses as $course)
                                <div class="bg-white border border-gray-200 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
                                    <!-- Course Image -->
                                    @if($course->image)
                                        <div class="h-48 bg-gray-200 rounded-t-lg overflow-hidden">
                                            <img src="{{ asset('storage/' . $course->image) }}" 
                                                 alt="{{ $course->title }}" 
                                                 class="w-full h-full object-cover">
                                        </div>
                                    @endif
                                    
                                    <div class="p-6">
                                        <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $course->title }}</h3>
                                        <p class="text-gray-600 text-sm mb-4">{{ Str::limit($course->description, 100) }}</p>
                                        
                                        <div class="flex items-center justify-between mb-4">
                                            @php
                                                $categoryTitle = $course->category 
                                                    ? $course->category->title 
                                                    : ($course->category_id ? \App\Models\Category::find($course->category_id)?->title : null);
                                            @endphp
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $categoryTitle ?? 'No Category' }}
                                            </span>
                                            @if($course->level)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    {{ ucfirst($course->level) }}
                                                </span>
                                            @endif
                                        </div>

                                        <div class="flex items-center justify-between mb-4">
                                            <div class="text-sm text-gray-500">
                                                @if($course->duration)
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        {{ $course->duration }} menit
                                                    </span>
                                                @endif
                                            </div>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                @if($course->status === 'published') bg-green-100 text-green-800
                                                @elseif($course->status === 'draft') bg-yellow-100 text-yellow-800
                                                @else bg-red-100 text-red-800 @endif">
                                                {{ ucfirst($course->status) }}
                                            </span>
                                        </div>

                                        <div class="flex items-center justify-between">
                                            <div class="text-sm text-gray-500">
                                                <span>Created: {{ $course->created_at->format('M d, Y') }}</span>
                                            </div>
                                            <div class="flex space-x-2">
                                                <a href="{{ route('admin.courses.show', $course) }}" 
                                                   class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-500 focus:outline-none focus:border-blue-700 focus:shadow-outline-blue active:bg-blue-700 transition ease-in-out duration-150">
                                                    View
                                                </a>
                                                <a href="{{ route('admin.courses.edit', $course) }}" 
                                                   class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:text-gray-800 active:bg-gray-50 transition ease-in-out duration-150">
                                                    Edit
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination jika diperlukan -->
                        @if(method_exists($courses, 'links'))
                            <div class="mt-6">
                                {{ $courses->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-12">
                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-gray-100 mb-4">
                                <svg class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <div class="text-gray-500 text-lg mb-4">Belum ada course yang tersedia.</div>
                            <a href="{{ route('admin.courses.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Buat Course Pertama
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>