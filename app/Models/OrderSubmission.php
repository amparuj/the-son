<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderSubmission extends Model
{
  protected $fillable = [
    'order_id',
    'source',
    'channel',
    'table_id',
    'created_by',
    'submitted_at',
    'status',
    'done_at',
  ];

  protected $casts = [
    'submitted_at' => 'datetime',
  ];

  public function order(): BelongsTo
  {
    return $this->belongsTo(Order::class);
  }

  public function table(): BelongsTo
  {
    return $this->belongsTo(Table::class);
  }

  public function items(): HasMany
  {
    return $this->hasMany(OrderSubmissionItem::class);
  }
}
