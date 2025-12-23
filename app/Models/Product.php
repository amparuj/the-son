<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{


    public function options(): BelongsToMany
    {
        return $this->belongsToMany(Option::class, 'product_option')
            ->withPivot(['is_allowed','is_default','is_required','price_override','max_qty','sort'])
            ->withTimestamps()
            ->orderBy('product_option.sort');
    }
    protected $fillable = ['name', 'price', 'is_active', 'image_path'];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}
