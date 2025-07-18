<div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
    <!-- Card Header -->
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-t-lg">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                @if($icon)
                    <div class="flex-shrink-0">
                        {{ $icon }}
                    </div>
                @endif
                <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
            </div>
            
            @if(isset($actions))
                <div class="flex items-center space-x-2">
                    {{ $actions }}
                </div>
            @endif
        </div>
    </div>

    <!-- Card Body -->
    <div class="p-6">
        {{ $slot }}
    </div>
</div>