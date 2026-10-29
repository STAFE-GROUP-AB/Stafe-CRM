<?php

namespace App\Livewire\SalesEnablement;

use App\Models\Quote;
use App\Models\QuoteItem;
use Livewire\Component;

class QuoteLineItemManager extends Component
{
    public Quote $quote;

    public $lineItems = [];
    public $taxRate = 0;
    public $globalDiscountType = 'none';
    public $globalDiscountValue = 0;

    public $editingItemId = null;
    public $deletingItemId = null;

    protected $rules = [
        'lineItems.*.name' => 'required|string|max:255',
        'lineItems.*.description' => 'nullable|string',
        'lineItems.*.sku' => 'nullable|string|max:100',
        'lineItems.*.quantity' => 'required|integer|min:1',
        'lineItems.*.unit_price' => 'required|numeric|min:0',
        'lineItems.*.discount_percent' => 'nullable|numeric|min:0|max:100',
        'lineItems.*.discount_amount' => 'nullable|numeric|min:0',
        'lineItems.*.tax_rate' => 'nullable|numeric|min:0|max:100',
        'taxRate' => 'nullable|numeric|min:0|max:100',
        'globalDiscountType' => 'required|in:none,percentage,fixed',
        'globalDiscountValue' => 'nullable|numeric|min:0',
    ];

    public function mount(Quote $quote)
    {
        $this->quote = $quote->load('items');
        $this->loadLineItems();
        $this->taxRate = 0;
    }

    public function loadLineItems()
    {
        $this->lineItems = $this->quote->items()->orderBy('sort_order')->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'description' => $item->description,
                'sku' => $item->sku,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'discount_percent' => $item->discount_percent ?? 0,
                'discount_amount' => $item->discount_amount ?? 0,
                'tax_rate' => $item->custom_fields['tax_rate'] ?? 0,
                'line_total' => $item->line_total,
                'sort_order' => $item->sort_order,
            ];
        })->toArray();
    }

    public function addLineItem()
    {
        $maxSortOrder = empty($this->lineItems) ? 0 : max(array_column($this->lineItems, 'sort_order'));

        $this->lineItems[] = [
            'id' => null,
            'name' => '',
            'description' => '',
            'sku' => '',
            'quantity' => 1,
            'unit_price' => 0,
            'discount_percent' => 0,
            'discount_amount' => 0,
            'tax_rate' => 0,
            'line_total' => 0,
            'sort_order' => $maxSortOrder + 1,
        ];

        $this->editingItemId = count($this->lineItems) - 1;
    }

    public function removeLineItem($index)
    {
        $this->deletingItemId = $index;
    }

    public function confirmDelete()
    {
        if ($this->deletingItemId !== null) {
            $item = $this->lineItems[$this->deletingItemId];

            if ($item['id']) {
                QuoteItem::find($item['id'])->delete();
            }

            array_splice($this->lineItems, $this->deletingItemId, 1);
            $this->deletingItemId = null;
            $this->reorderItems();
            $this->calculateTotals();
        }
    }

    public function cancelDelete()
    {
        $this->deletingItemId = null;
    }

    public function editLineItem($index)
    {
        $this->editingItemId = $index;
    }

    public function saveLineItem($index)
    {
        $this->validate([
            "lineItems.{$index}.name" => 'required|string|max:255',
            "lineItems.{$index}.quantity" => 'required|integer|min:1',
            "lineItems.{$index}.unit_price" => 'required|numeric|min:0',
        ]);

        $itemData = $this->lineItems[$index];

        $lineTotal = $this->calculateLineTotal($itemData);
        $this->lineItems[$index]['line_total'] = $lineTotal;

        if ($itemData['id']) {
            $item = QuoteItem::find($itemData['id']);
            $item->update([
                'name' => $itemData['name'],
                'description' => $itemData['description'],
                'sku' => $itemData['sku'],
                'quantity' => $itemData['quantity'],
                'unit_price' => $itemData['unit_price'],
                'discount_percent' => $itemData['discount_percent'],
                'discount_amount' => $itemData['discount_amount'],
                'custom_fields' => ['tax_rate' => $itemData['tax_rate']],
                'sort_order' => $itemData['sort_order'],
            ]);
        } else {
            $item = QuoteItem::create([
                'quote_id' => $this->quote->id,
                'name' => $itemData['name'],
                'description' => $itemData['description'],
                'sku' => $itemData['sku'],
                'quantity' => $itemData['quantity'],
                'unit_price' => $itemData['unit_price'],
                'discount_percent' => $itemData['discount_percent'],
                'discount_amount' => $itemData['discount_amount'],
                'custom_fields' => ['tax_rate' => $itemData['tax_rate']],
                'sort_order' => $itemData['sort_order'],
            ]);

            $this->lineItems[$index]['id'] = $item->id;
        }

        $this->editingItemId = null;
        $this->calculateTotals();
    }

    public function moveUp($index)
    {
        if ($index > 0) {
            $temp = $this->lineItems[$index];
            $this->lineItems[$index] = $this->lineItems[$index - 1];
            $this->lineItems[$index - 1] = $temp;
            $this->reorderItems();
        }
    }

    public function moveDown($index)
    {
        if ($index < count($this->lineItems) - 1) {
            $temp = $this->lineItems[$index];
            $this->lineItems[$index] = $this->lineItems[$index + 1];
            $this->lineItems[$index + 1] = $temp;
            $this->reorderItems();
        }
    }

    public function reorderItems()
    {
        foreach ($this->lineItems as $index => $item) {
            $this->lineItems[$index]['sort_order'] = $index;

            if ($item['id']) {
                QuoteItem::find($item['id'])->update(['sort_order' => $index]);
            }
        }
    }

    public function updatedLineItems($value, $key)
    {
        if (preg_match('/^(\d+)\.(quantity|unit_price|discount_percent|discount_amount)$/', $key, $matches)) {
            $index = (int) $matches[1];
            $this->calculateLineTotal($this->lineItems[$index]);
            $this->lineItems[$index]['line_total'] = $this->calculateLineTotal($this->lineItems[$index]);
        }
    }

    public function calculateLineTotal($item)
    {
        $subtotal = $item['quantity'] * $item['unit_price'];

        if ($item['discount_percent'] > 0) {
            $discountAmount = $subtotal * ($item['discount_percent'] / 100);
            $subtotal -= $discountAmount;
        } elseif ($item['discount_amount'] > 0) {
            $subtotal -= $item['discount_amount'];
        }

        return max(0, $subtotal);
    }

    public function calculateTotals()
    {
        $subtotal = 0;
        $totalDiscount = 0;

        foreach ($this->lineItems as $item) {
            $lineSubtotal = $item['quantity'] * $item['unit_price'];
            $subtotal += $lineSubtotal;

            if ($item['discount_percent'] > 0) {
                $totalDiscount += $lineSubtotal * ($item['discount_percent'] / 100);
            } elseif ($item['discount_amount'] > 0) {
                $totalDiscount += $item['discount_amount'];
            }
        }

        $subtotalAfterLineDiscounts = $subtotal - $totalDiscount;

        $globalDiscount = 0;
        if ($this->globalDiscountType === 'percentage') {
            $globalDiscount = $subtotalAfterLineDiscounts * ($this->globalDiscountValue / 100);
        } elseif ($this->globalDiscountType === 'fixed') {
            $globalDiscount = $this->globalDiscountValue;
        }

        $totalAfterDiscounts = $subtotalAfterLineDiscounts - $globalDiscount;
        $taxAmount = $totalAfterDiscounts * ($this->taxRate / 100);
        $grandTotal = $totalAfterDiscounts + $taxAmount;

        $this->quote->update([
            'subtotal' => $subtotal,
            'discount_amount' => $totalDiscount + $globalDiscount,
            'tax_amount' => $taxAmount,
            'total_amount' => $grandTotal,
        ]);

        $this->quote->refresh();
    }

    public function getSubtotalProperty()
    {
        return collect($this->lineItems)->sum(fn($item) => $item['quantity'] * $item['unit_price']);
    }

    public function getLineDiscountTotalProperty()
    {
        return collect($this->lineItems)->sum(function ($item) {
            $lineSubtotal = $item['quantity'] * $item['unit_price'];

            if ($item['discount_percent'] > 0) {
                return $lineSubtotal * ($item['discount_percent'] / 100);
            }

            return $item['discount_amount'] ?? 0;
        });
    }

    public function getGlobalDiscountAmountProperty()
    {
        $subtotalAfterLineDiscounts = $this->subtotal - $this->lineDiscountTotal;

        if ($this->globalDiscountType === 'percentage') {
            return $subtotalAfterLineDiscounts * ($this->globalDiscountValue / 100);
        }

        if ($this->globalDiscountType === 'fixed') {
            return $this->globalDiscountValue;
        }

        return 0;
    }

    public function getTotalDiscountProperty()
    {
        return $this->lineDiscountTotal + $this->globalDiscountAmount;
    }

    public function getTaxAmountProperty()
    {
        $subtotalAfterDiscounts = $this->subtotal - $this->totalDiscount;
        return $subtotalAfterDiscounts * ($this->taxRate / 100);
    }

    public function getGrandTotalProperty()
    {
        return $this->subtotal - $this->totalDiscount + $this->taxAmount;
    }

    public function render()
    {
        return view('livewire.sales-enablement.quote-line-item-manager');
    }
}
