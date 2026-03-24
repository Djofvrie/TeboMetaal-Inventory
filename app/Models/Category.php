<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'icon', 'sort_order'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class)
            ->orderByRaw("CAST(dimension AS INTEGER) ASC")
            ->orderByRaw("CAST(CASE WHEN INSTR(dimension, 'x') > 0 THEN SUBSTR(dimension, INSTR(dimension, 'x') + 1) ELSE '0' END AS INTEGER) ASC");
    }
}
