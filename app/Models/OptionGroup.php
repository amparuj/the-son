<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class OptionGroup extends Model
{
    protected $fillable = ['name', 'sort'];

    public function options(): BelongsToMany
    {
        return $this->belongsToMany(Option::class, 'option_group_items')
            ->withPivot(['sort'])
            ->orderBy('option_group_items.sort')
            ->withTimestamps();
    }
}
