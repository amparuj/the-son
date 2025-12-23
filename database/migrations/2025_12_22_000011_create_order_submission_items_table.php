<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('order_submission_items', function (Blueprint $table) {
      $table->id();
      $table->foreignId('order_submission_id')
        ->constrained('order_submissions')
        ->cascadeOnDelete();

      $table->string('product_name');
      $table->decimal('unit_price', 10, 2);
      $table->unsignedInteger('qty');
      $table->decimal('line_total', 10, 2);
      $table->string('note')->nullable();

      $table->timestamps();
      $table->index(['order_submission_id']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('order_submission_items');
  }
};
