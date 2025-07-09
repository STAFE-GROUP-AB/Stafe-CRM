<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Lead Scoring Dashboard</h1>
                <p class="mt-1 text-sm text-gray-600">
                    AI-powered lead scoring to identify your best prospects.
                </p>
            </div>
            <button wire:click="calculateAllScores" 
                    {{ $isCalculating ? 'disabled' : '' }}
                    class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                @if($isCalculating)
                    <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Calculating...
                @else
                    Calculate All Scores
                @endif
            </button>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-md">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-md">
            {{ session('error') }}
        </div>
    @endif

    <!-- Scoring Factors Overview -->
    <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Scoring Factors</h2>
            <p class="text-sm text-gray-600">Current factors used in lead scoring calculations.</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($scoringFactors as $factor)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h3 class="font-medium text-gray-900">{{ $factor->display_name }}</h3>
                                <p class="text-sm text-gray-600 mt-1">{{ $factor->description }}</p>
                                <div class="mt-2 flex items-center space-x-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $factor->getCategoryLabel() }}
                                    </span>
                                    <span class="text-xs text-gray-500">Weight: {{ $factor->weight }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Lead Scores Table -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Contact Lead Scores</h2>
            <p class="text-sm text-gray-600">View and manage lead scores for your contacts.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grade</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Updated</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($contacts as $contact)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $contact->first_name }} {{ $contact->last_name }}
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $contact->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $contact->company?->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($contact->leadScore)
                                    <div class="flex items-center">
                                        <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                            <div class="bg-{{ $this->getScoreColor($contact->leadScore->score) }}-600 h-2 rounded-full" 
                                                 style="width: {{ $contact->leadScore->score }}%"></div>
                                        </div>
                                        <span class="text-sm font-medium text-gray-900">{{ $contact->leadScore->score }}</span>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-500">Not calculated</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($contact->leadScore)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                        {{ $contact->leadScore->grade === 'A' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $contact->leadScore->grade === 'B' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $contact->leadScore->grade === 'C' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $contact->leadScore->grade === 'D' ? 'bg-orange-100 text-orange-800' : '' }}
                                        {{ $contact->leadScore->grade === 'F' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ $contact->leadScore->grade }}
                                    </span>
                                @else
                                    <span class="text-sm text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($contact->leadScore)
                                    {{ $contact->leadScore->last_calculated_at->diffForHumans() }}
                                    @if($contact->leadScore->isStale())
                                        <span class="text-orange-600">(Stale)</span>
                                    @endif
                                @else
                                    Never
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                @if($contact->leadScore)
                                    <button wire:click="viewScoreDetails({{ $contact->id }})"
                                            class="text-blue-600 hover:text-blue-900">
                                        View Details
                                    </button>
                                @endif
                                <button wire:click="calculateScore({{ $contact->id }})"
                                        {{ $isCalculating ? 'disabled' : '' }}
                                        class="text-green-600 hover:text-green-900 disabled:opacity-50">
                                    {{ $contact->leadScore ? 'Recalculate' : 'Calculate' }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                No contacts found. Add some contacts to see their lead scores.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Score Details Modal -->
    @if($selectedContact && $leadScore)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">
                            Lead Score Details: {{ $selectedContact->first_name }} {{ $selectedContact->last_name }}
                        </h3>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Overall Score -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <div class="text-center">
                            <div class="text-4xl font-bold text-gray-900 mb-2">{{ $leadScore->score }}</div>
                            <div class="text-lg text-gray-600 mb-2">Overall Lead Score</div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                {{ $leadScore->grade === 'A' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $leadScore->grade === 'B' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $leadScore->grade === 'C' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $leadScore->grade === 'D' ? 'bg-orange-100 text-orange-800' : '' }}
                                {{ $leadScore->grade === 'F' ? 'bg-red-100 text-red-800' : '' }}">
                                Grade {{ $leadScore->grade }}
                            </span>
                            <div class="mt-2 text-sm text-gray-500">
                                Conversion Probability: {{ number_format($leadScore->probability * 100, 1) }}%
                            </div>
                        </div>
                    </div>

                    <!-- Factor Breakdown -->
                    <div class="mb-6">
                        <h4 class="text-md font-medium text-gray-900 mb-3">Scoring Factor Breakdown</h4>
                        <div class="space-y-3">
                            @foreach($leadScore->factors as $factorName => $factorData)
                                <div class="border border-gray-200 rounded-lg p-3">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="font-medium text-gray-900">{{ $factorData['display_name'] }}</span>
                                        <span class="text-sm text-gray-600">
                                            {{ number_format($factorData['weighted_score'], 1) }} points
                                        </span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <div class="flex-1 bg-gray-200 rounded-full h-2">
                                            <div class="bg-blue-600 h-2 rounded-full" 
                                                 style="width: {{ $factorData['raw_score'] }}%"></div>
                                        </div>
                                        <span class="text-sm text-gray-600">{{ $factorData['raw_score'] }}%</span>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        Weight: {{ $factorData['weight'] }} | Raw Score: {{ $factorData['raw_score'] }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Explanations -->
                    @if($leadScore->explanations)
                        <div class="mb-6">
                            <h4 class="text-md font-medium text-gray-900 mb-3">Score Explanations</h4>
                            <ul class="space-y-2">
                                @foreach($leadScore->explanations as $explanation)
                                    <li class="text-sm text-gray-600 flex items-start">
                                        <svg class="w-4 h-4 text-blue-500 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $explanation }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Metadata -->
                    <div class="text-xs text-gray-500 border-t pt-3">
                        <div>Model Version: {{ $leadScore->model_version }}</div>
                        <div>Last Calculated: {{ $leadScore->last_calculated_at->format('M j, Y \a\t g:i A') }}</div>
                        @if($leadScore->aiModel)
                            <div>AI Model: {{ $leadScore->aiModel->getFullName() }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
