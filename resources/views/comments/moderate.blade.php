{{-- resources/views/comments/moderate.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Comment Moderation') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Pending Comments -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Pending Comments ({{ $pendingComments->total() }})</h3>
                    
                    @if($pendingComments->count() > 0)
                        <form method="POST" action="{{ route('moderator.comments.bulk-moderate') }}" class="mb-4">
                            @csrf
                            <div class="flex items-center space-x-4 mb-4">
                                <select name="action" class="border-gray-300 rounded-md shadow-sm">
                                    <option value="approve">Approve</option>
                                    <option value="reject">Reject</option>
                                    <option value="spam">Mark as Spam</option>
                                    <option value="delete">Delete</option>
                                </select>
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Apply to Selected
                                </button>
                            </div>
                            
                            <div class="space-y-4">
                                @foreach($pendingComments as $comment)
                                    <div class="border-b pb-4">
                                        <div class="flex items-start space-x-3">
                                            <input type="checkbox" name="comment_ids[]" value="{{ $comment->id }}" 
                                                   class="mt-1">
                                            <div class="flex-1">
                                                <p class="text-gray-800">{{ $comment->content }}</p>
                                                <div class="mt-2 text-sm text-gray-600">
                                                    By {{ $comment->author ?? $comment->author_name }} 
                                                    @if($comment->author_email)
                                                        ({{ $comment->author_email }})
                                                    @endif
                                                    on {{ $comment->created_at->format('M d, Y H:i') }}
                                                </div>
                                                @if($comment->post)
                                                    <div class="mt-1 text-sm text-blue-600">
                                                        Post: {{ $comment->post->title }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex space-x-2">
                                                <form method="POST" action="{{ route('moderator.comments.approve', $comment) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="text-green-600 hover:text-green-800">
                                                        Approve
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('moderator.comments.reject', $comment) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="text-red-600 hover:text-red-800">
                                                        Reject
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </form>
                        
                        {{ $pendingComments->links() }}
                    @else
                        <p class="text-gray-500">No pending comments.</p>
                    @endif
                </div>
            </div>

            <!-- Spam Comments -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Spam Comments ({{ $spamComments->total() }})</h3>
                    
                    @if($spamComments->count() > 0)
                        <div class="space-y-4">
                            @foreach($spamComments as $comment)
                                <div class="border-b pb-4 bg-red-50 p-3 rounded">
                                    <p class="text-gray-800">{{ $comment->content }}</p>
                                    <div class="mt-2 text-sm text-gray-600">
                                        By {{ $comment->author ?? $comment->author_name }} 
                                        @if($comment->author_email)
                                            ({{ $comment->author_email }})
                                        @endif
                                        on {{ $comment->created_at->format('M d, Y H:i') }}
                                    </div>
                                    <div class="mt-2">
                                        <form method="POST" action="{{ route('comments.destroy', $comment) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm"
                                                    onclick="return confirm('Are you sure you want to delete this comment?')">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        {{ $spamComments->links() }}
                    @else
                        <p class="text-gray-500">No spam comments.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>