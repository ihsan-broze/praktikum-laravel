    {{-- resources/views/posts/create.blade.php --}}
    <x-app-layout>
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create New Post') }}
            </h2>
        </x-slot>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data">
                            @csrf

                            <!-- Title -->
                            <div class="mb-6">
                                <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                                <input type="text" name="title" id="title" value="{{ old('title') }}" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @if(isset($errors) && $errors->has('title')) border-red-500 @endif" 
                                    required>
                                @if(isset($errors) && $errors->has('title'))
                                    <p class="mt-1 text-sm text-red-600">{{ $errors->first('title') }}</p>
                                @endif
                            </div>

                            <!-- Slug -->
                            <div class="mb-6">
                                <label for="slug" class="block text-sm font-medium text-gray-700">Slug (optional)</label>
                                <input type="text" name="slug" id="slug" value="{{ old('slug') }}" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @if(isset($errors) && $errors->has('slug')) border-red-500 @endif">
                                <p class="mt-1 text-sm text-gray-500">Leave empty to auto-generate from title</p>
                                @if(isset($errors) && $errors->has('slug'))
                                    <p class="mt-1 text-sm text-red-600">{{ $errors->first('slug') }}</p>
                                @endif
                            </div>

                            <!-- Category -->
                            <div class="mb-6">
                                <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>
                                <select name="category_id" id="category_id" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @if(isset($errors) && $errors->has('category_id')) border-red-500 @endif" 
                                        required>
                                    <option value="">Select a category</option>
                                    @if(isset($categories))
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @if(isset($errors) && $errors->has('category_id'))
                                    <p class="mt-1 text-sm text-red-600">{{ $errors->first('category_id') }}</p>
                                @endif
                            </div>

                            <!-- Excerpt -->
                            <div class="mb-6">
                                <label for="excerpt" class="block text-sm font-medium text-gray-700">Excerpt (optional)</label>
                                <textarea name="excerpt" id="excerpt" rows="3" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @if(isset($errors) && $errors->has('excerpt')) border-red-500 @endif">{{ old('excerpt') }}</textarea>
                                <p class="mt-1 text-sm text-gray-500">A brief summary of your post</p>
                                @if(isset($errors) && $errors->has('excerpt'))
                                    <p class="mt-1 text-sm text-red-600">{{ $errors->first('excerpt') }}</p>
                                @endif
                            </div>

                            <!-- Content -->
                            <div class="mb-6">
                                <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
                                <textarea name="content" id="content" rows="10" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @if(isset($errors) && $errors->has('content')) border-red-500 @endif" 
                                        required>{{ old('content') }}</textarea>
                                @if(isset($errors) && $errors->has('content'))
                                    <p class="mt-1 text-sm text-red-600">{{ $errors->first('content') }}</p>
                                @endif
                            </div>

                            <!-- Featured Image -->
                            <div class="mb-6">
                                <label for="featured_image" class="block text-sm font-medium text-gray-700">Featured Image (optional)</label>
                                <input type="file" name="featured_image" id="featured_image" accept="image/*"
                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 @if(isset($errors) && $errors->has('featured_image')) border-red-500 @endif">
                                <p class="mt-1 text-sm text-gray-500">Max file size: 2MB. Supported formats: JPG, PNG, GIF</p>
                                @if(isset($errors) && $errors->has('featured_image'))
                                    <p class="mt-1 text-sm text-red-600">{{ $errors->first('featured_image') }}</p>
                                @endif
                            </div>

                            <!-- Meta Title -->
                            <div class="mb-6">
                                <label for="meta_title" class="block text-sm font-medium text-gray-700">Meta Title (optional)</label>
                                <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title') }}" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @if(isset($errors) && $errors->has('meta_title')) border-red-500 @endif">
                                <p class="mt-1 text-sm text-gray-500">SEO title for search engines</p>
                                @if(isset($errors) && $errors->has('meta_title'))
                                    <p class="mt-1 text-sm text-red-600">{{ $errors->first('meta_title') }}</p>
                                @endif
                            </div>

                            <!-- Meta Description -->
                            <div class="mb-6">
                                <label for="meta_description" class="block text-sm font-medium text-gray-700">Meta Description (optional)</label>
                                <textarea name="meta_description" id="meta_description" rows="2" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @if(isset($errors) && $errors->has('meta_description')) border-red-500 @endif">{{ old('meta_description') }}</textarea>
                                <p class="mt-1 text-sm text-gray-500">SEO description for search engines</p>
                                @if(isset($errors) && $errors->has('meta_description'))
                                    <p class="mt-1 text-sm text-red-600">{{ $errors->first('meta_description') }}</p>
                                @endif
                            </div>

                            <!-- Status -->
                            <div class="mb-6">
                                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                <select name="status" id="status" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @if(isset($errors) && $errors->has('status')) border-red-500 @endif" 
                                        required>
                                    <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                                </select>
                                @if(isset($errors) && $errors->has('status'))
                                    <p class="mt-1 text-sm text-red-600">{{ $errors->first('status') }}</p>
                                @endif
                            </div>

                            <!-- Submit Buttons -->
                            <div class="flex items-center justify-between">
                                <a href="{{ route('posts.index') }}" 
                                class="text-gray-600 hover:text-gray-900">
                                    Cancel
                                </a>
                                <div class="flex space-x-3">
                                    <button type="submit" name="action" value="save" 
                                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Create Post
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </x-app-layout>