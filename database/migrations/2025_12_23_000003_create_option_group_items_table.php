<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('option_group_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('option_group_id')->constrained('option_groups')->cascadeOnDelete();
            $table->foreignId('option_id')->constrained('options')->cascadeOnDelete();
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();

            $table->unique(['option_group_id', 'option_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('option_group_items');
    }
};
