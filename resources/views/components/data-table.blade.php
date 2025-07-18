<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <!-- Table Header -->
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
            
            <div class="flex items-center space-x-3">
                @if($searchable)
                    <div class="relative">
                        <input 
                            type="text" 
                            id="{{ $tableId }}_search"
                            placeholder="Search..." 
                            class="pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500 w-64"
                        />
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                @endif
                
                @if(isset($actions))
                    {{ $actions }}
                @endif
            </div>
        </div>
    </div>

    <!-- Table Content -->
    <div class="overflow-x-auto">
        <table id="{{ $tableId }}" class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    @foreach($headers as $header)
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider {{ $header['sortable'] ?? false ? 'cursor-pointer hover:bg-gray-100' : '' }}" 
                            @if($header['sortable'] ?? false) 
                                onclick="sortTable('{{ $tableId }}', '{{ $header['key'] }}')"
                            @endif>
                            <div class="flex items-center space-x-1">
                                <span>{{ $header['label'] }}</span>
                                @if($header['sortable'] ?? false)
                                    <svg class="w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M5 12a1 1 0 102 0V6.414l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L5 6.414V12zM15 8a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L15 13.586V8z"/>
                                    </svg>
                                @endif
                            </div>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody id="{{ $bodyId }}" class="bg-white divide-y divide-gray-200">
                {{ $slot }}
            </tbody>
        </table>
    </div>

    <!-- Table Footer -->
    <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
        <div class="flex items-center justify-between text-sm text-gray-500">
            <span id="{{ $tableId }}_info">Showing data</span>
            <div class="flex items-center space-x-2">
                <button class="px-3 py-1 border border-gray-300 rounded-md hover:bg-gray-100 transition-colors">
                    Previous
                </button>
                <span class="px-3 py-1">1</span>
                <button class="px-3 py-1 border border-gray-300 rounded-md hover:bg-gray-100 transition-colors">
                    Next
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function sortTable(tableId, column) {
    const table = document.getElementById(tableId);
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    
    // Simple sort implementation - you can enhance this
    rows.sort((a, b) => {
        const aValue = a.cells[0].textContent.trim();
        const bValue = b.cells[0].textContent.trim();
        return aValue.localeCompare(bValue);
    });
    
    // Clear and re-append rows
    tbody.innerHTML = '';
    rows.forEach(row => tbody.appendChild(row));
}
</script>