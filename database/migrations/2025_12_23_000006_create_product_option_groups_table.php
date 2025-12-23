<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('product_option_groups', function (Blueprint $table) {
      $table->id();
      $table->foreignId('product_id')->constrained()->cascadeOnDelete();
      $table->foreignId('option_group_id')->constrained('option_groups')->cascadeOnDelete();
      $table->unsignedInteger('sort')->default(0);
      $table->unsignedInteger('min_select')->default(0); // 0 = ไม่บังคับ
      $table->unsignedInteger('max_select')->default(0); // 0 = ไม่จำกัด
      $table->boolean('is_enabled')->default(true);
      $table->timestamps();

      $table->unique(['product_id','option_group_id']);
      $table->index(['product_id','sort']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('product_option_groups');
  }
};
