<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('order_item_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained('order_items')->cascadeOnDelete();
            $table->foreignId('option_id')->constrained('options')->restrictOnDelete();

            $table->string('option_name_snapshot');
            $table->decimal('option_price_snapshot', 10, 2)->default(0);
            $table->unsignedInteger('qty')->default(1);

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('order_item_options');
    }
};
