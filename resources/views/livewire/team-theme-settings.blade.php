<div>
    <x-form-section submit="updateTheme">
        <x-slot name="title">
            {{ __('Team Theme') }}
        </x-slot>

        <x-slot name="description">
            {{ __('Customize your team\'s color theme. This will change the primary accent color throughout the application for all team members.') }}
        </x-slot>

        <x-slot name="form">
            <div class="col-span-6">
                <x-label for="theme" value="{{ __('Color Theme') }}" />

                <div class="mt-4 grid grid-cols-4 gap-4">
                    @foreach($presets as $key => $preset)
                        <label class="relative cursor-pointer">
                            <input type="radio"
                                   wire:model="selectedPreset"
                                   value="{{ $key }}"
                                   class="sr-only peer">
                            <div class="p-4 rounded-lg border-2 transition-all duration-200
                                        peer-checked:border-{{ $preset['primary'] }}-500
                                        peer-checked:ring-2
                                        peer-checked:ring-{{ $preset['primary'] }}-500
                                        peer-checked:ring-offset-2
                                        dark:peer-checked:ring-offset-gray-800
                                        border-gray-200 dark:border-gray-700
                                        hover:border-gray-300 dark:hover:border-gray-600
                                        bg-white dark:bg-gray-800">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-full bg-{{ $preset['primary'] }}-500"></div>
                                    <div class="w-4 h-4 rounded-full bg-{{ $preset['accent'] }}-500"></div>
                                </div>
                                <p class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $preset['name'] }}
                                </p>
                            </div>
                        </label>
                    @endforeach
                </div>

                @if (session()->has('status'))
                    <div class="mt-4 text-sm text-green-600 dark:text-green-400">
                        {{ session('status') }}
                    </div>
                @endif
            </div>
        </x-slot>

        <x-slot name="actions">
            <x-action-message class="me-3" on="theme-updated">
                {{ __('Saved.') }}
            </x-action-message>

            <x-button>
                {{ __('Save Theme') }}
            </x-button>
        </x-slot>
    </x-form-section>
</div>
