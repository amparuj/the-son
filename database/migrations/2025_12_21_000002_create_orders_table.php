<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no')->unique();

            $table->enum('channel', ['DINE_IN', 'DELIVERY'])->default('DINE_IN');
            $table->foreignId('table_id')->nullable()->constrained('tables');

            $table->enum('status', ['OPEN', 'PAID', 'CANCELLED'])->default('OPEN');

            $table->decimal('subtotal', 10, 2)->default(0);
            $table->enum('discount_type', ['NONE', 'AMOUNT', 'PERCENT'])->default('NONE');
            $table->decimal('discount_value', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);

            $table->timestamp('opened_at')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->index(['table_id', 'status']);
            $table->index(['channel', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
