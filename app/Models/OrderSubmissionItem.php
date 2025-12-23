<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderSubmissionItem extends Model
{
  protected $fillable = [
    'order_submission_id',
    'product_name',
    'unit_price',
    'qty',
    'line_total',
    'note',
  ];

  protected $casts = [
    'unit_price' => 'decimal:2',
    'line_total' => 'decimal:2',
    'qty' => 'integer',
  ];

  public function submission(): BelongsTo
  {
    return $this->belongsTo(OrderSubmission::class, 'order_submission_id');
  }
}
