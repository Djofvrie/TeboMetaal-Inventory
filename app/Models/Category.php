<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'icon', 'sort_order'];

    public function products(): HasMany
    {
        // Sort by numeric dimension value, stripping Ø prefix
        // Use DECIMAL for MariaDB/MySQL compatibility, +0 trick as fallback
        return $this->hasMany(Product::class)
            ->orderByRaw("REPLACE(dimension, 'Ø', '') + 0 ASC")
            ->orderByRaw("CASE WHEN LOCATE('x', dimension) > 0 THEN SUBSTRING(dimension, LOCATE('x', dimension) + 1) + 0 ELSE 0 END ASC");
    }
}
