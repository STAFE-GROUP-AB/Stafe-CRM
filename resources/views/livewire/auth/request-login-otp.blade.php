<x-authentication-card>
    <x-slot name="logo">
        <x-authentication-card-logo />
    </x-slot>

    <h1 class="sr-only">{{ __('Login with One-Time Password') }}</h1>

    @if (session()->has('message'))
        <div class="mb-4 p-3 rounded-lg bg-emerald-50 border border-emerald-200" role="alert" aria-live="polite">
            <p class="font-medium text-sm text-emerald-600">
                {{ session('message') }}
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
        {{ __('Enter your email address and we will send you a one-time password to log in.') }}
    </div>

    <form wire:submit="sendOtp">
        <div>
            <x-label for="email" value="{{ __('Email') }}" />
            <x-input
                id="email"
                class="block mt-1 w-full"
                type="email"
                wire:model="email"
                required
                autofocus
                autocomplete="email"
                aria-required="true"
            />
            <x-input-error for="email" class="mt-2" />
        </div>

        <div class="flex flex-col gap-4 mt-6">
            <x-button
                class="w-full justify-center"
                wire:loading.attr="disabled"
                wire:target="sendOtp"
            >
                <span wire:loading.class="hidden" wire:target="sendOtp">
                    {{ __('Send Code') }}
                </span>
                <span wire:loading.class.remove="hidden" wire:target="sendOtp" class="hidden inline-flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ __('Sending...') }}
                </span>
            </x-button>

            <div class="text-center">
                <p class="text-sm text-stone-600">
                    {{ __("Don't have an account?") }}
                    <a
                        href="{{ route('register') }}"
                        class="underline text-emerald-600 hover:text-emerald-700 font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
                        wire:navigate
                    >
                        {{ __('Register') }}
                    </a>
                </p>
            </div>
        </div>
    </form>
</x-authentication-card>
