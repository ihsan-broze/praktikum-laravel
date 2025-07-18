{{-- resources/views/comments/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Comments') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">All Comments</h3>
                    
                    @if($comments->count() > 0)
                        <div class="space-y-4">
                            @foreach($comments as $comment)
                                <div class="border-b pb-4">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <p class="text-gray-800">{{ $comment->content }}</p>
                                            <div class="mt-2 text-sm text-gray-600">
                                                By {{ $comment->author ?? $comment->author_name }} on {{ $comment->created_at->format('M d, Y') }}
                                                @if($comment->post)
                                                    • Post: <a href="{{ route('posts.show', $comment->post) }}" class="text-blue-600 hover:underline">{{ $comment->post->title }}</a>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <a href="{{ route('comments.show', $comment) }}" 
                                               class="text-blue-600 hover:text-blue-800">View</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-6">
                            {{ $comments->links() }}
                        </div>
                    @else
                        <p class="text-gray-500">No comments found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>