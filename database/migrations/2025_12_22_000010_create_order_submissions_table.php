<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('order_submissions', function (Blueprint $table) {
      $table->id();
      $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();

      $table->enum('source', ['QR', 'STAFF'])->index();
      $table->enum('channel', ['DINE_IN', 'DELIVERY'])->index();
      $table->foreignId('table_id')->nullable()->constrained('tables');

      $table->foreignId('created_by')->nullable()->constrained('users');
      $table->timestamp('submitted_at')->useCurrent()->index();

      $table->timestamps();
      $table->index(['submitted_at', 'id']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('order_submissions');
  }
};
