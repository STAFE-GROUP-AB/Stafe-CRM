<div class="max-w-4xl mx-auto">
    <div class="space-y-8">
        <!-- Page Header -->
        <div class="border-b border-gray-200 pb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">System Settings</h1>
                    <p class="mt-1 text-sm text-gray-600">Customize your CRM appearance and branding</p>
                </div>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('message') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <form wire:submit.prevent="save" class="space-y-8">
            <!-- Branding Section -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Branding</h2>
                
                <div class="space-y-6">
                    <!-- App Name -->
                    <div>
                        <label for="app_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Application Name
                        </label>
                        <input 
                            type="text" 
                            id="app_name" 
                            wire:model="app_name" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Enter your CRM name"
                        >
                        @error('app_name') 
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                        @enderror
                    </div>

                    <!-- Logo Upload -->
                    <div>
                        <label for="logo_upload" class="block text-sm font-medium text-gray-700 mb-2">
                            Logo
                        </label>
                        <div class="flex items-center space-x-4">
                            @if($app_logo)
                                <div class="flex-shrink-0">
                                    <img src="{{ $app_logo }}" alt="Current logo" class="h-12 w-12 rounded-lg object-cover">
                                </div>
                            @else
                                <div class="flex-shrink-0">
                                    <div class="h-12 w-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                </div>
                            @endif
                            <div class="flex-1">
                                <input 
                                    type="file" 
                                    id="logo_upload" 
                                    wire:model="logo_upload"
                                    accept="image/*"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                >
                                <p class="mt-1 text-sm text-gray-500">PNG, JPG, GIF up to 2MB</p>
                            </div>
                        </div>
                        @error('logo_upload') 
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Theme Colors -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Theme Colors</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Primary Color -->
                    <div>
                        <label for="primary_color" class="block text-sm font-medium text-gray-700 mb-2">
                            Primary Color
                        </label>
                        <div class="flex items-center space-x-3">
                            <input 
                                type="color" 
                                id="primary_color" 
                                wire:model="primary_color" 
                                class="w-12 h-10 border border-gray-300 rounded-lg cursor-pointer"
                            >
                            <input 
                                type="text" 
                                wire:model="primary_color" 
                                class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="#3B82F6"
                            >
                        </div>
                        @error('primary_color') 
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                        @enderror
                    </div>

                    <!-- Secondary Color -->
                    <div>
                        <label for="secondary_color" class="block text-sm font-medium text-gray-700 mb-2">
                            Secondary Color
                        </label>
                        <div class="flex items-center space-x-3">
                            <input 
                                type="color" 
                                id="secondary_color" 
                                wire:model="secondary_color" 
                                class="w-12 h-10 border border-gray-300 rounded-lg cursor-pointer"
                            >
                            <input 
                                type="text" 
                                wire:model="secondary_color" 
                                class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="#6B7280"
                            >
                        </div>
                        @error('secondary_color') 
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Preview Section -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Preview</h2>
                
                <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            @if($app_logo)
                                <img src="{{ $app_logo }}" alt="Logo" class="h-8 w-8 rounded">
                            @endif
                            <span class="text-xl font-bold text-gray-900">{{ $app_name }}</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button 
                                type="button" 
                                class="px-3 py-1 rounded-lg text-sm font-medium text-white transition-colors"
                                style="background-color: {{ $primary_color }}"
                            >
                                Primary Button
                            </button>
                            <button 
                                type="button" 
                                class="px-3 py-1 rounded-lg text-sm font-medium text-white transition-colors"
                                style="background-color: {{ $secondary_color }}"
                            >
                                Secondary Button
                            </button>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600">This is how your CRM will look with the current settings.</p>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-3">
                <button 
                    type="button" 
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    Cancel
                </button>
                <button 
                    type="submit" 
                    class="px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</div>