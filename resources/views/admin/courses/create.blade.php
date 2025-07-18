<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create New Course') }}
            </h2>
            <a href="{{ route('admin.courses.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Courses
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.courses.store') }}" enctype="multipart/form-data">
                            @csrf
                            <!-- Course Title -->
                            <div class="mb-4">
                                <x-input-label for="title" :value="__('Course Title')" />
                                <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" 
                                    :value="old('title')" required autofocus />
                                <x-input-error :messages="$errors->get('title')" class="mt-2" />
                            </div>

                            <!-- Description -->
                            <div class="mb-4">
                                <x-input-label for="description" :value="__('Description')" />
                                <textarea id="description" name="description" rows="4" 
                                    class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                                    required>{{ old('description') }}</textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>

                            <!-- Category -->
                            <div class="mb-4">
                                <x-input-label for="category_id" :value="__('Category')" />
                                <select id="category_id" name="category_id" 
                                    class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">Pilih Kategori</option>
                                    @if(isset($categories) && $categories->count() > 0)
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->title }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="">Tidak ada kategori tersedia</option>
                                    @endif
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
                                    <option value="beginner" {{ old('level') == 'beginner' ? 'selected' : '' }}>Beginner</option>
                                    <option value="intermediate" {{ old('level') == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                    <option value="advanced" {{ old('level') == 'advanced' ? 'selected' : '' }}>Advanced</option>
                                </select>
                                <x-input-error :messages="$errors->get('level')" class="mt-2" />
                            </div>

                            <!-- Duration -->
                            <div class="mb-4">
                                <x-input-label for="duration" :value="__('Duration (dalam menit)')" />
                                <x-text-input id="duration" class="block mt-1 w-full" type="number" name="duration" 
                                    :value="old('duration')" placeholder="Contoh: 120 (untuk 2 jam)" min="1" />
                                <x-input-error :messages="$errors->get('duration')" class="mt-2" />
                                <p class="text-sm text-gray-600 mt-1">Masukkan durasi dalam menit (contoh: 60 untuk 1 jam)</p>
                            </div>

                            <!-- Content -->
                            <div class="mb-4">
                                <x-input-label for="content" :value="__('Course Content')" />
                                <textarea id="content" name="content" rows="6" 
                                    class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                                    placeholder="Masukkan materi/konten course secara detail">{{ old('content') }}</textarea>
                                <x-input-error :messages="$errors->get('content')" class="mt-2" />
                            </div>

                            <!-- Status -->
                            <div class="mb-4">
                                <x-input-label for="status" :value="__('Status')" />
                                <select id="status" name="status" 
                                    class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                                    <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>

                            <!-- Featured -->
                            <div class="mb-4">
                                <div class="flex items-center">
                                    <input id="featured" type="checkbox" name="featured" value="1" 
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        {{ old('featured') ? 'checked' : '' }}>
                                    <x-input-label for="featured" :value="__('Featured Course')" class="ml-2" />
                                </div>
                                <x-input-error :messages="$errors->get('featured')" class="mt-2" />
                                <p class="text-sm text-gray-600 mt-1">Centang jika course ini ingin ditampilkan sebagai unggulan</p>
                            </div>

                            <!-- Course Image -->
                            <div class="mb-6">
                                <x-input-label for="image" :value="__('Course Image')" />
                                <input id="image" class="block mt-1 w-full" type="file" name="image" accept="image/*" />
                                <x-input-error :messages="$errors->get('image')" class="mt-2" />
                                <p class="text-sm text-gray-600 mt-1">Upload gambar course (opsional). Format: JPG, PNG, GIF. Maksimal 2MB</p>
                            </div>

                            <div class="flex items-center justify-end">
                                <a href="{{ route('admin.courses.index') }}" 
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-4">
                                    Cancel
                                </a>
                                <x-primary-button>
                                    {{ __('Create Course') }}
                                </x-primary-button>
                            </div>
                        </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>