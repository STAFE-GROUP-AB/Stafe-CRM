<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">AI Configuration</h1>
        <p class="mt-1 text-sm text-gray-600">
            Configure your AI providers and models for enhanced CRM intelligence features.
        </p>
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

    <!-- Available Providers Section -->
    <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Available AI Providers</h2>
            <p class="text-sm text-gray-600">Choose from supported AI providers to power your CRM intelligence.</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($providers as $provider)
                    <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-colors cursor-pointer {{ $selectedProvider == $provider->id ? 'border-blue-500 bg-blue-50' : '' }}"
                         wire:click="selectProvider({{ $provider->id }})">
                        <div class="flex items-center space-x-3">
                            @if($provider->logo_url)
                                <img src="{{ $provider->logo_url }}" alt="{{ $provider->name }}" class="w-8 h-8">
                            @else
                                <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                    <span class="text-xs font-medium text-gray-600">{{ substr($provider->name, 0, 2) }}</span>
                                </div>
                            @endif
                            <div>
                                <h3 class="font-medium text-gray-900">{{ $provider->name }}</h3>
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
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- User Configurations Section -->
    <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h2 class="text-lg font-medium text-gray-900">Your AI Configurations</h2>
                <p class="text-sm text-gray-600">Manage your AI provider configurations and API keys.</p>
            </div>
            <button wire:click="showAddConfigurationForm" 
                    class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700 transition-colors">
                Add Configuration
            </button>
        </div>
        <div class="p-6">
            @if(count($userConfigurations) > 0)
                <div class="space-y-4">
                    @foreach($userConfigurations as $config)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3">
                                        <h3 class="font-medium text-gray-900">{{ $config->name }}</h3>
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
                                    @if($config->last_used_at)
                                        <p class="text-xs text-gray-500 mt-1">Last used: {{ $config->last_used_at->diffForHumans() }}</p>
                                    @endif
                                </div>
                                <div class="flex space-x-2">
                                    <button wire:click="testConfiguration({{ $config->id }})"
                                            class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        Test
                                    </button>
                                    @if(!$config->is_default)
                                        <button wire:click="setDefaultConfiguration({{ $config->id }})"
                                                class="text-green-600 hover:text-green-800 text-sm font-medium">
                                            Set Default
                                        </button>
                                    @endif
                                    <button wire:click="editConfiguration({{ $config->id }})"
                                            class="text-gray-600 hover:text-gray-800 text-sm font-medium">
                                        Edit
                                    </button>
                                    <button wire:click="deleteConfiguration({{ $config->id }})"
                                            class="text-red-600 hover:text-red-800 text-sm font-medium"
                                            wire:confirm="Are you sure you want to delete this configuration?">
                                        Delete
                                    </button>
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
                    <p class="mt-1 text-sm text-gray-500">Get started by adding your first AI provider configuration.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Add/Edit Configuration Modal -->
    @if($showAddForm)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ $editingConfig ? 'Edit' : 'Add' }} AI Configuration
                        </h3>
                        <button wire:click="cancelForm" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="saveConfiguration" class="space-y-4">
                        <!-- Provider Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">AI Provider</label>
                            <select wire:model.live="selectedProvider" class="w-full border border-gray-300 rounded-md px-3 py-2">
                                <option value="">Select a provider</option>
                                @foreach($providers as $provider)
                                    <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                                @endforeach
                            </select>
                            @error('selectedProvider') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Configuration Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Configuration Name</label>
                            <input type="text" wire:model="configurationName" 
                                   class="w-full border border-gray-300 rounded-md px-3 py-2"
                                   placeholder="e.g., My OpenAI Config">
                            @error('configurationName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- API Key -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">API Key</label>
                            <input type="password" wire:model="apiKey" 
                                   class="w-full border border-gray-300 rounded-md px-3 py-2"
                                   placeholder="Enter your API key">
                            @error('apiKey') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Organization (for OpenAI) -->
                        @if($selectedProvider && $providers->find($selectedProvider)?->slug === 'openai')
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Organization ID (Optional)</label>
                                <input type="text" wire:model="organization" 
                                       class="w-full border border-gray-300 rounded-md px-3 py-2"
                                       placeholder="org-xxxxxx">
                            </div>
                        @endif

                        <!-- Default Models -->
                        @if(count($availableModels) > 0)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Default Models</label>
                                <div class="space-y-2">
                                    <div>
                                        <label class="block text-xs text-gray-600">Lead Scoring Model</label>
                                        <select wire:model="defaultModels.lead_scoring" class="w-full border border-gray-300 rounded-md px-3 py-2">
                                            <option value="">Select model</option>
                                            @foreach($availableModels as $model)
                                                <option value="{{ $model->model_id }}">{{ $model->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600">General Text Generation</label>
                                        <select wire:model="defaultModels.general" class="w-full border border-gray-300 rounded-md px-3 py-2">
                                            <option value="">Select model</option>
                                            @foreach($availableModels as $model)
                                                <option value="{{ $model->model_id }}">{{ $model->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Default Configuration -->
                        <div class="flex items-center">
                            <input type="checkbox" wire:model="isDefault" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <label class="ml-2 text-sm text-gray-700">Set as default configuration</label>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-3 pt-4">
                            <button type="button" wire:click="cancelForm"
                                    class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700">
                                {{ $editingConfig ? 'Update' : 'Save' }} Configuration
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
