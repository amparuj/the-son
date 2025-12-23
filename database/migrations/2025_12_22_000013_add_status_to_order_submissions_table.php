<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::table('order_submissions', function (Blueprint $table) {
      $table->enum('status', ['OPEN', 'DONE'])->default('OPEN')->index()->after('submitted_at');
      $table->timestamp('done_at')->nullable()->index()->after('status');
    });
  }

  public function down(): void
  {
    Schema::table('order_submissions', function (Blueprint $table) {
      $table->dropColumn(['status', 'done_at']);
    });
  }
};
