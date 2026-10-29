<div>
    {{-- Example 1: Basic Usage with Livewire --}}
    <div class="max-w-4xl mx-auto py-8 px-4">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
            E-Signature Pad Examples
        </h2>

        {{-- Example 1: Customer Quote Signature --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Example 1: Customer Quote Signature
            </h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                Capture customer signature for quote approval with full dark mode support
            </p>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Customer Name
                    </label>
                    <input
                        type="text"
                        wire:model="customerName"
                        class="block w-full bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500"
                        placeholder="Enter customer name"
                    >
                </div>

                <x-signature-pad
                    wire="customerSignature"
                    name="customer_signature"
                    label="Customer Signature"
                    :required="true"
                    :width="600"
                    :height="200"
                    :show-instructions="true"
                />

                <button
                    wire:click="saveCustomerSignature"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 dark:focus:ring-offset-gray-900"
                >
                    Save Signature
                </button>
            </div>
        </div>

        {{-- Example 2: Internal Approval Signature --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Example 2: Internal Approval Signature
            </h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                Compact signature pad for internal approvals
            </p>

            <x-signature-pad
                wire="approvalSignature"
                name="approval_signature"
                label="Approver Signature"
                :required="true"
                :width="400"
                :height="150"
            />
        </div>

        {{-- Example 3: Multiple Signatures on Same Form --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Example 3: Multiple Signatures
            </h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                Collect multiple signatures on the same document
            </p>

            <div class="space-y-6">
                <div>
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                        Client Signature
                    </h4>
                    <x-signature-pad
                        wire="clientSignature"
                        name="client_signature"
                        label="Client"
                        :required="true"
                        :width="500"
                        :height="150"
                    />
                </div>

                <div>
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                        Witness Signature
                    </h4>
                    <x-signature-pad
                        wire="witnessSignature"
                        name="witness_signature"
                        label="Witness"
                        :required="false"
                        :width="500"
                        :height="150"
                    />
                </div>
            </div>
        </div>
    </div>
</div>
