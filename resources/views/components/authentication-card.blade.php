<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-amber-50">
    <div>
        {{ $logo }}
    </div>

    <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-lg shadow-stone-200/50 overflow-hidden sm:rounded-xl border border-amber-100">
        {{ $slot }}
    </div>
</div>
