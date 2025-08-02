<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow-sm">
        <!-- Header -->
        <div class="border-b border-gray-200 px-6 py-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900">Import/Export Data</h1>
                <button wire:click="showImportModal" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    Import Data
                </button>
            </div>
        </div>

        <div class="flex">
            <!-- Sidebar -->
            <div class="w-64 border-r border-gray-200">
                <nav class="p-4 space-y-2">
                    <a wire:click="setActiveTab('import')" 
                       class="block px-3 py-2 rounded-md text-sm font-medium cursor-pointer {{ $activeTab === 'import' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        📥 Import Jobs
                    </a>
                    <a wire:click="setActiveTab('export')" 
                       class="block px-3 py-2 rounded-md text-sm font-medium cursor-pointer {{ $activeTab === 'export' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        📤 Export Data
                    </a>
                    <a wire:click="setActiveTab('templates')" 
                       class="block px-3 py-2 rounded-md text-sm font-medium cursor-pointer {{ $activeTab === 'templates' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        📄 Templates
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="flex-1">
                @if($activeTab === 'import')
                    <!-- Import Jobs List -->
                    <div class="p-4 border-b border-gray-200">
                        <input type="text" wire:model.live="search" placeholder="Search import jobs..." 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div class="p-6">
                        @if($importJobs->count() > 0)
                            <div class="space-y-4">
                                @foreach($importJobs as $job)
                                    <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                                        <div class="flex items-center justify-between mb-3">
                                            <div class="flex items-center space-x-3">
                                                <h3 class="text-lg font-medium text-gray-900">{{ $job->filename }}</h3>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                    {{ $job->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                                       ($job->status === 'failed' ? 'bg-red-100 text-red-800' : 
                                                        ($job->status === 'processing' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) }}">
                                                    {{ ucfirst($job->status) }}
                                                </span>
                                            </div>
                                            <span class="text-sm text-gray-500">{{ $job->created_at->diffForHumans() }}</span>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                            <div>
                                                <p class="text-sm text-gray-600">Type</p>
                                                <p class="font-medium">{{ ucfirst($job->type) }}</p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-600">Records Processed</p>
                                                <p class="font-medium">{{ number_format($job->processed_count) }}</p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-600">Success Rate</p>
                                                <p class="font-medium">
                                                    @if($job->processed_count > 0)
                                                        {{ round(($job->success_count / $job->processed_count) * 100, 1) }}%
                                                    @else
                                                        N/A
                                                    @endif
                                                </p>
                                            </div>
                                        </div>

                                        @if($job->status === 'processing')
                                            <div class="w-full bg-gray-200 rounded-full h-2 mb-4">
                                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $job->progress_percentage }}%"></div>
                                            </div>
                                        @endif

                                        @if($job->error_message)
                                            <div class="bg-red-50 border border-red-200 rounded-md p-3 mb-4">
                                                <p class="text-sm text-red-700">{{ $job->error_message }}</p>
                                            </div>
                                        @endif
                                        
                                        <div class="flex space-x-2">
                                            @if($job->status === 'failed')
                                                <button wire:click="retryImportJob({{ $job->id }})" 
                                                        class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                    Retry
                                                </button>
                                            @endif
                                            @if($job->status === 'completed' && $job->error_count > 0)
                                                <button class="text-yellow-600 hover:text-yellow-800 text-sm font-medium">
                                                    View Errors
                                                </button>
                                            @endif
                                            <button wire:click="deleteImportJob({{ $job->id }})" 
                                                    class="text-red-600 hover:text-red-800 text-sm font-medium">
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Pagination -->
                            <div class="mt-6">
                                {{ $importJobs->links() }}
                            </div>
                        @else
                            <div class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No import jobs found</h3>
                                <p class="mt-1 text-sm text-gray-500">Start by importing your first data file.</p>
                            </div>
                        @endif
                    </div>

                @elseif($activeTab === 'export')
                    <!-- Export Data -->
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6">Export Your Data</h2>
                        
                        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                            <!-- Contacts Export -->
                            <div class="border border-gray-200 rounded-lg p-6">
                                <div class="flex items-center mb-4">
                                    <svg class="w-8 h-8 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900">Contacts</h3>
                                </div>
                                <p class="text-sm text-gray-600 mb-4">Export all your contact information including names, emails, phone numbers, and associated companies.</p>
                                <button wire:click="exportData('contacts')" 
                                        class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                    Export Contacts
                                </button>
                            </div>

                            <!-- Companies Export -->
                            <div class="border border-gray-200 rounded-lg p-6">
                                <div class="flex items-center mb-4">
                                    <svg class="w-8 h-8 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900">Companies</h3>
                                </div>
                                <p class="text-sm text-gray-600 mb-4">Export company data including names, contact information, industry, employee count, and revenue data.</p>
                                <button wire:click="exportData('companies')" 
                                        class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                                    Export Companies
                                </button>
                            </div>

                            <!-- Deals Export -->
                            <div class="border border-gray-200 rounded-lg p-6">
                                <div class="flex items-center mb-4">
                                    <svg class="w-8 h-8 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900">Deals</h3>
                                </div>
                                <p class="text-sm text-gray-600 mb-4">Export deal pipeline data including values, stages, probabilities, contacts, and companies.</p>
                                <button wire:click="exportData('deals')" 
                                        class="w-full bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700">
                                    Export Deals
                                </button>
                            </div>
                        </div>
                    </div>

                @elseif($activeTab === 'templates')
                    <!-- Import Templates -->
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6">Import Templates</h2>
                        
                        <div class="space-y-6">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                                <h3 class="text-lg font-medium text-blue-900 mb-2">Download Sample Templates</h3>
                                <p class="text-sm text-blue-700 mb-4">Use these pre-formatted templates to ensure your data imports successfully.</p>
                                
                                <div class="grid gap-3 md:grid-cols-3">
                                    <a href="#" class="inline-flex items-center px-4 py-2 border border-blue-300 rounded-md text-sm font-medium text-blue-700 bg-white hover:bg-blue-50">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        Contacts Template
                                    </a>
                                    <a href="#" class="inline-flex items-center px-4 py-2 border border-blue-300 rounded-md text-sm font-medium text-blue-700 bg-white hover:bg-blue-50">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        Companies Template
                                    </a>
                                    <a href="#" class="inline-flex items-center px-4 py-2 border border-blue-300 rounded-md text-sm font-medium text-blue-700 bg-white hover:bg-blue-50">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        Deals Template
                                    </a>
                                </div>
                            </div>

                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                                <h3 class="text-lg font-medium text-yellow-900 mb-2">Import Guidelines</h3>
                                <ul class="text-sm text-yellow-800 space-y-2">
                                    <li>• Use CSV or Excel formats (.csv, .xlsx, .xls)</li>
                                    <li>• Include column headers in the first row</li>
                                    <li>• Ensure email addresses are properly formatted</li>
                                    <li>• Phone numbers can include country codes</li>
                                    <li>• Date fields should use YYYY-MM-DD format</li>
                                    <li>• Maximum file size: 10MB</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    @if($showImportModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Import Data</h3>
                        <button wire:click="hideImportModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <form wire:submit.prevent="startImport" class="space-y-6">
                        <!-- File Upload -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Select File</label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                            <span>Upload a file</span>
                                            <input type="file" wire:model="file" class="sr-only" accept=".csv,.xlsx,.xls">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">CSV, XLSX, XLS up to 10MB</p>
                                </div>
                            </div>
                            @error('file') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Import Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Import Type</label>
                            <select wire:model="importType" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="contacts">Contacts</option>
                                <option value="companies">Companies</option>
                                <option value="deals">Deals</option>
                            </select>
                            @error('importType') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- File has headers checkbox -->
                        <div class="flex items-center">
                            <input type="checkbox" wire:model="hasHeaders" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label class="ml-2 block text-sm text-gray-900">File has headers in first row</label>
                        </div>

                        <!-- Preview and Column Mapping -->
                        @if(!empty($previewData))
                            <div class="border border-gray-200 rounded-lg p-4">
                                <h4 class="text-lg font-medium text-gray-900 mb-3">File Preview & Column Mapping</h4>
                                
                                @if(!empty($previewData['headers']))
                                    <div class="space-y-3">
                                        @foreach($previewData['headers'] as $index => $header)
                                            <div class="grid grid-cols-2 gap-4 items-center">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">{{ $header }}</label>
                                                    @if(!empty($previewData['rows'][0][$index]))
                                                        <p class="text-sm text-gray-500">Sample: {{ $previewData['rows'][0][$index] }}</p>
                                                    @endif
                                                </div>
                                                <div>
                                                    <select wire:model="columnMappings.{{ $index }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                        <option value="">Skip this column</option>
                                                        @foreach($availableFields as $field => $label)
                                                            <option value="{{ $field }}">{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif
                        
                        <div class="flex justify-end space-x-3">
                            <button type="button" wire:click="hideImportModal" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                                    @if(empty($previewData)) disabled @endif>
                                Start Import
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