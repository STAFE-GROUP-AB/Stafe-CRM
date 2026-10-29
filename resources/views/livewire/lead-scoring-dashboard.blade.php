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
            @if($scoringFactors->isEmpty())
                <div class="text-center py-4">
                    <p class="text-gray-500">No scoring factors configured. Using default scoring algorithm.</p>
                    <p class="text-sm text-gray-400 mt-1">Run the ScoringFactorsSeeder to add default factors.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($scoringFactors as $factor)
                        <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-colors">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h3 class="font-medium text-gray-900">{{ $factor->display_name }}</h3>
                                    <p class="text-sm text-gray-600 mt-1">{{ $factor->description }}</p>
                                    <div class="mt-2 flex items-center space-x-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                            {{ $factor->category === 'engagement' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $factor->category === 'firmographic' ? 'bg-purple-100 text-purple-800' : '' }}
                                            {{ $factor->category === 'behavioral' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $factor->category === 'demographic' ? 'bg-orange-100 text-orange-800' : '' }}">
                                            {{ $factor->getCategoryLabel() }}
                                        </span>
                                        <span class="text-xs text-gray-500">Weight: {{ number_format($factor->weight, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
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
                                    <div class="flex items-center group relative">
                                        <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                            <div class="{{ $this->getScoreColorClass($contact->leadScore->score) }} h-2 rounded-full transition-all"
                                                 style="width: {{ $contact->leadScore->score }}%"></div>
                                        </div>
                                        <span class="text-sm font-medium text-gray-900">{{ $contact->leadScore->score }}</span>

                                        <!-- Score Tooltip -->
                                        <div class="absolute bottom-full left-0 mb-2 hidden group-hover:block z-10">
                                            <div class="bg-gray-900 text-white text-xs rounded py-2 px-3 min-w-48">
                                                <div class="font-semibold mb-1">Score Breakdown</div>
                                                @if($contact->leadScore->factors)
                                                    @foreach($contact->leadScore->factors as $name => $data)
                                                        <div class="flex justify-between py-0.5">
                                                            <span>{{ $data['display_name'] ?? ucfirst($name) }}</span>
                                                            <span>{{ number_format($data['raw_score'] ?? 0, 0) }}</span>
                                                        </div>
                                                    @endforeach
                                                @endif
                                                <div class="border-t border-gray-700 mt-1 pt-1 flex justify-between font-semibold">
                                                    <span>Total</span>
                                                    <span>{{ $contact->leadScore->score }}</span>
                                                </div>
                                            </div>
                                            <div class="w-3 h-3 bg-gray-900 rotate-45 absolute -bottom-1 left-4"></div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-500">Not calculated</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($contact->leadScore)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
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
                                        <span class="text-orange-600 text-xs ml-1">(Stale)</span>
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
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click.self="closeModal">
            <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 xl:w-1/2 shadow-lg rounded-lg bg-white max-h-[90vh] overflow-y-auto">
                <div class="mt-3">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-semibold text-gray-900">
                            Lead Score Details: {{ $selectedContact->first_name }} {{ $selectedContact->last_name }}
                        </h3>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Overall Score -->
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg p-6 mb-6">
                        <div class="flex items-center justify-between">
                            <div class="text-center flex-1">
                                <div class="text-5xl font-bold {{ $leadScore->score >= 80 ? 'text-green-600' : ($leadScore->score >= 60 ? 'text-yellow-600' : ($leadScore->score >= 40 ? 'text-orange-600' : 'text-red-600')) }}">
                                    {{ $leadScore->score }}
                                </div>
                                <div class="text-gray-600 mt-1">Overall Lead Score</div>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold mt-2
                                    {{ $leadScore->grade === 'A' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $leadScore->grade === 'B' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $leadScore->grade === 'C' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $leadScore->grade === 'D' ? 'bg-orange-100 text-orange-800' : '' }}
                                    {{ $leadScore->grade === 'F' ? 'bg-red-100 text-red-800' : '' }}">
                                    Grade {{ $leadScore->grade }}
                                </span>
                            </div>
                            <div class="text-center flex-1 border-l border-gray-300 pl-6">
                                <div class="text-3xl font-bold text-gray-700">
                                    {{ number_format($leadScore->probability * 100, 1) }}%
                                </div>
                                <div class="text-gray-600 mt-1">Conversion Probability</div>
                            </div>
                        </div>
                        <div class="mt-4 flex justify-center">
                            <button wire:click="openAdjustmentModal"
                                    class="text-sm text-blue-600 hover:text-blue-800 underline">
                                Manually Adjust Score
                            </button>
                        </div>
                    </div>

                    <!-- Category Scores -->
                    @if(!empty($categoryScores))
                        <div class="mb-6">
                            <h4 class="text-lg font-medium text-gray-900 mb-3">Category Breakdown</h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @foreach($categoryScores as $category => $data)
                                    @php
                                        $categoryScore = $data['weight'] > 0 ? round(($data['total'] / $data['weight']) * 100) : 0;
                                    @endphp
                                    <div class="bg-white border rounded-lg p-4 text-center">
                                        <div class="text-2xl font-bold {{ $categoryScore >= 70 ? 'text-green-600' : ($categoryScore >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                            {{ $categoryScore }}
                                        </div>
                                        <div class="text-sm text-gray-600">{{ $this->getCategoryLabel($category) }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Factor Breakdown -->
                    <div class="mb-6">
                        <h4 class="text-lg font-medium text-gray-900 mb-3">Scoring Factor Breakdown</h4>
                        <div class="space-y-3">
                            @foreach($leadScore->factors as $factorName => $factorData)
                                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex justify-between items-center mb-2">
                                        <div class="flex items-center space-x-2">
                                            <span class="font-medium text-gray-900">{{ $factorData['display_name'] }}</span>
                                            @if(isset($factorData['category']))
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                    {{ $factorData['category'] === 'engagement' ? 'bg-blue-100 text-blue-800' : '' }}
                                                    {{ $factorData['category'] === 'firmographic' ? 'bg-purple-100 text-purple-800' : '' }}
                                                    {{ $factorData['category'] === 'behavioral' ? 'bg-green-100 text-green-800' : '' }}
                                                    {{ $factorData['category'] === 'demographic' ? 'bg-orange-100 text-orange-800' : '' }}">
                                                    {{ ucfirst($factorData['category']) }}
                                                </span>
                                            @endif
                                        </div>
                                        <span class="text-sm font-semibold {{ $factorData['raw_score'] >= 70 ? 'text-green-600' : ($factorData['raw_score'] >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                            {{ number_format($factorData['weighted_score'], 1) }} points
                                        </span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <div class="flex-1 bg-gray-200 rounded-full h-2">
                                            <div class="{{ $factorData['raw_score'] >= 70 ? 'bg-green-500' : ($factorData['raw_score'] >= 50 ? 'bg-yellow-500' : 'bg-red-500') }} h-2 rounded-full transition-all"
                                                 style="width: {{ $factorData['raw_score'] }}%"></div>
                                        </div>
                                        <span class="text-sm text-gray-600 w-12 text-right">{{ number_format($factorData['raw_score'], 0) }}%</span>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        Weight: {{ $factorData['weight'] }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Recommendations -->
                    @if(!empty($recommendations))
                        <div class="mb-6">
                            <h4 class="text-lg font-medium text-gray-900 mb-3">Recommendations to Improve Score</h4>
                            <div class="space-y-3">
                                @foreach($recommendations as $rec)
                                    <div class="border-l-4 p-4 rounded-r-lg
                                        {{ $rec['priority'] === 'high' ? 'border-red-400 bg-red-50' : '' }}
                                        {{ $rec['priority'] === 'medium' ? 'border-yellow-400 bg-yellow-50' : '' }}
                                        {{ $rec['priority'] === 'low' ? 'border-green-400 bg-green-50' : '' }}">
                                        <div class="flex items-start justify-between">
                                            <div>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold uppercase
                                                    {{ $rec['priority'] === 'high' ? 'bg-red-100 text-red-800' : '' }}
                                                    {{ $rec['priority'] === 'medium' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                    {{ $rec['priority'] === 'low' ? 'bg-green-100 text-green-800' : '' }}">
                                                    {{ $rec['priority'] }} priority
                                                </span>
                                                <p class="text-sm text-gray-700 mt-1">{{ $rec['action'] }}</p>
                                            </div>
                                            <span class="text-xs text-gray-500 whitespace-nowrap ml-4">{{ $rec['impact'] }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Score History -->
                    @if(!empty($scoreHistory))
                        <div class="mb-6">
                            <h4 class="text-lg font-medium text-gray-900 mb-3">Score History</h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Previous</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">New</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Change</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Reason</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($scoreHistory as $history)
                                            @php
                                                $change = $history['new_score'] - ($history['previous_score'] ?? 0);
                                            @endphp
                                            <tr>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">
                                                    {{ \Carbon\Carbon::parse($history['created_at'])->format('M j, Y g:i A') }}
                                                </td>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $history['previous_score'] ?? '-' }}
                                                </td>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $history['new_score'] }}
                                                </td>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm font-medium {{ $change > 0 ? 'text-green-600' : ($change < 0 ? 'text-red-600' : 'text-gray-600') }}">
                                                    {{ $change > 0 ? '+' : '' }}{{ $change }}
                                                </td>
                                                <td class="px-4 py-2 text-sm text-gray-500">
                                                    {{ $history['change_reason'] }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <!-- Explanations -->
                    @if($leadScore->explanations)
                        <div class="mb-6">
                            <h4 class="text-lg font-medium text-gray-900 mb-3">Score Explanations</h4>
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
                        <div class="grid grid-cols-2 gap-2">
                            <div>Model Version: {{ $leadScore->model_version }}</div>
                            <div>Last Calculated: {{ $leadScore->last_calculated_at->format('M j, Y \a\t g:i A') }}</div>
                            @if($leadScore->aiModel)
                                <div>AI Model: {{ $leadScore->aiModel->getFullName() }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Manual Adjustment Modal -->
    @if($showAdjustmentModal && $leadScore)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-[60]" wire:click.self="closeModal">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Manually Adjust Score</h3>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Current Score</label>
                    <div class="text-2xl font-bold text-gray-900">{{ $leadScore->score }}</div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Adjustment Amount</label>
                    <input type="number" wire:model="adjustmentAmount"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           min="-100" max="100" placeholder="-10 to +10">
                    <p class="text-xs text-gray-500 mt-1">
                        New score will be: {{ min(100, max(0, $leadScore->score + $adjustmentAmount)) }}
                    </p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reason for Adjustment</label>
                    <textarea wire:model="adjustmentReason"
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                              rows="3" placeholder="Why are you adjusting this score?"></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                    <button wire:click="$set('showAdjustmentModal', false)"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                        Cancel
                    </button>
                    <button wire:click="submitAdjustment"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                        Apply Adjustment
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
