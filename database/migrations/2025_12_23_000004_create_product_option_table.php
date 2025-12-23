<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('product_option', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('option_id')->constrained('options')->cascadeOnDelete();

            $table->boolean('is_allowed')->default(true);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_required')->default(false);

            $table->decimal('price_override', 10, 2)->nullable();
            $table->unsignedInteger('max_qty')->nullable();
            $table->unsignedInteger('sort')->default(0);

            $table->timestamps();

            $table->unique(['product_id', 'option_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('product_option');
    }
};
