<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::table('order_items', function (Blueprint $table) {
      $table->foreignId('order_submission_id')
        ->nullable()
        ->after('order_id')
        ->constrained('order_submissions')
        ->nullOnDelete();

      $table->enum('status', ['OPEN', 'DONE'])->default('OPEN')->index()->after('created_by');
      $table->timestamp('done_at')->nullable()->index()->after('status');

      $table->index(['order_id', 'order_submission_id']);
    });
  }

  public function down(): void
  {
    Schema::table('order_items', function (Blueprint $table) {
      $table->dropIndex(['order_id', 'order_submission_id']);
      $table->dropConstrainedForeignId('order_submission_id');
      $table->dropColumn(['status', 'done_at']);
    });
  }
};
