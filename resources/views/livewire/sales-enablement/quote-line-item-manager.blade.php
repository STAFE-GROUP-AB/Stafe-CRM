<div class="space-y-6">
    <!-- Line Items Table -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Line Items
                </h3>
                <div class="flex items-center gap-4">
                    <button
                        wire:click="addLineItem"
                        type="button"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 dark:focus:ring-offset-gray-800 transition-colors"
                    >
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Line Item
                    </button>
                    @if(count($lineItems) > 0)
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ count($lineItems) }} {{ Str::plural('item', count($lineItems)) }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        @if(count($lineItems) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-8">
                                Order
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Product/Service
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-24">
                                SKU
                            </th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-24">
                                Qty
                            </th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-32">
                                Unit Price
                            </th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-32">
                                Discount
                            </th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-32">
                                Line Total
                            </th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-32">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($lineItems as $index => $item)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors" wire:key="line-item-{{ $item['id'] ?? $index }}">
                                <!-- Order Controls -->
                                <td class="px-4 py-3">
                                    <div class="flex flex-col gap-1">
                                        <button
                                            wire:click="moveUp({{ $index }})"
                                            @if($index === 0) disabled @endif
                                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 disabled:opacity-30 disabled:cursor-not-allowed"
                                            title="Move up"
                                        >
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                            </svg>
                                        </button>
                                        <button
                                            wire:click="moveDown({{ $index }})"
                                            @if($index === count($lineItems) - 1) disabled @endif
                                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 disabled:opacity-30 disabled:cursor-not-allowed"
                                            title="Move down"
                                        >
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>

                                <!-- Product/Service Name -->
                                <td class="px-4 py-3">
                                    @if($editingItemId === $index)
                                        <div class="space-y-2">
                                            <input
                                                wire:model="lineItems.{{ $index }}.name"
                                                type="text"
                                                placeholder="Product/Service name"
                                                class="block w-full bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 text-sm"
                                            />
                                            <textarea
                                                wire:model="lineItems.{{ $index }}.description"
                                                rows="2"
                                                placeholder="Description (optional)"
                                                class="block w-full bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 text-sm"
                                            ></textarea>
                                            @error("lineItems.{$index}.name")
                                                <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @else
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $item['name'] ?: 'Unnamed Item' }}
                                            </p>
                                            @if($item['description'])
                                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                    {{ $item['description'] }}
                                                </p>
                                            @endif
                                        </div>
                                    @endif
                                </td>

                                <!-- SKU -->
                                <td class="px-4 py-3">
                                    @if($editingItemId === $index)
                                        <input
                                            wire:model="lineItems.{{ $index }}.sku"
                                            type="text"
                                            placeholder="SKU"
                                            class="block w-full bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 text-sm"
                                        />
                                    @else
                                        <span class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $item['sku'] ?: '-' }}
                                        </span>
                                    @endif
                                </td>

                                <!-- Quantity -->
                                <td class="px-4 py-3 text-right">
                                    @if($editingItemId === $index)
                                        <input
                                            wire:model.live="lineItems.{{ $index }}.quantity"
                                            type="number"
                                            min="1"
                                            step="1"
                                            class="block w-full bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 text-sm text-right"
                                        />
                                    @else
                                        <span class="text-sm text-gray-900 dark:text-white">
                                            {{ $item['quantity'] }}
                                        </span>
                                    @endif
                                </td>

                                <!-- Unit Price -->
                                <td class="px-4 py-3 text-right">
                                    @if($editingItemId === $index)
                                        <input
                                            wire:model.live="lineItems.{{ $index }}.unit_price"
                                            type="number"
                                            min="0"
                                            step="0.01"
                                            class="block w-full bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 text-sm text-right"
                                        />
                                    @else
                                        <span class="text-sm text-gray-900 dark:text-white">
                                            ${{ number_format($item['unit_price'], 2) }}
                                        </span>
                                    @endif
                                </td>

                                <!-- Discount -->
                                <td class="px-4 py-3 text-right">
                                    @if($editingItemId === $index)
                                        <div class="space-y-2">
                                            <div class="flex items-center gap-1">
                                                <input
                                                    wire:model.live="lineItems.{{ $index }}.discount_percent"
                                                    type="number"
                                                    min="0"
                                                    max="100"
                                                    step="0.01"
                                                    placeholder="0"
                                                    class="block w-full bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 text-sm text-right"
                                                />
                                                <span class="text-sm text-gray-500 dark:text-gray-400">%</span>
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 text-right">
                                                or
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <span class="text-sm text-gray-500 dark:text-gray-400">$</span>
                                                <input
                                                    wire:model.live="lineItems.{{ $index }}.discount_amount"
                                                    type="number"
                                                    min="0"
                                                    step="0.01"
                                                    placeholder="0.00"
                                                    class="block w-full bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 text-sm text-right"
                                                />
                                            </div>
                                        </div>
                                    @else
                                        @if($item['discount_percent'] > 0)
                                            <span class="text-sm text-green-600 dark:text-green-400">
                                                {{ $item['discount_percent'] }}%
                                            </span>
                                        @elseif($item['discount_amount'] > 0)
                                            <span class="text-sm text-green-600 dark:text-green-400">
                                                ${{ number_format($item['discount_amount'], 2) }}
                                            </span>
                                        @else
                                            <span class="text-sm text-gray-400 dark:text-gray-500">-</span>
                                        @endif
                                    @endif
                                </td>

                                <!-- Line Total -->
                                <td class="px-4 py-3 text-right">
                                    @if($editingItemId === $index)
                                        <div class="space-y-1">
                                            <span class="text-sm font-semibold text-amber-600 dark:text-amber-400">
                                                ${{ number_format($item['line_total'], 2) }}
                                            </span>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                Live preview
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                            ${{ number_format($item['line_total'], 2) }}
                                        </span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($editingItemId === $index)
                                            <button
                                                wire:click="saveLineItem({{ $index }})"
                                                type="button"
                                                class="inline-flex items-center gap-1 px-2 py-1 text-green-600 dark:text-green-400 hover:text-white hover:bg-green-600 dark:hover:bg-green-500 rounded text-sm font-medium transition-colors"
                                                title="Save (Enter)"
                                            >
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                <span class="text-xs">Save</span>
                                            </button>
                                        @else
                                            <button
                                                wire:click="editLineItem({{ $index }})"
                                                type="button"
                                                class="text-amber-600 dark:text-amber-400 hover:text-amber-900 dark:hover:text-amber-300 text-sm font-medium"
                                                title="Edit"
                                            >
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
                                        @endif
                                        <button
                                            wire:click="removeLineItem({{ $index }})"
                                            type="button"
                                            class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 text-sm font-medium"
                                            title="Delete"
                                        >
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No line items</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by adding a product or service.</p>
                <div class="mt-6">
                    <button
                        wire:click="addLineItem"
                        type="button"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 dark:focus:ring-offset-gray-800"
                    >
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add First Line Item
                    </button>
                </div>
            </div>
        @endif
    </div>

    <!-- Summary Section -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                Quote Summary
            </h3>
        </div>

        <div class="px-6 py-4 space-y-4">
            <!-- Global Discount -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="globalDiscountType" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Global Discount Type
                    </label>
                    <select
                        wire:model.live="globalDiscountType"
                        id="globalDiscountType"
                        class="block w-full bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 text-sm"
                    >
                        <option value="none">No Discount</option>
                        <option value="percentage">Percentage</option>
                        <option value="fixed">Fixed Amount</option>
                    </select>
                </div>

                @if($globalDiscountType !== 'none')
                    <div>
                        <label for="globalDiscountValue" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Discount Value
                        </label>
                        <div class="relative">
                            @if($globalDiscountType === 'percentage')
                                <input
                                    wire:model.live="globalDiscountValue"
                                    type="number"
                                    min="0"
                                    max="100"
                                    step="0.01"
                                    id="globalDiscountValue"
                                    class="block w-full bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 text-sm pr-8"
                                />
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 dark:text-gray-400 text-sm">%</span>
                                </div>
                            @else
                                <input
                                    wire:model.live="globalDiscountValue"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    id="globalDiscountValue"
                                    class="block w-full bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 text-sm pl-8"
                                />
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 dark:text-gray-400 text-sm">$</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Tax Rate -->
                <div>
                    <label for="taxRate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Tax Rate (%)
                    </label>
                    <input
                        wire:model.live="taxRate"
                        type="number"
                        min="0"
                        max="100"
                        step="0.01"
                        id="taxRate"
                        placeholder="0.00"
                        class="block w-full bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 text-sm"
                    />
                </div>
            </div>

            <!-- Summary Breakdown -->
            <div class="border-t border-gray-200 dark:border-gray-700 pt-4 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Subtotal:</span>
                    <span class="font-medium text-gray-900 dark:text-white">
                        ${{ number_format($this->subtotal, 2) }}
                    </span>
                </div>

                @if($this->lineDiscountTotal > 0)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Line Item Discounts:</span>
                        <span class="font-medium text-green-600 dark:text-green-400">
                            -${{ number_format($this->lineDiscountTotal, 2) }}
                        </span>
                    </div>
                @endif

                @if($this->globalDiscountAmount > 0)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">
                            Global Discount
                            @if($globalDiscountType === 'percentage')
                                ({{ $globalDiscountValue }}%)
                            @endif:
                        </span>
                        <span class="font-medium text-green-600 dark:text-green-400">
                            -${{ number_format($this->globalDiscountAmount, 2) }}
                        </span>
                    </div>
                @endif

                @if($this->totalDiscount > 0)
                    <div class="flex justify-between text-sm border-t border-gray-200 dark:border-gray-700 pt-2">
                        <span class="font-medium text-gray-600 dark:text-gray-400">Total Discounts:</span>
                        <span class="font-semibold text-green-600 dark:text-green-400">
                            -${{ number_format($this->totalDiscount, 2) }}
                        </span>
                    </div>
                @endif

                @if($taxRate > 0)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Tax ({{ $taxRate }}%):</span>
                        <span class="font-medium text-gray-900 dark:text-white">
                            ${{ number_format($this->taxAmount, 2) }}
                        </span>
                    </div>
                @endif

                <div class="flex justify-between text-base font-semibold border-t-2 border-gray-300 dark:border-gray-600 pt-3">
                    <span class="text-gray-900 dark:text-white">Grand Total:</span>
                    <span class="text-amber-600 dark:text-amber-400">
                        ${{ number_format($this->grandTotal, 2) }}
                    </span>
                </div>
            </div>

            <!-- Save Button -->
            <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                <button
                    wire:click="calculateTotals"
                    type="button"
                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 dark:focus:ring-offset-gray-800 transition-colors"
                >
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Update Quote Totals
                </button>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    @if($deletingItemId !== null)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 transition-opacity" wire:click="cancelDelete"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                    Delete Line Item
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Are you sure you want to delete "{{ $lineItems[$deletingItemId]['name'] ?? 'this item' }}"? This action cannot be undone.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button
                            wire:click="confirmDelete"
                            type="button"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:focus:ring-offset-gray-800 sm:ml-3 sm:w-auto sm:text-sm transition-colors"
                        >
                            Delete
                        </button>
                        <button
                            wire:click="cancelDelete"
                            type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 dark:focus:ring-offset-gray-800 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
