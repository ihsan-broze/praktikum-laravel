<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center space-y-4 lg:space-y-0">
            <div class="flex items-center space-x-4">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Analytics Dashboard') }}
                </h2>
                <div class="hidden lg:flex items-center space-x-2">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    <span class="text-xs text-gray-500">Live Updates</span>
                </div>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                <!-- Advanced Filters -->
                <div class="flex items-center space-x-2">
                    <!-- Date Range Picker -->
                    <select id="dateRange" class="text-xs border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="today">Today</option>
                        <option value="7">Last 7 days</option>
                        <option value="30" selected>Last 30 days</option>
                        <option value="90">Last 3 months</option>
                        <option value="365">Last year</option>
                        <option value="custom">Custom Range</option>
                    </select>
                    
                    <!-- Category Filter -->
                    <select id="categoryFilter" class="text-xs border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="all">All Categories</option>
                        <option value="programming">Programming</option>
                        <option value="design">Design</option>
                        <option value="marketing">Marketing</option>
                        <option value="business">Business</option>
                    </select>
                    
                    <!-- View Mode Toggle -->
                    <div class="flex bg-gray-100 rounded-md p-1">
                        <button id="dashboardView" class="view-toggle active px-3 py-1 text-xs font-medium rounded">
                            <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                            </svg>
                            Grid
                        </button>
                        <button id="detailView" class="view-toggle px-3 py-1 text-xs font-medium rounded">
                            <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                            </svg>
                            List
                        </button>
                    </div>
                </div>
                
                <!-- Realtime Status -->
                <div id="realtime-status" class="flex items-center bg-green-50 px-2 py-1 rounded-md">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse mr-2"></div>
                    <span class="text-xs text-green-700 font-medium">Live</span>
                </div>
                
                <!-- Last Updated -->
                <div class="text-xs text-gray-500">
                    <span class="hidden sm:inline">Updated: </span><span id="last-updated">{{ now()->format('H:i:s') }}</span>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex items-center space-x-2">
                    <!-- Refresh Button -->
                    <button id="refreshBtn" class="bg-gray-100 hover:bg-gray-200 text-gray-700 p-2 rounded-md text-xs transition-colors duration-200">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    
                    <!-- Settings Button -->
                    <button id="settingsBtn" class="bg-gray-100 hover:bg-gray-200 text-gray-700 p-2 rounded-md text-xs transition-colors duration-200">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    
                    <!-- Export Button -->
                    <button id="exportBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-md text-xs font-medium transition-colors duration-200 flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                        Export
                    </button>
                </div>
            </div>
        </div>
    </x-slot>

    <!-- Meta tag untuk CSRF token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="py-4 lg:py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Loading Overlay -->
            <div id="loadingOverlay" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 hidden">
                <div class="flex items-center justify-center h-full">
                    <div class="bg-white p-6 rounded-lg shadow-lg max-w-sm w-full mx-4">
                        <div class="flex items-center justify-center">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                        </div>
                        <p class="mt-4 text-gray-600 text-center font-medium">Loading analytics data...</p>
                        <div class="mt-2 bg-gray-200 rounded-full h-2">
                            <div id="loadingProgress" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notification Toast -->
            <div id="notification" class="fixed top-4 right-4 z-50 transform translate-x-full transition-transform duration-300">
                <div class="bg-white rounded-lg shadow-lg border-l-4 border-blue-500 p-4 max-w-sm">
                    <div class="flex items-start">
                        <div id="notificationIcon" class="flex-shrink-0">
                            <!-- Icon will be inserted here -->
                        </div>
                        <div class="ml-3 flex-1">
                            <p id="notificationTitle" class="text-sm font-medium text-gray-900"></p>
                            <p id="notificationMessage" class="text-sm text-gray-500 mt-1"></p>
                        </div>
                        <button id="closeNotification" class="ml-4 text-gray-400 hover:text-gray-600">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Quick Insights Banner -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-blue-900">Quick Insights</h3>
                            <p class="text-xs text-blue-700" id="quickInsight">
                                User engagement increased by 15% this week. Course completion rates are trending upward.
                            </p>
                        </div>
                    </div>
                    <button id="dismissInsight" class="text-blue-400 hover:text-blue-600">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Enhanced KPI Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-6 lg:mb-8">
                <!-- Total Users KPI with Drill-down -->
                <x-kpi-card 
                    title="Total Users"
                    :value="number_format($users ?? 0)"
                    trend="from last month"
                    trend-value="+12.5%"
                    value-id="total-users"
                    gradient="from-blue-500 to-blue-600">
                    <x-slot name="icon">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                        </svg>
                    </x-slot>
                    <div class="mt-2 cursor-pointer" onclick="showUserDetails()">
                        <div class="flex justify-between text-xs text-white text-opacity-75">
                            <span>Active: <span id="activeUsers">{{ number_format(($users ?? 0) * 0.7) }}</span></span>
                            <span class="underline">View Details →</span>
                        </div>
                    </div>
                </x-kpi-card>

                <!-- Total Courses KPI with Quick Actions -->
                <x-kpi-card 
                    title="Total Courses"
                    :value="number_format($courses ?? 0)"
                    trend="from last month"
                    trend-value="+8.3%"
                    value-id="total-courses"
                    gradient="from-green-500 to-green-600">
                    <x-slot name="icon">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                        </svg>
                    </x-slot>
                    <div class="mt-2">
                        <div class="flex justify-between text-xs text-white text-opacity-75">
                            <span>Published: <span id="publishedCourses">{{ number_format(($courses ?? 0) * 0.8) }}</span></span>
                            <button class="underline hover:text-white" onclick="showCourseBreakdown()">Breakdown →</button>
                        </div>
                    </div>
                </x-kpi-card>

                <!-- Average Score KPI with Trend Graph -->
                <x-kpi-card 
                    title="Average Score"
                    :value="isset($chartData['realtimeMetrics']['avg_overall_score']) ? $chartData['realtimeMetrics']['avg_overall_score'] : '85.2'"
                    trend="this week"
                    trend-value="+2.4 points"
                    value-id="avg-score"
                    gradient="from-yellow-500 to-yellow-600">
                    <x-slot name="icon">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.518 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                    </x-slot>
                    <div class="mt-2">
                        <canvas id="miniScoreTrend" width="100" height="20" class="w-full h-5 opacity-80"></canvas>
                    </div>
                </x-kpi-card>

                <!-- Completion Rate KPI with Interactive Progress -->
                <x-kpi-card 
                    title="Completion Rate"
                    :value="(isset($chartData['realtimeMetrics']['completion_rate']) ? $chartData['realtimeMetrics']['completion_rate'] : '78.5') . '%'"
                    trend="this month"
                    trend-value="+5.8%"
                    value-id="completion-rate"
                    gradient="from-purple-500 to-purple-600">
                    <x-slot name="icon">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </x-slot>
                    <div class="mt-2">
                        <div class="flex justify-between text-xs text-white text-opacity-75 mb-1">
                            <span>Goal: 85%</span>
                            <span id="completionTarget">78.5%</span>
                        </div>
                        <div class="w-full bg-white bg-opacity-20 rounded-full h-1.5">
                            <div id="completionProgress" class="bg-white h-1.5 rounded-full transition-all duration-500" style="width: 78.5%"></div>
                        </div>
                    </div>
                </x-kpi-card>
            </div>

            <!-- Main Analytics Section -->
            <div class="space-y-6 lg:space-y-8">
                <!-- Charts Grid with Enhanced Interactivity -->
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6 lg:gap-8">
                    <!-- Enhanced User Growth Chart -->
                    <x-chart-card 
                        title="User Growth & Engagement"
                        chart-id="userGrowthChart"
                        height="h-80"
                        colspan="xl:col-span-2">
                        <x-slot name="actions">
                            <div class="flex items-center space-x-2">
                                <button class="chart-period-btn active" data-period="7" data-chart="growth">7D</button>
                                <button class="chart-period-btn" data-period="30" data-chart="growth">30D</button>
                                <button class="chart-period-btn" data-period="90" data-chart="growth">90D</button>
                                <div class="h-4 w-px bg-gray-300"></div>
                                <button class="text-blue-600 hover:text-blue-700 text-xs" onclick="exportChart('userGrowthChart')">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            </div>
                        </x-slot>
                        <div class="flex items-center justify-between text-xs text-gray-500 mt-2">
                            <span>Peak activity: <span class="font-medium text-gray-700">2:00 PM - 4:00 PM</span></span>
                            <span>Avg. session: <span class="font-medium text-gray-700">24 min</span></span>
                        </div>
                    </x-chart-card>

                    <!-- Advanced Course Categories with Comparison -->
                    <x-chart-card 
                        title="Course Categories"
                        chart-id="categoriesChart"
                        height="h-80">
                        <x-slot name="actions">
                            <button class="text-blue-600 hover:text-blue-700 text-xs" onclick="toggleCategoryView()">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </x-slot>
                        <div class="grid grid-cols-2 gap-2 text-xs text-gray-600 mt-2">
                            <div class="flex justify-between">
                                <span>Most Popular:</span>
                                <span class="font-medium">Programming</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Fastest Growing:</span>
                                <span class="font-medium">Design</span>
                            </div>
                        </div>
                    </x-chart-card>

                    <!-- Interactive Score Distribution -->
                    <x-chart-card 
                        title="Score Distribution & Analysis"
                        chart-id="scoreChart">
                        <x-slot name="actions">
                            <select class="text-xs border-gray-300 rounded" onchange="filterScoreData(this.value)">
                                <option value="all">All Courses</option>
                                <option value="programming">Programming</option>
                                <option value="design">Design</option>
                                <option value="marketing">Marketing</option>
                            </select>
                        </x-slot>
                        <div class="text-xs text-gray-600 mt-2">
                            <div class="flex justify-between mb-1">
                                <span>Average: <span class="font-medium" id="scoreAverage">82.4</span></span>
                                <span>Median: <span class="font-medium" id="scoreMedian">85.0</span></span>
                            </div>
                            <div class="flex justify-between">
                                <span>Pass Rate: <span class="font-medium text-green-600" id="passRate">94.2%</span></span>
                                <span>Excellence: <span class="font-medium text-blue-600" id="excellenceRate">31.8%</span></span>
                            </div>
                        </div>
                    </x-chart-card>

                    <!-- Performance Radar with Benchmarks -->
                    <x-chart-card 
                        title="Performance vs Benchmarks"
                        chart-id="categoryPerformanceChart">
                        <x-slot name="actions">
                            <button class="benchmark-toggle text-xs text-gray-600 hover:text-gray-800" onclick="toggleBenchmarks()">
                                Show Industry Avg
                            </button>
                        </x-slot>
                        <div class="text-xs text-gray-600 mt-2">
                            <div class="grid grid-cols-2 gap-2">
                                <div>Strengths: <span class="font-medium text-green-600">UI/UX, Code Quality</span></div>
                                <div>Improve: <span class="font-medium text-orange-600">Technical Depth</span></div>
                            </div>
                        </div>
                    </x-chart-card>

                    <!-- Enhanced Course Status -->
                    <x-chart-card 
                        title="Course Lifecycle Status"
                        chart-id="statusChart">
                        <div class="text-xs text-gray-600 mt-2 space-y-1">
                            <div class="flex justify-between">
                                <span>In Review:</span>
                                <span class="font-medium" id="reviewCount">12</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Avg. Review Time:</span>
                                <span class="font-medium" id="avgReviewTime">3.2 days</span>
                            </div>
                        </div>
                    </x-chart-card>
                </div>

                <!-- Enhanced Activity Heatmap with Time Insights -->
                <x-chart-card 
                    title="User Activity Heatmap & Patterns"
                    chart-id="activityHeatmapChart"
                    height="h-96">
                    <x-slot name="actions">
                        <div class="flex items-center space-x-2">
                            <select class="text-xs border-gray-300 rounded" onchange="updateHeatmapView(this.value)">
                                <option value="hourly">Hourly View</option>
                                <option value="daily">Daily View</option>
                                <option value="weekly">Weekly View</option>
                            </select>
                            <button class="text-blue-600 hover:text-blue-700 text-xs" onclick="showHeatmapInsights()">
                                Insights
                            </button>
                        </div>
                    </x-slot>
                    <div class="mt-4">
                        <div class="flex flex-wrap items-center justify-between text-xs text-gray-600 mb-2">
                            <div class="flex items-center space-x-4">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-blue-100 rounded mr-2"></div>
                                    <span>Low (0-25%)</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-blue-300 rounded mr-2"></div>
                                    <span>Medium (26-75%)</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-blue-600 rounded mr-2"></div>
                                    <span>High (76-100%)</span>
                                </div>
                            </div>
                            <div class="text-xs">
                                <span>Peak Time: <span class="font-medium">Wed 2-4 PM</span></span>
                            </div>
                        </div>
                        <div id="heatmapInsights" class="text-xs text-gray-500 bg-gray-50 p-2 rounded hidden">
                            <p class="font-medium mb-1">Key Insights:</p>
                            <ul class="space-y-1">
                                <li>• Weekday afternoons show highest engagement</li>
                                <li>• Weekend mornings have opportunity for growth</li>
                                <li>• Late evening sessions have higher completion rates</li>
                            </ul>
                        </div>
                    </div>
                </x-chart-card>

                <!-- Enhanced Data Tables Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8">
                    <!-- Advanced Top Courses Table -->
                    <x-data-table 
                        title="Top Performing Courses"
                        table-id="topCoursesTable"
                        body-id="topCoursesBody"
                        :searchable="true"
                        :headers="[
                            ['label' => 'Course', 'key' => 'title', 'sortable' => true],
                            ['label' => 'Enrollments', 'key' => 'enrollments', 'sortable' => true],
                            ['label' => 'Rating', 'key' => 'rating', 'sortable' => true],
                            ['label' => 'Revenue', 'key' => 'revenue', 'sortable' => true]
                        ]">
                        <x-slot name="actions">
                            <div class="flex items-center space-x-2">
                                <select class="text-xs border-gray-300 rounded" onchange="filterCourses(this.value)">
                                    <option value="all">All Time</option>
                                    <option value="week">This Week</option>
                                    <option value="month">This Month</option>
                                </select>
                                <button class="text-blue-600 hover:text-blue-700 text-xs font-medium" onclick="exportTableData('courses')">
                                    Export
                                </button>
                                <button class="text-gray-600 hover:text-gray-700" onclick="showTableSettings('courses')">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                    </svg>
                                </button>
                            </div>
                        </x-slot>
                        <!-- Enhanced table rows will be populated by JavaScript -->
                    </x-data-table>

                    <!-- Enhanced Recent Activities with Filtering -->
                    <x-info-card title="Live Activity Stream">
                        <x-slot name="actions">
                            <div class="flex items-center space-x-2">
                                <select id="activityTypeFilter" class="text-xs border-gray-300 rounded" onchange="filterActivities(this.value)">
                                    <option value="all">All Activities</option>
                                    <option value="enrollment">Enrollments</option>
                                    <option value="completion">Completions</option>
                                    <option value="score">High Scores</option>
                                    <option value="login">Logins</option>
                                </select>
                                <button class="text-blue-600 hover:text-blue-700 text-xs font-medium" onclick="exportActivities()">
                                    Export
                                </button>
                                <button class="text-gray-600 hover:text-gray-700" id="activityFilter">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            </div>
                        </x-slot>
                        
                        <div class="space-y-3" id="recentActivities">
                            <!-- Enhanced Activity Items -->
                            <x-activity-item 
                                type="enrollment"
                                user="John Doe"
                                action="Enrolled in React Fundamentals"
                                time="2 min ago" />
                            
                            <x-activity-item 
                                type="completion"
                                user="Jane Smith"
                                action="Completed Python for Beginners with 95% score"
                                time="5 min ago" />
                            
                            <x-activity-item 
                                type="score"
                                user="Mike Johnson"
                                action="Achieved perfect score in Data Science Quiz"
                                time="10 min ago" />
                            
                            <x-activity-item 
                                type="login"
                                user="Sarah Wilson"
                                action="Started learning session"
                                time="15 min ago" />
                        </div>
                        
                        <!-- Activity Summary -->
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <div class="grid grid-cols-3 gap-4 text-center text-xs">
                                <div>
                                    <div class="font-medium text-gray-900" id="todayEnrollments">23</div>
                                    <div class="text-gray-500">Today's Enrollments</div>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900" id="todayCompletions">8</div>
                                    <div class="text-gray-500">Completions</div>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900" id="avgSessionTime">34m</div>
                                    <div class="text-gray-500">Avg. Session</div>
                                </div>
                            </div>
                        </div>
                    </x-info-card>
                </div>

                <!-- Enhanced System Metrics with Real-time Updates -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Advanced Server Performance -->
                    <x-info-card title="System Performance">
                        <x-slot name="actions">
                            <button class="text-blue-600 hover:text-blue-700 text-xs" onclick="refreshSystemMetrics()">
                                Refresh
                            </button>
                        </x-slot>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">CPU Usage</span>
                                <span class="text-sm font-medium" id="cpuUsage">45%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 relative">
                                <div id="cpuBar" class="bg-gradient-to-r from-blue-500 to-blue-600 h-2 rounded-full transition-all duration-500" style="width: 45%"></div>
                                <div class="absolute inset-0 flex items-center justify-center text-xs text-white font-medium">45%</div>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Memory Usage</span>
                                <span class="text-sm font-medium" id="memoryUsage">72%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 relative">
                                <div id="memoryBar" class="bg-gradient-to-r from-green-500 to-green-600 h-2 rounded-full transition-all duration-500" style="width: 72%"></div>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Storage</span>
                                <span class="text-sm font-medium" id="storageUsage">38%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 relative">
                                <div id="storageBar" class="bg-gradient-to-r from-yellow-500 to-yellow-600 h-2 rounded-full transition-all duration-500" style="width: 38%"></div>
                            </div>
                        </div>
                        
                        <div class="mt-4 pt-3 border-t border-gray-200 text-xs text-gray-500">
                            <div class="flex justify-between">
                                <span>Uptime: <span class="font-medium">99.9%</span></span>
                                <span>Load: <span class="font-medium">0.8</span></span>
                            </div>
                        </div>
                    </x-info-card>

                    <!-- Enhanced Database Statistics -->
                    <x-info-card title="Database Performance">
                        <x-slot name="actions">
                            <button class="text-blue-600 hover:text-blue-700 text-xs" onclick="optimizeDatabase()">
                                Optimize
                            </button>
                        </x-slot>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Total Records</span>
                                <span class="text-sm font-medium" id="totalRecords">2.3M</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Avg Query Time</span>
                                <span class="text-sm font-medium" id="avgQueryTime">1.2ms</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Cache Hit Rate</span>
                                <span class="text-sm font-medium text-green-600" id="cacheHitRate">94.5%</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Active Connections</span>
                                <span class="text-sm font-medium" id="activeConnections">12</span>
                            </div>
                        </div>
                        
                        <div class="mt-4 pt-3 border-t border-gray-200">
                            <div class="text-xs text-gray-500">
                                <div class="flex justify-between mb-1">
                                    <span>Slow Queries: <span class="font-medium">2</span></span>
                                    <span>Lock Wait: <span class="font-medium">0.3s</span></span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-1">
                                    <div class="bg-green-500 h-1 rounded-full" style="width: 94.5%"></div>
                                </div>
                            </div>
                        </div>
                    </x-info-card>

                    <!-- Enhanced API Performance -->
                    <x-info-card title="API Performance">
                        <x-slot name="actions">
                            <button class="text-blue-600 hover:text-blue-700 text-xs" onclick="viewAPILogs()">
                                View Logs
                            </button>
                        </x-slot>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Requests/min</span>
                                <span class="text-sm font-medium" id="requestsPerMin">1,247</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Success Rate</span>
                                <span class="text-sm font-medium text-green-600" id="successRate">99.7%</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Avg Response</span>
                                <span class="text-sm font-medium" id="avgResponse">245ms</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Errors/hour</span>
                                <span class="text-sm font-medium text-red-600" id="errorsPerHour">3</span>
                            </div>
                        </div>
                        
                        <div class="mt-4 pt-3 border-t border-gray-200">
                            <div class="text-xs text-gray-500 space-y-1">
                                <div class="flex justify-between">
                                    <span>Fastest Endpoint:</span>
                                    <span class="font-medium">/api/users (45ms)</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Slowest Endpoint:</span>
                                    <span class="font-medium">/api/analytics (850ms)</span>
                                </div>
                            </div>
                        </div>
                    </x-info-card>
                </div>

                <!-- Enhanced Live Activity Feed -->
                <x-info-card title="Live Activity & Notifications">
                    <x-slot name="actions">
                        <div class="flex items-center space-x-2">
                            <button id="pauseActivityFeed" class="text-gray-600 hover:text-gray-700" title="Pause/Resume">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM7 8a1 1 0 012 0v4a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v4a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                            <button id="clearActivityFeed" class="text-gray-600 hover:text-gray-700" title="Clear Feed">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" clip-rule="evenodd"/>
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                            <button id="exportActivityFeed" class="text-blue-600 hover:text-blue-700 text-xs" title="Export Feed">
                                Export
                            </button>
                        </div>
                    </x-slot>
                    
                    <div id="activity-feed" class="space-y-3 max-h-64 overflow-y-auto">
                        <x-activity-item 
                            type="success"
                            action="Dashboard initialized - Real-time monitoring active"
                            :time="now()->format('H:i:s')" />
                    </div>
                    
                    <!-- Activity Statistics -->
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="grid grid-cols-4 gap-4 text-center text-xs">
                            <div>
                                <div class="font-medium text-gray-900" id="feedCount">1</div>
                                <div class="text-gray-500">Total</div>
                            </div>
                            <div>
                                <div class="font-medium text-green-600" id="successCount">1</div>
                                <div class="text-gray-500">Success</div>
                            </div>
                            <div>
                                <div class="font-medium text-red-600" id="errorCount">0</div>
                                <div class="text-gray-500">Errors</div>
                            </div>
                            <div>
                                <div class="font-medium text-blue-600" id="infoCount">0</div>
                                <div class="text-gray-500">Info</div>
                            </div>
                        </div>
                    </div>
                </x-info-card>
            </div>
        </div>
    </div>

    <!-- Enhanced Scripts with Advanced Features -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
    <script>
        // Enhanced Global Variables
        let charts = {};
        let realtimeInterval;
        let currentTimeFilter = 30;
        let activityPaused = false;
        let chartData = {};
        let systemMetrics = {};
        let activityStats = { total: 1, success: 1, error: 0, info: 0 };

        // Store initial chart data
        window.chartData = @json($chartData ?? []);

        // Enhanced Chart Configuration
        const defaultChartConfig = {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index',
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        font: { size: 11 }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: 'white',
                    bodyColor: 'white',
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 1,
                    cornerRadius: 6,
                    displayColors: true,
                    callbacks: {
                        afterLabel: function(context) {
                            if (context.dataset.label === 'Revenue') {
                                return `Revenue: ${context.parsed.y.toLocaleString()}`;
                            }
                            return '';
                        }
                    }
                }
            },
            animation: {
                duration: 750,
                easing: 'easeInOutQuart'
            }
        };

        // Enhanced Color Palette
        const colors = {
            primary: ['#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6', '#EC4899', '#06B6D4', '#84CC16'],
            gradients: [
                ['#667eea', '#764ba2'],
                ['#f093fb', '#f5576c'],
                ['#4facfe', '#00f2fe'],
                ['#43e97b', '#38f9d7']
            ],
            background: [
                'rgba(59, 130, 246, 0.1)', 'rgba(239, 68, 68, 0.1)', 'rgba(16, 185, 129, 0.1)', 
                'rgba(245, 158, 11, 0.1)', 'rgba(139, 92, 246, 0.1)', 'rgba(236, 72, 153, 0.1)',
                'rgba(6, 182, 212, 0.1)', 'rgba(132, 204, 22, 0.1)'
            ]
        };

        // Enhanced Chart Initialization
        function initializeCharts(data = {}) {
            console.log('Initializing enhanced charts with data:', data);
            chartData = data;

            // Initialize all charts with enhanced features
            initEnhancedUserGrowthChart(data);
            initEnhancedCategoriesChart(data);
            initEnhancedScoreChart(data);
            initEnhancedCategoryPerformanceChart(data);
            initEnhancedStatusChart(data);
            initEnhancedActivityHeatmapChart(data);
            initMiniScoreTrend();
            initializeTables(data);
        }

        // Enhanced User Growth Chart with Predictions
        function initEnhancedUserGrowthChart(data) {
            const ctx = document.getElementById('userGrowthChart')?.getContext('2d');
            if (!ctx) return;

            if (charts.userGrowthChart) {
                charts.userGrowthChart.destroy();
            }

            const mockData = generateEnhancedUserGrowthData();
            
            charts.userGrowthChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: mockData.labels,
                    datasets: [{
                        label: 'New Users',
                        data: mockData.newUsers,
                        borderColor: colors.primary[0],
                        backgroundColor: createGradient(ctx, colors.primary[0]),
                        fill: true,
                        tension: 0.4,
                        pointRadius: 3,
                        pointHoverRadius: 6,
                        pointBackgroundColor: colors.primary[0],
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    }, {
                        label: 'Active Users',
                        data: mockData.activeUsers,
                        borderColor: colors.primary[2],
                        backgroundColor: 'transparent',
                        fill: false,
                        tension: 0.4,
                        pointRadius: 3,
                        pointHoverRadius: 6,
                        borderDash: [5, 5]
                    }, {
                        label: 'Predicted Growth',
                        data: mockData.prediction,
                        borderColor: colors.primary[5],
                        backgroundColor: 'transparent',
                        fill: false,
                        tension: 0.4,
                        pointRadius: 2,
                        borderDash: [10, 5],
                        pointStyle: 'triangle'
                    }]
                },
                options: {
                    ...defaultChartConfig,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0, 0, 0, 0.05)' },
                            ticks: { 
                                callback: function(value) {
                                    return value.toLocaleString();
                                }
                            }
                        },
                        x: {
                            grid: { color: 'rgba(0, 0, 0, 0.05)' }
                        }
                    },
                    plugins: {
                        ...defaultChartConfig.plugins,
                        tooltip: {
                            ...defaultChartConfig.plugins.tooltip,
                            callbacks: {
                                afterBody: function(tooltipItems) {
                                    const dataPoint = tooltipItems[0];
                                    if (dataPoint.datasetIndex === 2) {
                                        return ['', 'Predicted based on current trends'];
                                    }
                                    return '';
                                }
                            }
                        }
                    }
                }
            });
        }

        // Enhanced Categories Chart with Comparison
        function initEnhancedCategoriesChart(data) {
            const ctx = document.getElementById('categoriesChart')?.getContext('2d');
            if (!ctx) return;

            if (charts.categoriesChart) {
                charts.categoriesChart.destroy();
            }

            const categoryData = data.categories || generateMockCategories();
            
            charts.categoriesChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: categoryData.map(item => item.label),
                    datasets: [{
                        data: categoryData.map(item => item.count),
                        backgroundColor: colors.primary,
                        borderWidth: 3,
                        borderColor: '#ffffff',
                        hoverBorderWidth: 5,
                        hoverOffset: 10
                    }]
                },
                options: {
                    ...defaultChartConfig,
                    cutout: '60%',
                    plugins: {
                        ...defaultChartConfig.plugins,
                        legend: {
                            position: 'right',
                            labels: {
                                padding: 15,
                                usePointStyle: true,
                                generateLabels: function(chart) {
                                    const data = chart.data;
                                    if (data.labels.length && data.datasets.length) {
                                        return data.labels.map((label, i) => {
                                            const meta = chart.getDatasetMeta(0);
                                            const ds = data.datasets[0];
                                            const arc = meta.data[i];
                                            const value = ds.data[i];
                                            const total = ds.data.reduce((a, b) => a + b, 0);
                                            const percentage = Math.round((value / total) * 100);
                                            
                                            return {
                                                text: `${label} (${percentage}%)`,
                                                fillStyle: ds.backgroundColor[i],
                                                pointStyle: 'circle',
                                                hidden: isNaN(ds.data[i]) || meta.data[i].hidden,
                                                index: i
                                            };
                                        });
                                    }
                                    return [];
                                }
                            }
                        }
                    }
                }
            });
        }

        // Enhanced Score Chart with Statistics
        function initEnhancedScoreChart(data) {
            const ctx = document.getElementById('scoreChart')?.getContext('2d');
            if (!ctx) return;

            if (charts.scoreChart) {
                charts.scoreChart.destroy();
            }

            const scoreData = data.scores || generateMockScoreData();
            
            charts.scoreChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: scoreData.map(item => item.label),
                    datasets: [{
                        label: 'Count',
                        data: scoreData.map(item => item.count),
                        backgroundColor: createBarGradients(ctx, colors.primary),
                        borderColor: colors.primary,
                        borderWidth: 1,
                        borderRadius: 8,
                        borderSkipped: false,
                        hoverBackgroundColor: colors.primary.map(color => color + '80')
                    }]
                },
                options: {
                    ...defaultChartConfig,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0, 0, 0, 0.05)' },
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString();
                                }
                            }
                        },
                        x: {
                            grid: { display: false }
                        }
                    },
                    plugins: {
                        ...defaultChartConfig.plugins,
                        tooltip: {
                            ...defaultChartConfig.plugins.tooltip,
                            callbacks: {
                                afterLabel: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = Math.round((context.parsed.y / total) * 100);
                                    return `${percentage}% of total scores`;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Enhanced Performance Radar with Benchmarks
        function initEnhancedCategoryPerformanceChart(data) {
            const ctx = document.getElementById('categoryPerformanceChart')?.getContext('2d');
            if (!ctx) return;

            if (charts.categoryPerformanceChart) {
                charts.categoryPerformanceChart.destroy();
            }

            const categoryScores = data.categoryScores || generateMockCategoryScores();
            
            charts.categoryPerformanceChart = new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: categoryScores.map(item => item.label),
                    datasets: [{
                        label: 'Current Performance',
                        data: categoryScores.map(item => item.score),
                        backgroundColor: 'rgba(59, 130, 246, 0.2)',
                        borderColor: colors.primary[0],
                        borderWidth: 3,
                        pointBackgroundColor: colors.primary[0],
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: colors.primary[0],
                        pointRadius: 6,
                        pointHoverRadius: 8,
                    }, {
                        label: 'Industry Average',
                        data: categoryScores.map(item => item.score * 0.85), // Mock industry average
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        borderColor: colors.primary[1],
                        borderWidth: 2,
                        pointBackgroundColor: colors.primary[1],
                        pointBorderColor: '#fff',
                        pointRadius: 4,
                        borderDash: [5, 5]
                    }]
                },
                options: {
                    ...defaultChartConfig,
                    scales: {
                        r: {
                            beginAtZero: true,
                            max: 100,
                            grid: { color: 'rgba(0, 0, 0, 0.1)' },
                            angleLines: { color: 'rgba(0, 0, 0, 0.1)' },
                            pointLabels: {
                                font: { size: 11 }
                            },
                            ticks: {
                                stepSize: 20,
                                font: { size: 10 }
                            }
                        }
                    }
                }
            });
        }

        // Enhanced Status Chart
        function initEnhancedStatusChart(data) {
            const ctx = document.getElementById('statusChart')?.getContext('2d');
            if (!ctx) return;

            if (charts.statusChart) {
                charts.statusChart.destroy();
            }

            const statusData = data.status || generateMockStatusData();
            
            charts.statusChart = new Chart(ctx, {
                type: 'polarArea',
                data: {
                    labels: statusData.map(item => item.label),
                    datasets: [{
                        data: statusData.map(item => item.count),
                        backgroundColor: colors.background,
                        borderColor: colors.primary,
                        borderWidth: 2,
                        hoverBorderWidth: 4
                    }]
                },
                options: {
                    ...defaultChartConfig,
                    scales: {
                        r: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0, 0, 0, 0.1)' },
                            ticks: { 
                                stepSize: 5,
                                font: { size: 10 }
                            }
                        }
                    }
                }
            });
        }

        // Enhanced Activity Heatmap
        function initEnhancedActivityHeatmapChart(data) {
            const ctx = document.getElementById('activityHeatmapChart')?.getContext('2d');
            if (!ctx) return;

            if (charts.activityHeatmapChart) {
                charts.activityHeatmapChart.destroy();
            }

            const heatmapData = generateEnhancedHeatmapData();
            
            charts.activityHeatmapChart = new Chart(ctx, {
                type: 'scatter',
                data: {
                    datasets: [{
                        label: 'Activity Level',
                        data: heatmapData,
                        backgroundColor: function(context) {
                            const value = context.parsed.v || 0;
                            const alpha = Math.min(value / 100, 1);
                            return `rgba(59, 130, 246, ${alpha})`;
                        },
                        pointRadius: function(context) {
                            const value = context.parsed.v || 0;
                            return Math.max(Math.min(value / 8, 15), 3);
                        },
                        pointHoverRadius: function(context) {
                            const value = context.parsed.v || 0;
                            return Math.max(Math.min(value / 6, 20), 5);
                        }
                    }]
                },
                options: {
                    ...defaultChartConfig,
                    scales: {
                        x: {
                            type: 'linear',
                            position: 'bottom',
                            min: 0,
                            max: 6,
                            ticks: {
                                stepSize: 1,
                                callback: function(value) {
                                    const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                                    return days[value] || '';
                                }
                            },
                            title: {
                                display: true,
                                text: 'Day of Week'
                            }
                        },
                        y: {
                            min: 0,
                            max: 23,
                            ticks: {
                                stepSize: 2,
                                callback: function(value) {
                                    return value + ':00';
                                }
                            },
                            title: {
                                display: true,
                                text: 'Hour of Day'
                            }
                        }
                    },
                    plugins: {
                        ...defaultChartConfig.plugins,
                        tooltip: {
                            ...defaultChartConfig.plugins.tooltip,
                            callbacks: {
                                title: function(tooltipItems) {
                                    const item = tooltipItems[0];
                                    const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                                    const day = days[item.parsed.x];
                                    const hour = item.parsed.y;
                                    return `${day} at ${hour}:00`;
                                },
                                label: function(context) {
                                    const value = context.parsed.v || 0;
                                    return `Activity Level: ${Math.round(value)}%`;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Mini Score Trend Chart
        function initMiniScoreTrend() {
            const canvas = document.getElementById('miniScoreTrend');
            if (!canvas) return;
            
            const ctx = canvas.getContext('2d');
            const width = canvas.width;
            const height = canvas.height;
            
            // Generate mini trend data
            const data = Array.from({ length: 7 }, () => Math.random() * 40 + 60);
            
            // Clear canvas
            ctx.clearRect(0, 0, width, height);
            
            // Draw trend line
            ctx.beginPath();
            ctx.strokeStyle = 'rgba(255, 255, 255, 0.8)';
            ctx.lineWidth = 2;
            
            data.forEach((value, index) => {
                const x = (index / (data.length - 1)) * width;
                const y = height - ((value - 60) / 40) * height;
                
                if (index === 0) {
                    ctx.moveTo(x, y);
                } else {
                    ctx.lineTo(x, y);
                }
            });
            
            ctx.stroke();
            
            // Draw points
            ctx.fillStyle = 'rgba(255, 255, 255, 0.9)';
            data.forEach((value, index) => {
                const x = (index / (data.length - 1)) * width;
                const y = height - ((value - 60) / 40) * height;
                
                ctx.beginPath();
                ctx.arc(x, y, 2, 0, 2 * Math.PI);
                ctx.fill();
            });
        }

        // Enhanced Mock Data Generators
        function generateEnhancedUserGrowthData() {
            const days = [];
            const newUsers = [];
            const activeUsers = [];
            const prediction = [];
            let totalActive = 8000;

            for (let i = 29; i >= 0; i--) {
                const date = new Date();
                date.setDate(date.getDate() - i);
                days.push(date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));
                
                const newUsersCount = Math.floor(Math.random() * 100) + 20;
                const activeUsersCount = totalActive + Math.floor(Math.random() * 200) - 100;
                
                newUsers.push(newUsersCount);
                activeUsers.push(activeUsersCount);
                
                totalActive = activeUsersCount;
                
                // Add prediction for last few days
                if (i <= 7) {
                    const trend = newUsers.slice(-5).reduce((a, b) => a + b, 0) / 5;
                    prediction.push(Math.round(trend * (1.1 + Math.random() * 0.2)));
                } else {
                    prediction.push(null);
                }
            }

            return { labels: days, newUsers, activeUsers, prediction };
        }

        function generateMockCategories() {
            return [
                { label: 'Programming', count: 45 },
                { label: 'Design', count: 32 },
                { label: 'Marketing', count: 28 },
                { label: 'Business', count: 25 },
                { label: 'Data Science', count: 18 }
            ];
        }

        function generateMockScoreData() {
            return [
                { label: 'Excellent (90-100)', count: 125 },
                { label: 'Good (80-89)', count: 200 },
                { label: 'Average (70-79)', count: 180 },
                { label: 'Below Avg (60-69)', count: 95 },
                { label: 'Poor (<60)', count: 45 }
            ];
        }

        function generateMockCategoryScores() {
            return [
                { label: 'Technical Skills', score: 85 },
                { label: 'Creativity', score: 78 },
                { label: 'Communication', score: 82 },
                { label: 'Problem Solving', score: 88 },
                { label: 'Collaboration', score: 75 }
            ];
        }

        function generateMockStatusData() {
            return [
                { label: 'Published', count: 45 },
                { label: 'Draft', count: 12 },
                { label: 'In Review', count: 8 },
                { label: 'Archived', count: 5 }
            ];
        }

        function generateEnhancedHeatmapData() {
            const data = [];
            for (let day = 0; day < 7; day++) {
                for (let hour = 0; hour < 24; hour++) {
                    // Create realistic activity patterns
                    let baseActivity = 20;
                    
                    // Higher activity during work hours on weekdays
                    if (day >= 1 && day <= 5 && hour >= 9 && hour <= 17) {
                        baseActivity = 70;
                    }
                    
                    // Evening activity spike
                    if ((day === 0 || day === 6) && hour >= 19 && hour <= 22) {
                        baseActivity = 60;
                    }
                    
                    // Add some randomness
                    const activity = Math.max(0, Math.min(100, baseActivity + (Math.random() - 0.5) * 40));
                    
                    data.push({ x: day, y: hour, v: activity });
                }
            }
            return data;
        }

        // Enhanced Utility Functions
        function createGradient(ctx, color) {
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, color + '40');
            gradient.addColorStop(1, color + '00');
            return gradient;
        }

        function createBarGradients(ctx, colors) {
            return colors.map(color => {
                const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                gradient.addColorStop(0, color);
                gradient.addColorStop(1, color + '80');
                return gradient;
            });
        }

        // Enhanced Table Initialization
        function initializeTables(data) {
            const topCoursesData = [
                { title: 'React Fundamentals', category: 'Web Development', enrollments: 1250, rating: 4.8, completion: 85, revenue: 24750 },
                { title: 'Python for Beginners', category: 'Programming', enrollments: 980, rating: 4.6, completion: 78, revenue: 19600 },
                { title: 'Digital Marketing', category: 'Marketing', enrollments: 756, rating: 4.7, completion: 92, revenue: 15120 },
                { title: 'Data Science Basics', category: 'Data Science', enrollments: 643, rating: 4.5, completion: 71, revenue: 12860 },
                { title: 'UI/UX Design', category: 'Design', enrollments: 521, rating: 4.9, completion: 88, revenue: 10420 },
            ];
            populateEnhancedTopCoursesTable(topCoursesData);
        }

        function populateEnhancedTopCoursesTable(data) {
            const tbody = document.getElementById('topCoursesBody');
            if (!tbody) return;

            tbody.innerHTML = '';
            data.forEach((course, index) => {
                const row = document.createElement('tr');
                row.className = 'hover:bg-gray-50 transition-colors duration-200 cursor-pointer';
                row.onclick = () => showCourseDetails(course);
                
                const trendIcon = course.rating > 4.7 ? '📈' : course.rating > 4.5 ? '➡️' : '📉';
                const revenueBadge = course.revenue > 20000 ? 'bg-green-100 text-green-800' : 
                                   course.revenue > 15000 ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800';

                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 relative">
                                <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-400 to-blue-600 flex items-center justify-center">
                                    <span class="text-sm font-medium text-white">${course.title.charAt(0)}</span>
                                </div>
                                <div class="absolute -top-1 -right-1 text-xs">${index + 1}</div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">${course.title}</div>
                                <div class="text-sm text-gray-500 flex items-center">
                                    ${course.category}
                                    <span class="ml-2">${trendIcon}</span>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 font-medium">${course.enrollments.toLocaleString()}</div>
                        <div class="text-xs text-gray-500">+${Math.floor(Math.random() * 50)} this week</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="text-sm text-gray-900 font-medium">${course.rating}</div>
                            <div class="ml-2 text-yellow-400">
                                ${'★'.repeat(Math.floor(course.rating))}${'☆'.repeat(5 - Math.floor(course.rating))}
                            </div>
                        </div>
                        <div class="text-xs text-gray-500">${Math.floor(Math.random() * 200) + 100} reviews</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <span class="text-sm font-medium px-2 py-1 rounded-full text-xs ${revenueBadge}">
                                ${course.revenue.toLocaleString()}
                            </span>
                        </div>
                        <div class="text-xs text-gray-500 mt-1">${Math.round(course.revenue / course.enrollments)} avg/user</div>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }

        // Enhanced Real-time Updates
        function updateCharts(data) {
            // Just reinitialize with new data instead of destroying all charts
            initializeCharts(data);
        }

        function fetchRealtimeData() {
            console.log('Fetching enhanced real-time data...');
            showLoadingWithProgress();
            
            // Simulate API call with mock data updates
            setTimeout(() => {
                updateLoadingProgress(50);
                
                // Generate some mock updated data
                const mockUpdatedData = {
                    realtimeMetrics: {
                        avg_overall_score: (85 + Math.random() * 10).toFixed(1),
                        completion_rate: (75 + Math.random() * 10).toFixed(1),
                        last_updated: new Date().toLocaleTimeString()
                    },
                    categories: generateMockCategories(),
                    scores: generateMockScoreData(),
                    categoryScores: generateMockCategoryScores(),
                    status: generateMockStatusData()
                };
                
                updateLoadingProgress(80);
                hideLoading();
                
                updateMetrics(mockUpdatedData);
                updateCharts(mockUpdatedData);
                initializeTables(mockUpdatedData);
                addActivityItem('Data refreshed successfully', 'success');
                updateStatusIndicator('live');
                showNotification('Success', 'Dashboard data updated successfully', 'success');
                
                updateLoadingProgress(100);
            }, 2000);
        }

        function updateMetrics(data) {
            if (data.realtimeMetrics) {
                const metrics = data.realtimeMetrics;
                
                // Update KPI values with animation
                animateValue('avg-score', parseFloat(document.getElementById('avg-score').textContent) || 0, parseFloat(metrics.avg_overall_score) || 0, 1000);
                
                const completionElement = document.getElementById('completion-rate');
                if (completionElement) {
                    const currentValue = parseFloat(completionElement.textContent.replace('%', '')) || 0;
                    const newValue = parseFloat(metrics.completion_rate) || 0;
                    animateValue('completion-rate', currentValue, newValue, 1000, '%');
                    
                    // Update completion progress bar
                    const progressBar = document.getElementById('completionProgress');
                    if (progressBar) {
                        progressBar.style.width = newValue + '%';
                    }
                }
                
                document.getElementById('last-updated').textContent = metrics.last_updated;
                
                // Add pulsing effect to updated elements
                ['avg-score', 'completion-rate'].forEach(id => {
                    const element = document.getElementById(id);
                    if (element) {
                        element.classList.add('animate-pulse');
                        setTimeout(() => {
                            element.classList.remove('animate-pulse');
                        }, 2000);
                    }
                });
            }
        }

        // Enhanced Utility Functions
        function animateValue(elementId, start, end, duration, suffix = '') {
            const element = document.getElementById(elementId);
            if (!element) return;
            
            const range = end - start;
            const increment = range / (duration / 16);
            let current = start;
            
            const timer = setInterval(() => {
                current += increment;
                if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
                    current = end;
                    clearInterval(timer);
                }
                element.textContent = Math.round(current * 10) / 10 + suffix;
            }, 16);
        }

        function showLoadingWithProgress() {
            document.getElementById('loadingOverlay').classList.remove('hidden');
            updateLoadingProgress(0);
        }

        function updateLoadingProgress(percentage) {
            const progressBar = document.getElementById('loadingProgress');
            if (progressBar) {
                progressBar.style.width = percentage + '%';
            }
        }

        function hideLoading() {
            setTimeout(() => {
                document.getElementById('loadingOverlay').classList.add('hidden');
            }, 200);
        }

        function showNotification(title, message, type = 'info') {
            const notification = document.getElementById('notification');
            const titleEl = document.getElementById('notificationTitle');
            const messageEl = document.getElementById('notificationMessage');
            const iconEl = document.getElementById('notificationIcon');
            
            // Set content
            titleEl.textContent = title;
            messageEl.textContent = message;
            
            // Set icon based on type
            const icons = {
                success: '<svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>',
                error: '<svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>',
                info: '<svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>'
            };
            
            iconEl.innerHTML = icons[type] || icons.info;
            
            // Show notification
            notification.style.transform = 'translateX(0)';
            
            // Auto hide after 5 seconds
            setTimeout(() => {
                notification.style.transform = 'translateX(100%)';
            }, 5000);
        }

        function addActivityItem(message, type = 'info', user = '') {
            if (activityPaused) return;
            
            const feed = document.getElementById('activity-feed');
            if (!feed) return;
            
            const now = new Date().toLocaleTimeString();
            
            // Update activity stats
            activityStats.total++;
            activityStats[type] = (activityStats[type] || 0) + 1;
            updateActivityStats();
            
            // Create activity item
            const item = document.createElement('div');
            item.className = 'animate-fade-in';
            
            const iconColors = {
                'success': 'bg-green-500',
                'error': 'bg-red-500',
                'info': 'bg-blue-500',
                'warning': 'bg-yellow-500'
            };
            
            const colorClass = iconColors[type] || 'bg-gray-500';
            
            item.innerHTML = `
                <div class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200 border-l-2 border-${type === 'success' ? 'green' : type === 'error' ? 'red' : 'blue'}-500">
                    <div class="flex-shrink-0 w-8 h-8 ${colorClass} rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        ${user ? `<div class="text-sm font-medium text-gray-900">${user}</div>` : ''}
                        <div class="text-sm ${user ? 'text-gray-500' : 'text-gray-600'}">${message}</div>
                    </div>
                    <div class="text-xs text-gray-400">${now}</div>
                </div>
            `;

            feed.insertBefore(item, feed.firstChild);

            // Keep only last 15 items
            while (feed.children.length > 15) {
                feed.removeChild(feed.lastChild);
            }
        }

        function updateActivityStats() {
            document.getElementById('feedCount').textContent = activityStats.total;
            document.getElementById('successCount').textContent = activityStats.success || 0;
            document.getElementById('errorCount').textContent = activityStats.error || 0;
            document.getElementById('infoCount').textContent = activityStats.info || 0;
        }

        function updateStatusIndicator(status) {
            const statusIndicator = document.getElementById('realtime-status');
            if (statusIndicator) {
                const indicator = statusIndicator.querySelector('div');
                const text = statusIndicator.querySelector('span');
                
                if (status === 'live') {
                    statusIndicator.className = 'flex items-center bg-green-50 px-2 py-1 rounded-md';
                    if (indicator) indicator.className = 'w-2 h-2 bg-green-500 rounded-full animate-pulse mr-2';
                    if (text) {
                        text.textContent = 'Live';
                        text.className = 'text-xs text-green-700 font-medium';
                    }
                } else if (status === 'error') {
                    statusIndicator.className = 'flex items-center bg-red-50 px-2 py-1 rounded-md';
                    if (indicator) indicator.className = 'w-2 h-2 bg-red-500 rounded-full mr-2';
                    if (text) {
                        text.textContent = 'Error';
                        text.className = 'text-xs text-red-700 font-medium';
                    }
                }
            }
        }

        // Enhanced Interactive Functions
        function showUserDetails() {
            showNotification('User Details', 'Drilling down into user analytics...', 'info');
            // Implement user details modal or navigation
        }

        function showCourseBreakdown() {
            showNotification('Course Breakdown', 'Loading detailed course analytics...', 'info');
            // Implement course breakdown view
        }

        function showCourseDetails(course) {
            showNotification('Course Details', `Loading details for ${course.title}...`, 'info');
            // Implement course details modal
        }

        function exportChart(chartId) {
            if (charts[chartId]) {
                const url = charts[chartId].toBase64Image();
                const link = document.createElement('a');
                link.download = `${chartId}_${new Date().toISOString().split('T')[0]}.png`;
                link.href = url;
                link.click();
                showNotification('Export', 'Chart exported successfully', 'success');
            }
        }

        function toggleCategoryView() {
            // Toggle between doughnut and bar chart
            const chart = charts.categoriesChart;
            if (chart) {
                chart.config.type = chart.config.type === 'doughnut' ? 'bar' : 'doughnut';
                chart.update();
            }
        }

        function filterScoreData(category) {
            showNotification('Filter Applied', `Filtering scores by ${category}`, 'info');
            // Implement score filtering logic
        }

        function toggleBenchmarks() {
            const chart = charts.categoryPerformanceChart;
            if (chart && chart.data.datasets.length > 1) {
                const benchmarkDataset = chart.data.datasets[1];
                benchmarkDataset.hidden = !benchmarkDataset.hidden;
                chart.update();
                
                const button = document.querySelector('.benchmark-toggle');
                button.textContent = benchmarkDataset.hidden ? 'Show Industry Avg' : 'Hide Industry Avg';
            }
        }

        function updateHeatmapView(viewType) {
            showNotification('View Updated', `Switched to ${viewType} view`, 'info');
            // Implement heatmap view switching
        }

        function showHeatmapInsights() {
            const insights = document.getElementById('heatmapInsights');
            insights.classList.toggle('hidden');
        }

        function refreshSystemMetrics() {
            showNotification('System Metrics', 'Refreshing system performance data...', 'info');
            // Simulate system metrics update
            setTimeout(() => {
                document.getElementById('cpuUsage').textContent = Math.floor(Math.random() * 100) + '%';
                document.getElementById('memoryUsage').textContent = Math.floor(Math.random() * 100) + '%';
                showNotification('Success', 'System metrics updated', 'success');
            }, 1000);
        }

        function optimizeDatabase() {
            showNotification('Database', 'Running database optimization...', 'info');
            addActivityItem('Database optimization started', 'info');
        }

        function viewAPILogs() {
            showNotification('API Logs', 'Opening API performance logs...', 'info');
        }

        function filterCourses(period) {
            showNotification('Filter', `Filtering courses by ${period}`, 'info');
        }

        function exportTableData(type) {
            showNotification('Export', `Exporting ${type} data...`, 'info');
        }

        function showTableSettings(type) {
            showNotification('Settings', `Opening ${type} table settings...`, 'info');
        }

        function filterActivities(type) {
            showNotification('Filter', `Filtering activities by ${type}`, 'info');
        }

        function exportActivities() {
            showNotification('Export', 'Exporting activity data...', 'info');
        }

        // Enhanced Event Listeners
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Enhanced component-based dashboard initializing...');
            
            const initialData = window.chartData || {};
            initializeCharts(initialData);

            // Enhanced filter listeners
            document.getElementById('dateRange').addEventListener('change', function(e) {
                currentTimeFilter = e.target.value;
                addActivityItem(`Date range changed to: ${e.target.options[e.target.selectedIndex].text}`, 'info');
                fetchRealtimeData();
            });

            document.getElementById('categoryFilter').addEventListener('change', function(e) {
                addActivityItem(`Filter applied: ${e.target.options[e.target.selectedIndex].text}`, 'info');
                // Implement category filtering
            });

            // View mode toggle
            document.querySelectorAll('.view-toggle').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.view-toggle').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    addActivityItem(`View changed to: ${this.textContent.trim()}`, 'info');
                });
            });

            // Chart period buttons
            document.querySelectorAll('.chart-period-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const chartGroup = this.dataset.chart || 'default';
                    document.querySelectorAll(`[data-chart="${chartGroup}"]`).forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    addActivityItem(`Chart period changed: ${this.textContent}`, 'info');
                });
            });

            // Enhanced action buttons
            document.getElementById('refreshBtn').addEventListener('click', function() {
                fetchRealtimeData();
            });

            document.getElementById('exportBtn').addEventListener('click', function() {
                showNotification('Export', 'Preparing dashboard export...', 'info');
                addActivityItem('Dashboard export initiated', 'info');
            });

            document.getElementById('settingsBtn').addEventListener('click', function() {
                showNotification('Settings', 'Dashboard settings opened', 'info');
            });

            // Activity feed controls
            document.getElementById('pauseActivityFeed')?.addEventListener('click', function() {
                activityPaused = !activityPaused;
                this.innerHTML = activityPaused ? 
                    '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8 7a1 1 0 012 0v6a1 1 0 11-2 0V7zM12 7a1 1 0 012 0v6a1 1 0 11-2 0V7z" clip-rule="evenodd"/></svg>' :
                    '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM7 8a1 1 0 012 0v4a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v4a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>';
                addActivityItem(activityPaused ? 'Activity feed paused' : 'Activity feed resumed', 'info');
            });

            document.getElementById('clearActivityFeed')?.addEventListener('click', function() {
                const feed = document.getElementById('activity-feed');
                if (feed) {
                    feed.innerHTML = '<div class="flex items-center p-3 bg-gray-50 rounded-lg"><div class="flex-shrink-0 w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center"><svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg></div><div class="ml-3 flex-1"><div class="text-sm text-gray-600">Activity feed cleared</div></div><div class="text-xs text-gray-400">' + new Date().toLocaleTimeString() + '</div></div>';
                    activityStats = { total: 1, success: 0, error: 0, info: 1 };
                    updateActivityStats();
                }
            });

            // Notification close button
            document.getElementById('closeNotification')?.addEventListener('click', function() {
                document.getElementById('notification').style.transform = 'translateX(100%)';
            });

            // Dismiss insight banner
            document.getElementById('dismissInsight')?.addEventListener('click', function() {
                this.closest('.bg-gradient-to-r').style.display = 'none';
            });

            // Table search functionality
            const searchInput = document.getElementById('topCoursesTable_search');
            if (searchInput) {
                searchInput.addEventListener('input', function(e) {
                    const searchTerm = e.target.value.toLowerCase();
                    const rows = document.querySelectorAll('#topCoursesBody tr');
                    
                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = text.includes(searchTerm) ? '' : 'none';
                    });
                    
                    addActivityItem(`Search applied: "${searchTerm}"`, 'info');
                });
            }

            // Start enhanced real-time updates
            realtimeInterval = setInterval(fetchRealtimeData, 45000); // 45 second intervals
            updateStatusIndicator('live');
            
            // Initial enhanced fetch after 3 seconds
            setTimeout(() => {
                addActivityItem('Enhanced real-time monitoring initialized', 'success');
                fetchRealtimeData();
            }, 3000);
        });

        // Cleanup
        window.addEventListener('beforeunload', function() {
            if (realtimeInterval) {
                clearInterval(realtimeInterval);
            }
        });
    </script>

    <style>
        .chart-period-btn {
            @apply px-3 py-1 text-xs font-medium text-gray-500 bg-gray-100 rounded-md hover:bg-gray-200 transition-all duration-200;
        }
        
        .chart-period-btn.active {
            @apply bg-blue-600 text-white hover:bg-blue-700 shadow-md;
        }
        
        .view-toggle {
            @apply text-gray-600 hover:text-gray-800 hover:bg-gray-200 transition-all duration-200;
        }
        
        .view-toggle.active {
            @apply bg-blue-600 text-white hover:bg-blue-700;
        }
        
        .animate-fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Enhanced scrollbar */
        #activity-feed::-webkit-scrollbar {
            width: 6px;
        }
        
        #activity-feed::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 6px;
        }
        
        #activity-feed::-webkit-scrollbar-thumb {
            background: linear-gradient(to bottom, #3b82f6, #1d4ed8);
            border-radius: 6px;
        }
        
        #activity-feed::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(to bottom, #2563eb, #1e40af);
        }

        /* Mobile-first responsive design */
        @media (max-width: 640px) {
            .chart-period-btn {
                @apply px-2 py-1 text-2xs;
            }
            
            #activity-feed {
                max-height: 200px;
            }
        }

        /* Loading animation */
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(59, 130, 246, 0); }
        }
        
        .animate-pulse-glow {
            animation: pulse-glow 2s infinite;
        }
    </style>
</x-app-layout>