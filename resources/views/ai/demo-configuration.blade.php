<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AI Configuration Demo - Stafe CRM</title>
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
                        🤖 AI Configuration Demo - Phase 4.1
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
                <h2 class="text-3xl font-bold text-gray-900">AI Provider Configuration</h2>
                <p class="mt-1 text-sm text-gray-600">
                    Configure your AI providers and models for enhanced CRM intelligence features.
                </p>
            </div>

            <!-- Available Providers Section -->
            <div class="bg-white shadow rounded-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Available AI Providers</h3>
                    <p class="text-sm text-gray-600">Choose from supported AI providers to power your CRM intelligence.</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($providers as $provider)
                            <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-colors">
                                <div class="flex items-center space-x-3">
                                    @if($provider->logo_url)
                                        <img src="{{ $provider->logo_url }}" alt="{{ $provider->name }}" class="w-8 h-8">
                                    @else
                                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                            <span class="text-xs font-medium text-white">{{ substr($provider->name, 0, 2) }}</span>
                                        </div>
                                    @endif
                                    <div>
                                        <h4 class="font-medium text-gray-900">{{ $provider->name }}</h4>
                                        <p class="text-xs text-gray-500">{{ count($provider->supported_features) }} features</p>
                                    </div>
                                </div>
                                <p class="mt-2 text-sm text-gray-600">{{ $provider->description }}</p>
                                <div class="mt-2 flex flex-wrap gap-1">
                                    @foreach($provider->supported_features as $feature)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ str_replace('_', ' ', ucfirst($feature)) }}
                                        </span>
                                    @endforeach
                                </div>
                                <div class="mt-3 flex items-center justify-between">
                                    <span class="text-xs text-gray-500">
                                        {{ $provider->aiModels->count() }} models available
                                    </span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                        {{ $provider->status }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- User Configurations Section -->
            <div class="bg-white shadow rounded-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Demo User AI Configurations</h3>
                    <p class="text-sm text-gray-600">Example configurations for the demo user.</p>
                </div>
                <div class="p-6">
                    @if(count($userConfigurations) > 0)
                        <div class="space-y-4">
                            @foreach($userConfigurations as $config)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-3">
                                                <h4 class="font-medium text-gray-900">{{ $config->name }}</h4>
                                                @if($config->is_default)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                        Default
                                                    </span>
                                                @endif
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $config->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                    {{ $config->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-600 mt-1">{{ $config->aiProvider->name }}</p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                Default Models: 
                                                @foreach($config->default_models as $useCase => $model)
                                                    <span class="inline-block bg-gray-100 rounded px-2 py-1 text-xs mr-1">{{ ucfirst($useCase) }}: {{ $model }}</span>
                                                @endforeach
                                            </p>
                                        </div>
                                        <div class="flex space-x-2">
                                            <span class="text-blue-600 text-sm font-medium">Ready for API calls</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No configurations</h3>
                            <p class="mt-1 text-sm text-gray-500">No AI configurations found for the demo user.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Key Features Overview -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Phase 4.1 Key Features</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="border-l-4 border-blue-500 pl-4">
                            <h4 class="font-medium text-gray-900">🔐 Secure Credential Management</h4>
                            <p class="text-sm text-gray-600">Encrypted storage of API keys with per-user configurations</p>
                        </div>
                        <div class="border-l-4 border-green-500 pl-4">
                            <h4 class="font-medium text-gray-900">🤖 Multi-Provider Support</h4>
                            <p class="text-sm text-gray-600">OpenAI, Anthropic, and Google AI integration</p>
                        </div>
                        <div class="border-l-4 border-purple-500 pl-4">
                            <h4 class="font-medium text-gray-900">📊 Intelligent Lead Scoring</h4>
                            <p class="text-sm text-gray-600">Rule-based and ML-ready scoring system</p>
                        </div>
                        <div class="border-l-4 border-orange-500 pl-4">
                            <h4 class="font-medium text-gray-900">⚙️ Flexible Configuration</h4>
                            <p class="text-sm text-gray-600">Per-use-case model selection and preferences</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>