<x-authentication-card>
    <x-slot name="logo">
        <x-authentication-card-logo />
    </x-slot>

    <h1 class="sr-only">{{ __('Verify Registration Code') }}</h1>

    @if (session()->has('message'))
        <div class="mb-4 p-3 rounded-lg bg-emerald-50 border border-emerald-200" role="alert" aria-live="polite">
            <p class="font-medium text-sm text-emerald-600">
                {{ session('message') }}
            </p>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200" role="alert" aria-live="assertive">
            <p class="font-medium text-sm text-red-600">
                {{ session('error') }}
            </p>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200" role="alert" aria-live="assertive">
            <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mb-4 text-sm text-stone-600">
        {{ __('We have sent a 6-digit code to your email address. Please enter it below to complete your registration.') }}
    </div>

    <form wire:submit="verify">
        <div>
            <x-label for="name" value="{{ __('Name') }}" />
            <x-input
                id="name"
                class="block mt-1 w-full bg-stone-50"
                type="text"
                wire:model="name"
                disabled
            />
        </div>

        <div class="mt-4">
            <x-label for="email" value="{{ __('Email') }}" />
            <x-input
                id="email"
                class="block mt-1 w-full bg-stone-50"
                type="email"
                wire:model="email"
                disabled
            />
        </div>

        <div class="mt-4">
            <x-label for="otp" value="{{ __('One-Time Password') }}" />
            <x-input
                id="otp"
                class="block mt-1 w-full text-center text-2xl tracking-widest font-mono"
                type="text"
                wire:model="otp"
                required
                autofocus
                maxlength="6"
                pattern="[0-9]{6}"
                inputmode="numeric"
                autocomplete="one-time-code"
                placeholder="000000"
            />
            <p class="mt-1 text-xs text-stone-500">
                {{ __('Enter the 6-digit code from your email') }}
            </p>
            <x-input-error for="otp" class="mt-2" />
        </div>

        <div class="block mt-4">
            <label for="remember_device" class="flex items-start">
                <x-checkbox
                    id="remember_device"
                    wire:model="remember_device"
                    class="mt-0.5"
                />
                <span class="ms-2 text-sm text-stone-600">
                    {{ __('Remember this device for 30 days') }}
                    <span class="block text-xs text-stone-500 mt-1">
                        {{ __('You won\'t need to enter a code on this device for 30 days') }}
                    </span>
                </span>
            </label>
        </div>

        <div class="flex flex-col gap-3 mt-6">
            <x-button
                class="w-full justify-center"
                wire:loading.attr="disabled"
                wire:target="verify"
            >
                <span wire:loading.class="hidden" wire:target="verify">
                    {{ __('Complete Registration') }}
                </span>
                <span wire:loading.class.remove="hidden" wire:target="verify" class="hidden inline-flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ __('Verifying...') }}
                </span>
            </x-button>

            <x-secondary-button
                type="button"
                wire:click="resend"
                class="w-full justify-center"
            >
                {{ __('Resend Code') }}
            </x-secondary-button>

            <div class="text-center mt-2">
                <a
                    href="{{ route('register') }}"
                    class="text-sm underline text-stone-600 hover:text-stone-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
                    wire:navigate
                >
                    {{ __('Use different details') }}
                </a>
            </div>
        </div>
    </form>
</x-authentication-card>
