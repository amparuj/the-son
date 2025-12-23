<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDelivery extends Model
{
    protected $fillable = ['order_id','customer_name','phone','address','note'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
