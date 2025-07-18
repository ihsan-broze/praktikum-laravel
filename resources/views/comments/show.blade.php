{{-- resources/views/comments/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Comment Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium mb-2">Comment</h3>
                        <p class="text-gray-800 mb-4">{{ $comment->content }}</p>
                        
                        <div class="text-sm text-gray-600">
                            <p>By {{ $comment->author ?? $comment->author_name }}</p>
                            @if($comment->author_email)
                                <p>Email: {{ $comment->author_email }}</p>
                            @endif
                            <p>Posted: {{ $comment->created_at->format('M d, Y H:i') }}</p>
                            <p>Status: <span class="capitalize font-medium">{{ $comment->status }}</span></p>
                        </div>
                    </div>

                    @if($comment->post)
                        <div class="border-t pt-4">
                            <h4 class="font-medium mb-2">Related Post</h4>
                            <a href="{{ route('posts.show', $comment->post) }}" 
                               class="text-blue-600 hover:text-blue-800">
                                {{ $comment->post->title }}
                            </a>
                        </div>
                    @endif

                    @auth
                        @if($comment->canBeEditedBy(auth()->user()))
                            <div class="border-t pt-4 mt-4">
                                <div class="flex space-x-4">
                                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                        Edit Comment
                                    </button>
                                    <form method="POST" action="{{ route('comments.destroy', $comment) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                                                onclick="return confirm('Are you sure you want to delete this comment?')">
                                            Delete Comment
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>
</x-app-layout>