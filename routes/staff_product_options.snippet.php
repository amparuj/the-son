<?php
// Add inside your existing staff route group (prefix('staff')->name('staff.')->middleware(['auth']))
use App\Http\Controllers\Staff\ProductOptionController;

Route::get('products/{product}/options', [ProductOptionController::class, 'edit'])
  ->name('products.options.edit');

Route::post('products/{product}/options', [ProductOptionController::class, 'update'])
  ->name('products.options.update');
