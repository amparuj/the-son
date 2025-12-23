<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();

            // snapshot at time of sale
            $table->string('product_name');
            $table->decimal('unit_price', 10, 2);
            $table->unsignedInteger('qty');
            $table->decimal('line_total', 10, 2);

            $table->string('note')->nullable();

            $table->enum('created_via', ['STAFF', 'QR'])->default('STAFF');
            $table->foreignId('created_by')->nullable()->constrained('users');

            $table->timestamps();

            $table->index(['order_id', 'created_via']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
