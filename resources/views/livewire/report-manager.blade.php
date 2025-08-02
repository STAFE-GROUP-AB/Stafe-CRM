<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow-sm">
        <!-- Header -->
        <div class="border-b border-gray-200 px-6 py-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900">Advanced Reporting & Analytics</h1>
                <button wire:click="showCreateModal" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Create Report
                </button>
            </div>
        </div>

        <div class="flex">
            <!-- Sidebar -->
            <div class="w-64 border-r border-gray-200">
                <nav class="p-4 space-y-2">
                    <a wire:click="setActiveTab('analytics')" 
                       class="block px-3 py-2 rounded-md text-sm font-medium cursor-pointer {{ $activeTab === 'analytics' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        📊 Analytics Dashboard
                    </a>
                    <a wire:click="setActiveTab('reports')" 
                       class="block px-3 py-2 rounded-md text-sm font-medium cursor-pointer {{ $activeTab === 'reports' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        📈 My Reports
                    </a>
                    @if($selectedReportData)
                        <a wire:click="setActiveTab('view')" 
                           class="block px-3 py-2 rounded-md text-sm font-medium cursor-pointer {{ $activeTab === 'view' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                            👁️ View Report
                        </a>
                    @endif
                </nav>
            </div>

            <!-- Main Content -->
            <div class="flex-1">
                @if($activeTab === 'analytics')
                    <!-- Analytics Dashboard -->
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6">Real-time Analytics Dashboard</h2>
                        
                        <!-- KPI Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-6 text-white">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-blue-100 text-sm">Total Contacts</p>
                                        <p class="text-2xl font-bold">{{ number_format($analyticsData['total_contacts']) }}</p>
                                        <p class="text-blue-100 text-sm">+{{ $analyticsData['contacts_this_month'] }} this month</p>
                                    </div>
                                    <svg class="w-8 h-8 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                            </div>

                            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-6 text-white">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-green-100 text-sm">Total Companies</p>
                                        <p class="text-2xl font-bold">{{ number_format($analyticsData['total_companies']) }}</p>
                                        <p class="text-green-100 text-sm">Active organizations</p>
                                    </div>
                                    <svg class="w-8 h-8 text-green-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                            </div>

                            <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-6 text-white">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-purple-100 text-sm">Total Deals</p>
                                        <p class="text-2xl font-bold">{{ number_format($analyticsData['total_deals']) }}</p>
                                        <p class="text-purple-100 text-sm">+{{ $analyticsData['deals_this_month'] }} this month</p>
                                    </div>
                                    <svg class="w-8 h-8 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                </div>
                            </div>

                            <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-lg p-6 text-white">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-yellow-100 text-sm">Deal Value</p>
                                        <p class="text-2xl font-bold">${{ number_format($analyticsData['total_deal_value']) }}</p>
                                        <p class="text-yellow-100 text-sm">{{ $analyticsData['deal_conversion_rate'] }}% conversion</p>
                                    </div>
                                    <svg class="w-8 h-8 text-yellow-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Activity Summary -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Activity Summary</h3>
                            <div class="flex items-center space-x-4">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                                    <span class="text-sm text-gray-600">{{ $analyticsData['recent_activities'] }} activities this week</span>
                                </div>
                            </div>
                        </div>
                    </div>

                @elseif($activeTab === 'reports')
                    <!-- Reports List -->
                    <div class="p-4 border-b border-gray-200">
                        <input type="text" wire:model.live="search" placeholder="Search reports..." 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div class="p-6">
                        @if($reports->count() > 0)
                            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                                @foreach($reports as $report)
                                    <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                                        <div class="flex items-center justify-between mb-3">
                                            <h3 class="text-lg font-medium text-gray-900">{{ $report->name }}</h3>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                {{ $report->generated_at ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ $report->generated_at ? 'Generated' : 'Draft' }}
                                            </span>
                                        </div>
                                        
                                        @if($report->description)
                                            <p class="text-sm text-gray-600 mb-4">{{ Str::limit($report->description, 100) }}</p>
                                        @endif
                                        
                                        <div class="flex items-center justify-between mb-4">
                                            <span class="text-xs text-gray-500">Type: {{ ucfirst($report->type) }}</span>
                                            <span class="text-xs text-gray-500">{{ $report->created_at->diffForHumans() }}</span>
                                        </div>
                                        
                                        <div class="flex space-x-2">
                                            @if($report->generated_at)
                                                <button wire:click="$set('selectedReport', {{ $report->id }}); setActiveTab('view')" 
                                                        class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                    View Report
                                                </button>
                                            @endif
                                            <button wire:click="generateReport({{ $report->id }})" 
                                                    class="text-green-600 hover:text-green-800 text-sm font-medium">
                                                {{ $report->generated_at ? 'Regenerate' : 'Generate' }}
                                            </button>
                                            <button wire:click="deleteReport({{ $report->id }})" 
                                                    class="text-red-600 hover:text-red-800 text-sm font-medium">
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Pagination -->
                            <div class="mt-6">
                                {{ $reports->links() }}
                            </div>
                        @else
                            <div class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No reports found</h3>
                                <p class="mt-1 text-sm text-gray-500">Get started by creating your first report.</p>
                            </div>
                        @endif
                    </div>

                @elseif($activeTab === 'view' && $selectedReportData)
                    <!-- Report View -->
                    <div class="p-6">
                        <div class="mb-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h2 class="text-xl font-semibold text-gray-900">{{ $selectedReportData->name }}</h2>
                                    @if($selectedReportData->description)
                                        <p class="text-sm text-gray-600 mt-1">{{ $selectedReportData->description }}</p>
                                    @endif
                                </div>
                                <div class="flex space-x-2">
                                    <button wire:click="generateReport({{ $selectedReportData->id }})" 
                                            class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 text-sm">
                                        Regenerate
                                    </button>
                                    <button class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 text-sm">
                                        Export
                                    </button>
                                </div>
                            </div>
                        </div>

                        @if($selectedReportData->generated_at && $selectedReportData->data)
                            @php $reportData = json_decode($selectedReportData->data, true); @endphp
                            
                            <!-- Report Summary -->
                            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Report Summary</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="text-center">
                                        <p class="text-2xl font-bold text-blue-600">{{ number_format($reportData['total_records'] ?? 0) }}</p>
                                        <p class="text-sm text-gray-600">Total Records</p>
                                    </div>
                                    @if(isset($reportData['summary']))
                                        @foreach($reportData['summary'] as $key => $value)
                                            <div class="text-center">
                                                <p class="text-2xl font-bold text-green-600">{{ is_numeric($value) ? number_format($value) : $value }}</p>
                                                <p class="text-sm text-gray-600">{{ ucfirst(str_replace('_', ' ', $key)) }}</p>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            <!-- Generated timestamp -->
                            <div class="text-sm text-gray-500 mb-4">
                                Generated on {{ $selectedReportData->generated_at->format('M j, Y g:i A') }}
                            </div>
                        @else
                            <div class="text-center py-8">
                                <p class="text-gray-500">This report hasn't been generated yet.</p>
                                <button wire:click="generateReport({{ $selectedReportData->id }})" 
                                        class="mt-4 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                    Generate Report
                                </button>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Create Report Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Create New Report</h3>
                        <button wire:click="hideCreateModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <form wire:submit.prevent="createReport" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Report Name</label>
                            <input type="text" wire:model="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Description (Optional)</label>
                            <textarea wire:model="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                            @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Report Type</label>
                            <select wire:model="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="contacts">Contacts</option>
                                <option value="companies">Companies</option>
                                <option value="deals">Deals</option>
                                <option value="activities">Activities</option>
                            </select>
                            @error('type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="flex justify-end space-x-3">
                            <button type="button" wire:click="hideCreateModal" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Create Report
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" 
             class="fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow-lg z-50">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" 
             class="fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow-lg z-50">
            {{ session('error') }}
        </div>
    @endif
</div>