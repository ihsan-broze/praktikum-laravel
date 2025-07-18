<div class="relative overflow-hidden rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200 group">
    <!-- Background Gradient -->
    <div class="absolute inset-0 bg-gradient-to-br {{ $gradient }} opacity-90"></div>
    
    <!-- Pattern Overlay -->
    <div class="absolute inset-0 opacity-10">
        <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="pattern-{{ $valueId }}" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                    <circle cx="2" cy="2" r="1" fill="currentColor"/>
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#pattern-{{ $valueId }})" class="text-white"/>
        </svg>
    </div>
    
    <!-- Content -->
    <div class="relative p-6 text-white">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <h3 class="text-sm font-medium text-white text-opacity-90 mb-1">{{ $title }}</h3>
                <div class="flex items-baseline space-x-2">
                    <span id="{{ $valueId }}" class="text-2xl font-bold tracking-tight">{{ $value }}</span>
                    @if($trendValue)
                        <span class="text-sm font-medium text-white text-opacity-80 bg-white bg-opacity-20 px-2 py-1 rounded-full">
                            {{ $trendValue }}
                        </span>
                    @endif
                </div>
                @if($trend)
                    <p class="text-xs text-white text-opacity-75 mt-1">{{ $trend }}</p>
                @endif
            </div>
            
            @if(isset($icon))
                <div class="flex-shrink-0 ml-4">
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center group-hover:bg-opacity-30 transition-all duration-200">
                        {{ $icon }}
                    </div>
                </div>
            @endif
        </div>
        
        <!-- Additional content slot -->
        @if($slot->isNotEmpty())
            {{ $slot }}
        @endif
    </div>
    
    <!-- Hover Effect -->
    <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-5 transition-opacity duration-200"></div>
</div>