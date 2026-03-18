<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    protected $fillable = ['product_id', 'wall_thickness', 'quality', 'low_stock_threshold'];

    protected $casts = [
        'wall_thickness' => 'decimal:1',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function stockItems(): HasMany
    {
        return $this->hasMany(StockItem::class)->orderByDesc('length_mm');
    }

    public function mutations(): HasMany
    {
        return $this->hasMany(StockMutation::class)->orderByDesc('created_at');
    }

    public function totalLengthMm(): int
    {
        return $this->stockItems->sum(fn ($item) => $item->length_mm * $item->quantity);
    }

    public function totalPieces(): int
    {
        return $this->stockItems->sum('quantity');
    }

    public function displayName(): string
    {
        return $this->product->dimension . 'x' . rtrim(rtrim(number_format($this->wall_thickness, 1, ',', ''), '0'), ',');
    }
}
