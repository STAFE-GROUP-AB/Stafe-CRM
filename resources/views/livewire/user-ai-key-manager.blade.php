<div class="max-w-4xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-sm">
        <div class="border-b border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">AI Key Management</h1>
                    <p class="text-gray-600 mt-1">Manage your AI service API keys and configurations</p>
                </div>
                <button wire:click="showAddForm" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Add AI Configuration
                </button>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="m-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex">
                    <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-green-700">{{ session('message') }}</p>
                </div>
            </div>
        @endif

        <!-- Add/Edit Form -->
        @if ($showForm)
            <div class="p-6 border-b border-gray-200">
                <form wire:submit="save">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                AI Provider
                            </label>
                            <select wire:model="selectedProvider" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select a provider</option>
                                @foreach($providers as $provider)
                                    <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                                @endforeach
                            </select>
                            @error('selectedProvider') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                API Key
                            </label>
                            <input type="password" wire:model="apiKey"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Enter your API key">
                            @error('apiKey') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                API Endpoint (Optional)
                            </label>
                            <input type="url" wire:model="apiEndpoint"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="https://api.example.com">
                            @error('apiEndpoint') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" wire:model="isDefault" id="isDefault"
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                            <label for="isDefault" class="ml-2 text-sm text-gray-700">
                                Set as default configuration
                            </label>
                        </div>
                    </div>

                    <div class="mt-6 flex space-x-3">
                        <button type="submit"
                                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            {{ $editingId ? 'Update' : 'Save' }} Configuration
                        </button>
                        <button type="button" wire:click="$set('showForm', false)"
                                class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition-colors">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <!-- Configurations List -->
        <div class="p-6">
            @if(empty($configurations))
                <div class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No AI configurations found</h3>
                    <p class="text-gray-600 mb-4">Add your first AI service configuration to start using AI features.</p>
                    <button wire:click="showAddForm" 
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        Add Configuration
                    </button>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($configurations as $config)
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-sm transition-shadow">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-medium text-gray-900">
                                            {{ $config['ai_provider']['name'] ?? 'Unknown Provider' }}
                                            @if($config['is_default'])
                                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                    Default
                                                </span>
                                            @endif
                                        </h3>
                                        <p class="text-sm text-gray-600">
                                            API Key: ••••••••••••{{ substr($config['api_key'], -4) }}
                                        </p>
                                        @if($config['api_endpoint'])
                                            <p class="text-sm text-gray-600">
                                                Endpoint: {{ $config['api_endpoint'] }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-2">
                                    @if(!$config['is_default'])
                                        <button wire:click="setDefault({{ $config['id'] }})"
                                                class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            Set Default
                                        </button>
                                    @endif
                                    
                                    <button wire:click="testConnection({{ $config['id'] }})"
                                            class="text-green-600 hover:text-green-800 text-sm font-medium">
                                        Test
                                    </button>
                                    
                                    <button wire:click="edit({{ $config['id'] }})"
                                            class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        Edit
                                    </button>
                                    
                                    <button wire:click="delete({{ $config['id'] }})"
                                            wire:confirm="Are you sure you want to delete this configuration?"
                                            class="text-red-600 hover:text-red-800 text-sm font-medium">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Info Panel -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-lg font-medium text-blue-900 mb-2">About AI Key Management</h3>
        <div class="text-blue-700 space-y-2">
            <p>• <strong>Secure Storage:</strong> All API keys are encrypted and stored securely.</p>
            <p>• <strong>Multiple Providers:</strong> Configure different AI services for different features.</p>
            <p>• <strong>Cost Control:</strong> You maintain full control over your AI service costs.</p>
            <p>• <strong>Default Configuration:</strong> Set a default configuration for seamless AI features.</p>
        </div>
    </div>
</div>
