<div class="bg-white rounded-lg shadow-sm border border-gray-200 {{ $colspan }} hover:shadow-md transition-shadow duration-200">
    <!-- Card Header -->
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-t-lg">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
                <div class="flex items-center space-x-1">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    <span class="text-xs text-gray-500">Live</span>
                </div>
            </div>
            
            @if($actions)
                <div class="flex items-center space-x-2">
                    {{ $actions }}
                </div>
            @else
                <div class="flex items-center space-x-2">
                    <button class="text-gray-400 hover:text-gray-600 transition-colors" title="Refresh">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    <button class="text-gray-400 hover:text-gray-600 transition-colors" title="Export">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Card Body -->
    <div class="p-6">
        <!-- Chart Container -->
        <div class="relative {{ $height }} w-full">
            <canvas id="{{ $chartId }}" class="w-full h-full"></canvas>
            
            <!-- Loading State -->
            <div id="{{ $chartId }}_loading" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-90 hidden">
                <div class="flex flex-col items-center space-y-2">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    <span class="text-sm text-gray-500">Loading chart...</span>
                </div>
            </div>
            
            <!-- Error State -->
            <div id="{{ $chartId }}_error" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-90 hidden">
                <div class="flex flex-col items-center space-y-2 text-center">
                    <svg class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm text-gray-500">Failed to load chart</span>
                    <button class="text-xs text-blue-600 hover:text-blue-700" onclick="retryChart('{{ $chartId }}')">
                        Retry
                    </button>
                </div>
            </div>
        </div>

        <!-- Additional Content Slot -->
        @if($slot->isNotEmpty())
            <div class="mt-4 pt-4 border-t border-gray-100">
                {{ $slot }}
            </div>
        @endif
    </div>
</div>

<script>
// Chart helper functions for {{ $chartId }}
function showChartLoading(chartId) {
    const loading = document.getElementById(chartId + '_loading');
    const error = document.getElementById(chartId + '_error');
    if (loading) loading.classList.remove('hidden');
    if (error) error.classList.add('hidden');
}

function hideChartLoading(chartId) {
    const loading = document.getElementById(chartId + '_loading');
    if (loading) loading.classList.add('hidden');
}

function showChartError(chartId) {
    const loading = document.getElementById(chartId + '_loading');
    const error = document.getElementById(chartId + '_error');
    if (loading) loading.classList.add('hidden');
    if (error) error.classList.remove('hidden');
}

function retryChart(chartId) {
    const error = document.getElementById(chartId + '_error');
    if (error) error.classList.add('hidden');
    
    // Trigger chart reload
    if (typeof initializeCharts === 'function') {
        showChartLoading(chartId);
        setTimeout(() => {
            initializeCharts(window.chartData || {});
            hideChartLoading(chartId);
        }, 1000);
    }
}
</script>