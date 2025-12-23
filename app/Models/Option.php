<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Option extends Model
{
    protected $fillable = ['name', 'base_price', 'is_active', 'sort'];

    protected $casts = [
        'is_active' => 'boolean',
        'base_price' => 'decimal:2',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_option')
            ->withPivot(['is_allowed','is_default','is_required','price_override','max_qty','sort'])
            ->withTimestamps();
    }
}
