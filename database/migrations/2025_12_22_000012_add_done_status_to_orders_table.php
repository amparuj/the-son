<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
  public function up(): void
  {
    $driver = DB::getDriverName();

    // NOTE:
    // - MySQL: orders.status is ENUM from initial migration; extend enum with DONE.
    // - Other drivers: initial enum may be implemented differently; you may prefer fresh migration if it fails.
    if ($driver === 'mysql') {
      DB::statement("ALTER TABLE `orders` MODIFY `status` ENUM('OPEN','DONE','PAID','CANCELLED') NOT NULL DEFAULT 'OPEN'");
    }
  }

  public function down(): void
  {
    $driver = DB::getDriverName();
    if ($driver === 'mysql') {
      DB::statement("ALTER TABLE `orders` MODIFY `status` ENUM('OPEN','PAID','CANCELLED') NOT NULL DEFAULT 'OPEN'");
    }
  }
};
