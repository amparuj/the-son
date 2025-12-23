<?php

use App\Http\Controllers\Staff\ProductOptionController;

Route::prefix('staff')->name('staff.')->middleware(['auth'])->group(function () {
  Route::get('products/{product}/options', [ProductOptionController::class, 'edit'])
    ->name('products.options.edit');

  Route::post('products/{product}/options', [ProductOptionController::class, 'update'])
    ->name('products.options.update');
});
