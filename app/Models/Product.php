<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    protected $fillable = ['name', 'price', 'is_active', 'image_path'];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Options ที่อนุญาตให้เลือกได้ในสินค้า (ใช้ pivot: product_option)
     */
    public function options(): BelongsToMany
    {
        return $this->belongsToMany(Option::class, 'product_option')
            ->withPivot(['is_allowed','is_default','is_required','price_override','max_qty','sort'])
            ->withTimestamps()
            ->orderBy('product_option.sort')
            ->orderBy('options.id');
    }

    /**
     * กลุ่มตัวเลือกที่ผูกกับสินค้า (เรียงตาม pivot: product_option_groups.sort)
     * ใช้กับหน้าหลังบ้าน/การตั้งค่าระดับสินค้า
     */
    public function optionGroups(): BelongsToMany
    {
        return $this->belongsToMany(OptionGroup::class, 'product_option_groups')
            ->withPivot(['sort','min_select','max_select','is_enabled'])
            ->withTimestamps()
            ->orderBy('product_option_groups.sort')
            ->orderBy('option_groups.id');
    }

    /**
     * กลุ่มตัวเลือกสำหรับหน้า QR (เรียงตาม global sort ของ option_groups.sort)
     * ให้ตรงกับหน้า Option Groups
     */
    public function optionGroupsGlobal(): BelongsToMany
    {
        return $this->belongsToMany(OptionGroup::class, 'product_option_groups')
            ->withPivot(['sort','min_select','max_select','is_enabled'])
            ->withTimestamps()
            ->orderBy('option_groups.sort')
            ->orderBy('option_groups.id');
    }
}
