<?php
// ตรวจสอบให้แน่ใจว่า App\Models\OptionGroup มี relation นี้

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

public function options(): BelongsToMany
{
  return $this->belongsToMany(\App\Models\Option::class, 'option_group_items')
    ->withPivot(['sort'])
    ->orderBy('option_group_items.sort')
    ->withTimestamps();
}
