<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_id',
        'name',
        'description',
        'sku',
        'quantity',
        'unit_price',
        'discount_percent',
        'discount_amount',
        'line_total',
        'custom_fields',
        'sort_order',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'line_total' => 'decimal:2',
        'custom_fields' => 'array',
        'sort_order' => 'integer',
    ];

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    // Calculate line total based on quantity, unit price, and discounts
    public function calculateLineTotal(): float
    {
        $subtotal = $this->quantity * $this->unit_price;
        
        if ($this->discount_percent > 0) {
            $this->discount_amount = $subtotal * ($this->discount_percent / 100);
        }
        
        return $subtotal - $this->discount_amount;
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $item->line_total = $item->calculateLineTotal();
        });

        static::saved(function ($item) {
            $item->quote->updateTotals();
        });

        static::deleted(function ($item) {
            $item->quote->updateTotals();
        });
    }
}