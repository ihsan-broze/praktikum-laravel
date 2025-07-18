<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Course') }}
            </h2>
            <a href="{{ route('admin.courses.show', $course) }}" 
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Course
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.courses.update', $course) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Course Title -->
                        <div class="mb-4">
                            <x-input-label for="title" :value="__('Course Title')" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" 
                                :value="old('title', $course->title)" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" rows="4" 
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                                required>{{ old('description', $course->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- Category -->
                        <div class="mb-4">
                            <x-input-label for="category_id" :value="__('Category')" />
                            <select id="category_id" name="category_id" 
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                        {{ old('category_id', $course->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->title }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                            <p class="text-sm text-gray-600 mt-1">Pilih kategori course (opsional)</p>
                        </div>

                        <!-- Level -->
                        <div class="mb-4">
                            <x-input-label for="level" :value="__('Level')" />
                            <select id="level" name="level" 
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Pilih Level</option>
                                <option value="beginner" {{ old('level', $course->level) == 'beginner' ? 'selected' : '' }}>Beginner</option>
                                <option value="intermediate" {{ old('level', $course->level) == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                <option value="advanced" {{ old('level', $course->level) == 'advanced' ? 'selected' : '' }}>Advanced</option>
                            </select>
                            <x-input-error :messages="$errors->get('level')" class="mt-2" />
                        </div>

                        <!-- Duration -->
                        <div class="mb-4">
                            <x-input-label for="duration" :value="__('Duration (dalam menit)')" />
                            <x-text-input id="duration" class="block mt-1 w-full" type="number" name="duration" 
                                :value="old('duration', $course->duration)" placeholder="Contoh: 120 (untuk 2 jam)" min="1" />
                            <x-input-error :messages="$errors->get('duration')" class="mt-2" />
                            <p class="text-sm text-gray-600 mt-1">Masukkan durasi dalam menit (contoh: 60 untuk 1 jam)</p>
                        </div>

                        <!-- Content -->
                        <div class="mb-4">
                            <x-input-label for="content" :value="__('Course Content')" />
                            <textarea id="content" name="content" rows="6" 
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                                placeholder="Masukkan materi/konten course secara detail">{{ old('content', $course->content) }}</textarea>
                            <x-input-error :messages="$errors->get('content')" class="mt-2" />
                        </div>

                        <!-- Status -->
                        <div class="mb-4">
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status" name="status" 
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="draft" {{ old('status', $course->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ old('status', $course->status) == 'published' ? 'selected' : '' }}>Published</option>
                                <option value="archived" {{ old('status', $course->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <!-- Featured -->
                        <div class="mb-4">
                            <div class="flex items-center">
                                <input id="featured" type="checkbox" name="featured" value="1" 
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    {{ old('featured', $course->featured) ? 'checked' : '' }}>
                                <x-input-label for="featured" :value="__('Featured Course')" class="ml-2" />
                            </div>
                            <x-input-error :messages="$errors->get('featured')" class="mt-2" />
                            <p class="text-sm text-gray-600 mt-1">Centang jika course ini ingin ditampilkan sebagai unggulan</p>
                        </div>

                        <!-- Current Image -->
                        @if($course->image)
                            <div class="mb-4">
                                <x-input-label :value="__('Current Image')" />
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $course->image) }}" 
                                         alt="{{ $course->title }}" 
                                         class="w-32 h-32 object-cover rounded-lg">
                                </div>
                            </div>
                        @endif

                        <!-- Course Image -->
                        <div class="mb-6">
                            <x-input-label for="image" :value="__('Course Image')" />
                            <input id="image" class="block mt-1 w-full" type="file" name="image" accept="image/*" />
                            <x-input-error :messages="$errors->get('image')" class="mt-2" />
                            <p class="text-sm text-gray-600 mt-1">
                                Upload gambar course baru (opsional). Format: JPG, PNG, GIF. Maksimal 2MB
                                @if($course->image)
                                    <br>Kosongkan jika tidak ingin mengubah gambar.
                                @endif
                            </p>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex space-x-3">
                                <a href="{{ route('admin.courses.show', $course) }}" 
                                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                    Cancel
                                </a>
                                
                                <a href="{{ route('admin.courses.index') }}" 
                                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Back to List
                                </a>
                            </div>
                            
                            <x-primary-button>
                                {{ __('Update Course') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>