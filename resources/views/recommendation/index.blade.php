<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Recommended Courses') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Header Section -->
                    <div class="mb-8">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Courses Just For You</h3>
                        <p class="text-gray-600">Based on your viewing history and interests, we've curated these courses that match your learning preferences.</p>
                    </div>

                    @if($recommended->count() > 0)
                        <!-- Recommended Courses Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($recommended as $course)
                                <div class="bg-white border border-gray-200 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 group">
                                    <!-- Course Image -->
                                    @if($course->image)
                                        <div class="relative overflow-hidden rounded-t-lg">
                                            <img src="{{ asset('storage/' . $course->image) }}" 
                                                 alt="{{ $course->title }}" 
                                                 class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                                            <div class="absolute top-3 left-3">
                                                @php
                                                    $categoryTitle = $course->category 
                                                        ? $course->category->title 
                                                        : ($course->category_id ? \App\Models\Category::find($course->category_id)?->title : 'No Category');
                                                @endphp
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-600 text-white">
                                                    {{ $categoryTitle }}
                                                </span>
                                            </div>
                                            @if($course->level)
                                                <div class="absolute top-3 right-3">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-white text-gray-800">
                                                        {{ ucfirst($course->level) }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="relative bg-gradient-to-br from-blue-500 to-purple-600 h-48 rounded-t-lg flex items-center justify-center">
                                            <div class="text-white text-center">
                                                <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                </svg>
                                                @php
                                                    $categoryTitle = $course->category 
                                                        ? $course->category->title 
                                                        : ($course->category_id ? \App\Models\Category::find($course->category_id)?->title : 'No Category');
                                                @endphp
                                                <div class="text-sm font-medium">{{ $categoryTitle }}</div>
                                            </div>
                                            <div class="absolute top-3 right-3">
                                                @if($course->level)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-white text-gray-800">
                                                        {{ ucfirst($course->level) }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Course Content -->
                                    <div class="p-6">
                                        <h4 class="text-xl font-semibold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors duration-200">
                                            {{ $course->title }}
                                        </h4>
                                        <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                                            {{ Str::limit($course->description, 120) }}
                                        </p>

                                        <!-- Course Meta -->
                                        <div class="flex items-center justify-between mb-4">
                                            @if($course->duration)
                                                <div class="flex items-center text-sm text-gray-500">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    {{ $course->duration }} min
                                                </div>
                                            @endif

                                            <div class="flex items-center space-x-2">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                    @if($course->status === 'published') bg-green-100 text-green-800
                                                    @elseif($course->status === 'draft') bg-yellow-100 text-yellow-800
                                                    @else bg-red-100 text-red-800 @endif">
                                                    {{ ucfirst($course->status ?? 'draft') }}
                                                </span>
                                                @if($course->featured)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                        Featured
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Action Buttons -->
                                        <div class="flex items-center justify-between">
                                            <a href="{{ route('admin.courses.show', $course) }}" 
                                               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors duration-200">
                                                View Course
                                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </a>

                                            <!-- Recommendation Badge -->
                                            <div class="flex items-center text-xs text-green-600 bg-green-50 px-2 py-1 rounded-full">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                                Recommended
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Browse More Section -->
                        <div class="mt-12 text-center">
                            <div class="bg-gray-50 rounded-lg p-8">
                                <h4 class="text-lg font-semibold text-gray-900 mb-2">Want to explore more?</h4>
                                <p class="text-gray-600 mb-4">Browse all available courses to discover new learning opportunities.</p>
                                <a href="{{ route('admin.courses.index') }}" 
                                   class="inline-flex items-center px-6 py-3 bg-gray-800 hover:bg-gray-900 text-white font-medium rounded-lg transition-colors duration-200">
                                    Browse All Courses
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>

                    @else
                        <!-- Empty State -->
                        <div class="text-center py-12">
                            <div class="w-24 h-24 mx-auto mb-6 bg-gray-100 rounded-full flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                </svg>
                            </div>
                            
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">No Recommendations Yet</h3>
                            <p class="text-gray-600 mb-6 max-w-md mx-auto">
                                Start exploring courses to get personalized recommendations based on your interests and learning history.
                            </p>

                            <div class="space-y-3 sm:space-y-0 sm:space-x-3 sm:flex sm:justify-center">
                                <a href="{{ route('admin.courses.index') }}" 
                                   class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                                    Browse Courses
                                </a>
                                <a href="{{ route('admin.dashboard') }}" 
                                   class="inline-flex items-center px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium rounded-lg transition-colors duration-200">
                                    Go to Dashboard
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>