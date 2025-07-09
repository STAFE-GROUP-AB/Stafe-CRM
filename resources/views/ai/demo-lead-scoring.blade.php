<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lead Scoring Demo - Stafe CRM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <div class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-900">
                        📊 Lead Scoring Dashboard - Phase 4.1
                    </h1>
                    <div class="flex space-x-4">
                        <a href="/ai/demo/configuration" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm">AI Configuration</a>
                        <a href="/ai/demo/lead-scoring" class="bg-green-600 text-white px-4 py-2 rounded-md text-sm">Lead Scoring</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <h2 class="text-3xl font-bold text-gray-900">AI-Powered Lead Scoring</h2>
                <p class="mt-1 text-sm text-gray-600">
                    Intelligent lead scoring to identify your best prospects using AI and machine learning.
                </p>
            </div>

            <!-- Scoring Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">{{ $contacts->where('leadScore')->count() }}</h3>
                            <p class="text-sm text-gray-600">Scored Contacts</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">
                                {{ $contacts->where('leadScore.score', '>=', 80)->count() }}
                            </h3>
                            <p class="text-sm text-gray-600">High Quality Leads</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-yellow-100 rounded-lg">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">5</h3>
                            <p class="text-sm text-gray-600">Scoring Factors</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">3</h3>
                            <p class="text-sm text-gray-600">AI Providers</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contacts Lead Scores Table -->
            <div class="bg-white shadow rounded-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Contact Lead Scores</h3>
                    <p class="text-sm text-gray-600">AI-powered scores for your contacts (demo data).</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grade</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php
                                // Generate demo scores for display
                                $demoScores = [
                                    ['score' => 92, 'grade' => 'A', 'probability' => 0.85],
                                    ['score' => 78, 'grade' => 'B', 'probability' => 0.72],
                                    ['score' => 65, 'grade' => 'C', 'probability' => 0.58],
                                    ['score' => 43, 'grade' => 'D', 'probability' => 0.35],
                                    ['score' => 88, 'grade' => 'A', 'probability' => 0.82],
                                ];
                            @endphp
                            
                            @forelse($contacts as $index => $contact)
                                @php
                                    $demoScore = $demoScores[$index % count($demoScores)];
                                    $scoreColor = match(true) {
                                        $demoScore['score'] >= 80 => 'green',
                                        $demoScore['score'] >= 60 => 'blue',
                                        $demoScore['score'] >= 40 => 'yellow',
                                        default => 'red'
                                    };
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                                <span class="text-sm font-medium text-white">
                                                    {{ substr($contact->first_name, 0, 1) }}{{ substr($contact->last_name, 0, 1) }}
                                                </span>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $contact->first_name }} {{ $contact->last_name }}
                                                </div>
                                                <div class="text-sm text-gray-500">{{ $contact->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $contact->company?->name ?? 'Individual' }}</div>
                                        <div class="text-sm text-gray-500">{{ $contact->title }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-16 bg-gray-200 rounded-full h-2 mr-3">
                                                <div class="bg-{{ $scoreColor }}-600 h-2 rounded-full" 
                                                     style="width: {{ $demoScore['score'] }}%"></div>
                                            </div>
                                            <span class="text-sm font-medium text-gray-900">{{ $demoScore['score'] }}</span>
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ number_format($demoScore['probability'] * 100, 1) }}% conversion probability
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                            {{ $demoScore['grade'] === 'A' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $demoScore['grade'] === 'B' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $demoScore['grade'] === 'C' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $demoScore['grade'] === 'D' ? 'bg-orange-100 text-orange-800' : '' }}">
                                            Grade {{ $demoScore['grade'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                            Ready for Engagement
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                        No contacts found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Scoring Factors Overview -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">AI Scoring Factors</h3>
                    <p class="text-sm text-gray-600">Factors used in our AI-powered lead scoring algorithm.</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900">📧 Email Engagement</h4>
                            <p class="text-sm text-gray-600 mt-1">Open rates, click-through rates, and response rates</p>
                            <div class="mt-2 flex items-center space-x-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                    Engagement
                                </span>
                                <span class="text-xs text-gray-500">Weight: 25%</span>
                            </div>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900">🏢 Company Size</h4>
                            <p class="text-sm text-gray-600 mt-1">Employee count and company scale indicators</p>
                            <div class="mt-2 flex items-center space-x-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                    Firmographic
                                </span>
                                <span class="text-xs text-gray-500">Weight: 20%</span>
                            </div>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900">🎯 Industry Match</h4>
                            <p class="text-sm text-gray-600 mt-1">Alignment with target industry segments</p>
                            <div class="mt-2 flex items-center space-x-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                    Firmographic
                                </span>
                                <span class="text-xs text-gray-500">Weight: 15%</span>
                            </div>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900">🌐 Website Activity</h4>
                            <p class="text-sm text-gray-600 mt-1">Page views, session duration, and engagement</p>
                            <div class="mt-2 flex items-center space-x-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                    Behavioral
                                </span>
                                <span class="text-xs text-gray-500">Weight: 20%</span>
                            </div>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900">📍 Lead Source Quality</h4>
                            <p class="text-sm text-gray-600 mt-1">Quality indicators based on acquisition channel</p>
                            <div class="mt-2 flex items-center space-x-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800">
                                    Demographic
                                </span>
                                <span class="text-xs text-gray-500">Weight: 20%</span>
                            </div>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-4 bg-gradient-to-br from-blue-50 to-purple-50">
                            <h4 class="font-medium text-gray-900">🤖 AI Enhancement</h4>
                            <p class="text-sm text-gray-600 mt-1">Machine learning model optimization coming in Phase 4.2</p>
                            <div class="mt-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                    Future Enhancement
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>