<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Course Details') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.courses.edit', $course) }}" 
                   class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                    Edit Course
                </a>
                <a href="{{ route('admin.courses.index') }}" 
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to List
                </a>
            </div>
        </div>
    </x-slot>
    

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <!-- Course Header -->
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $course->title }}</h1>
                            <div class="flex items-center space-x-4 text-sm text-gray-600">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    @if($course->status === 'published') bg-green-100 text-green-800
                                    @elseif($course->status === 'draft') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($course->status) }}
                                </span>
                                @if($course->featured)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        Featured
                                    </span>
                                @endif
                                <span>Created: {{ $course->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                        
                        <!-- Course Image -->
                        @if($course->image)
                            <div class="ml-6 flex-shrink-0">
                                <img src="{{ asset('storage/' . $course->image) }}" 
                                     alt="{{ $course->title }}" 
                                     class="w-32 h-32 object-cover rounded-lg shadow-md">
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Course Content -->
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Left Column -->
                        <div class="space-y-6">
                            <!-- Course Details -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-3">Course Details</h3>
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="font-medium text-gray-700">Category:</span>
                                        @php
                                            $categoryTitle = $course->category 
                                                ? $course->category->title 
                                                : ($course->category_id ? \App\Models\Category::find($course->category_id)?->title : null);
                                        @endphp
                                        <span class="text-gray-900">{{ $categoryTitle ?? 'No Category' }}</span>
                                    </div>
                                    @if($course->level)
                                        <div class="flex justify-between">
                                            <span class="font-medium text-gray-700">Level:</span>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                {{ ucfirst($course->level) }}
                                            </span>
                                        </div>
                                    @endif
                                    @if($course->duration)
                                        <div class="flex justify-between">
                                            <span class="font-medium text-gray-700">Duration:</span>
                                            <span class="text-gray-900 flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                {{ $course->duration }} minutes
                                            </span>
                                        </div>
                                    @endif
                                    @if($course->creator)
                                        <div class="flex justify-between">
                                            <span class="font-medium text-gray-700">Created by:</span>
                                            <span class="text-gray-900">{{ $course->creator->name }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-6">
                            <!-- Description -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-3">Description</h3>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-gray-700 leading-relaxed">{{ $course->description }}</p>
                                </div>
                            </div>

                            <!-- Content -->
                            @if($course->content)
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Course Content</h3>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <div class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $course->content }}</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Full Width Image if exists -->
                    @if($course->image)
                        <div class="mt-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Course Image</h3>
                            <div class="text-center">
                                <img src="{{ asset('storage/' . $course->image) }}" 
                                     alt="{{ $course->title }}" 
                                     class="max-w-full h-auto rounded-lg shadow-lg mx-auto">
                            </div>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="mt-8 flex justify-between items-center pt-6 border-t border-gray-200">
                        <div class="flex space-x-3">
                            <a href="{{ route('admin.courses.edit', $course) }}" 
                               class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-500 active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit Course
                            </a>
                            
                            <form action="{{ route('admin.courses.destroy', $course) }}" method="POST" 
                                  onsubmit="return confirm('Are you sure you want to delete this course?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Delete Course
                                </button>
                            </form>
                        </div>
                        
                        <a href="{{ route('admin.courses.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Courses
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>