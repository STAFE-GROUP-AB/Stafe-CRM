<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Quote Header -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Sign Quote {{ $quote->quote_number }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ $quote->title }}
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ $quote->company->name }}
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Total: ${{ number_format($quote->total_amount, 2) }}
                    </p>
                </div>
            </div>

            @if($quote->description)
                <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-md">
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                        {{ $quote->description }}
                    </p>
                </div>
            @endif
        </div>

        <!-- Signature Form -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <form wire:submit.prevent="saveSignature">
                <div class="space-y-6">
                    <!-- Signer Information -->
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                            Signer Information
                        </h3>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label for="signerName" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Full Name <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="signerName"
                                    wire:model="signerName"
                                    class="mt-1 block w-full bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500"
                                    required
                                >
                                @error('signerName')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="signerEmail" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Email Address <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="email"
                                    id="signerEmail"
                                    wire:model="signerEmail"
                                    class="mt-1 block w-full bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500"
                                    required
                                >
                                @error('signerEmail')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-2">
                                <label for="signerTitle" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Title/Position
                                </label>
                                <input
                                    type="text"
                                    id="signerTitle"
                                    wire:model="signerTitle"
                                    class="mt-1 block w-full bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500"
                                    placeholder="e.g., CEO, Director, Manager"
                                >
                                @error('signerTitle')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Signature Pad -->
                    <div>
                        <x-signature-pad
                            wire="signatureData"
                            name="quote_signature"
                            label="Your Signature"
                            :required="true"
                            :width="600"
                            :height="200"
                            :show-instructions="true"
                        />
                        @error('signatureData')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Terms Agreement -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-md p-4">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input
                                    id="terms"
                                    type="checkbox"
                                    required
                                    class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300 dark:border-gray-600 rounded"
                                >
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="terms" class="font-medium text-gray-700 dark:text-gray-300">
                                    I agree to the terms and conditions
                                </label>
                                <p class="text-gray-500 dark:text-gray-400 mt-1">
                                    By signing this quote, you acknowledge that you have read and agree to the terms and conditions outlined in this document.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Legal Notice -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400 dark:text-blue-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800 dark:text-blue-300">
                                    Legal Notice
                                </h3>
                                <p class="mt-1 text-sm text-blue-700 dark:text-blue-400">
                                    Your electronic signature has the same legal validity as a handwritten signature. Your signature, IP address, and timestamp will be recorded for verification purposes.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-between pt-4">
                        <a
                            href="{{ route('quotes.index') }}"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 dark:focus:ring-offset-gray-900"
                        >
                            Cancel
                        </a>

                        <button
                            type="submit"
                            class="inline-flex items-center px-6 py-3 border border-transparent shadow-sm text-base font-medium rounded-md text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 dark:focus:ring-offset-gray-900 disabled:opacity-50 disabled:cursor-not-allowed"
                            wire:loading.attr="disabled"
                        >
                            <svg wire:loading class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span wire:loading.remove>Sign and Submit Quote</span>
                            <span wire:loading>Submitting...</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Signature Information -->
        <div class="mt-6 bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                        Secure Signature Process
                    </h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Your signature is encrypted and stored securely. We record your IP address ({{ request()->ip() }}) and timestamp for audit purposes.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
