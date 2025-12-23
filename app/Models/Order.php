<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'order_no','channel','table_id','status',
        'subtotal','discount_type','discount_value','total',
        'opened_at','paid_at','created_by'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'total' => 'decimal:2',
        'opened_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function delivery(): HasOne
    {
        return $this->hasOne(OrderDelivery::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(\App\Models\OrderSubmission::class);
    }

    public function isOpen(): bool
    {
        return $this->status === 'OPEN';
    }
}
