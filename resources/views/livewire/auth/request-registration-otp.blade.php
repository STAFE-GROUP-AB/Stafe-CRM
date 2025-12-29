<x-authentication-card>
    <x-slot name="logo">
        <x-authentication-card-logo />
    </x-slot>

    <h1 class="sr-only">{{ __('Register with One-Time Password') }}</h1>

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
        {{ __('Create your account by entering your details below. We will send you a one-time password to verify your email.') }}
    </div>

    <form wire:submit="sendOtp">
        <div>
            <x-label for="name" value="{{ __('Name') }}" />
            <x-input
                id="name"
                class="block mt-1 w-full"
                type="text"
                wire:model="name"
                required
                autofocus
                autocomplete="name"
            />
            <x-input-error for="name" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-label for="email" value="{{ __('Email') }}" />
            @if($invitation)
                <x-input
                    id="email"
                    class="block mt-1 w-full bg-stone-100"
                    type="email"
                    wire:model="email"
                    required
                    autocomplete="email"
                    readonly
                />
                <p class="mt-1 text-xs text-stone-600">{{ __('This email address is locked as per your invitation.') }}</p>
            @else
                <x-input
                    id="email"
                    class="block mt-1 w-full"
                    type="email"
                    wire:model="email"
                    required
                    autocomplete="email"
                />
            @endif
            <x-input-error for="email" class="mt-2" />
        </div>

        @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
            <div class="mt-4">
                <x-label for="terms">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <x-checkbox name="terms" id="terms" wire:model="terms" required />
                        </div>
                        <div class="ms-2 text-sm">
                            {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                    'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-emerald-600 hover:text-emerald-700 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">'.__('Terms of Service').'</a>',
                                    'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-emerald-600 hover:text-emerald-700 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">'.__('Privacy Policy').'</a>',
                            ]) !!}
                        </div>
                    </div>
                </x-label>
                <x-input-error for="terms" class="mt-2" />
            </div>
        @endif

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
                    {{ __('Already have an account?') }}
                    <a
                        href="{{ route('login') }}"
                        class="underline text-emerald-600 hover:text-emerald-700 font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
                        wire:navigate
                    >
                        {{ __('Log in') }}
                    </a>
                </p>
            </div>
        </div>
    </form>
</x-authentication-card>
