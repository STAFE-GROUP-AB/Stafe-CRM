<x-guest-layout>
    <div class="pt-4 bg-amber-50">
        <div class="min-h-screen flex flex-col items-center pt-6 sm:pt-0">
            <div>
                <x-authentication-card-logo />
            </div>

            <div class="w-full sm:max-w-2xl mt-6 p-6 bg-white shadow-lg shadow-stone-200/50 overflow-hidden sm:rounded-xl border border-amber-100 prose prose-stone">
                {!! $policy !!}
            </div>
        </div>
    </div>
</x-guest-layout>
