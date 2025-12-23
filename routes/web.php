<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QrMenuController;

use App\Http\Controllers\Staff\OrderController;
use App\Http\Controllers\Staff\CheckoutController;
use App\Http\Controllers\Staff\MonitorController;
use App\Http\Controllers\Staff\ProductController;
use App\Http\Controllers\Staff\ProductOptionController;
use App\Http\Controllers\Staff\OptionController;
use App\Http\Controllers\Staff\OptionGroupController; // ✅ NEW

Route::get('/', function () {
    return redirect()->route('staff.orders.dashboard');
});

require __DIR__.'/auth.php';

// ===== Public QR =====
Route::get('/t/{table:public_uuid}', [QrMenuController::class, 'showMenu'])->name('qr.menu');

Route::post('/t/{table:public_uuid}/submit', [QrMenuController::class, 'submit'])
    ->middleware('throttle:10,1')
    ->name('qr.submit');


// ===== Staff (protected) =====
Route::middleware(['auth'])->prefix('staff')->name('staff.')->group(function () {

    // ---- Monitor ----
    Route::get('/monitor/submissions', [MonitorController::class, 'submissions'])->name('monitor.submissions');
    Route::patch('/monitor/submissions/{submission}/done', [MonitorController::class, 'markSubmissionDone'])->name('monitor.submissions.done');

    // ---- Orders ----
    Route::get('/orders', [OrderController::class, 'dashboard'])->name('orders.dashboard');

    Route::post('/orders/open/dine-in/{table}', [OrderController::class, 'openDineIn'])->name('orders.open.dinein');
    Route::post('/orders/open/delivery', [OrderController::class, 'openDelivery'])->name('orders.open.delivery');

    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

    Route::post('/orders/{order}/items', [OrderController::class, 'addItem'])->name('orders.items.add');

    Route::post('/orders/{order}/submit', [OrderController::class, 'submitPending'])->name('orders.submit');
    Route::post('/orders/{order}/submit-stay', [OrderController::class, 'submitPendingStay'])->name('orders.submitStay');

    Route::patch('/order-items/{item}', [OrderController::class, 'updateItemQty'])->name('orders.items.qty');
    Route::delete('/order-items/{item}', [OrderController::class, 'removeItem'])->name('orders.items.remove');

    Route::patch('/orders/{order}/discount', [OrderController::class, 'applyDiscount'])->name('orders.discount');
    Route::patch('/orders/{order}/delivery', [OrderController::class, 'updateDelivery'])->name('orders.delivery.update');

    Route::get('/orders/{order}/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/orders/{order}/checkout', [CheckoutController::class, 'pay'])->name('checkout.pay');

    // ---- Products CRUD ----
    Route::resource('products', ProductController::class)->except(['show']);

    // ---- Product Options (pivot) ----
    Route::get('products/{product}/options', [ProductOptionController::class, 'edit'])->name('products.options.edit');
    Route::post('products/{product}/options', [ProductOptionController::class, 'update'])->name('products.options.update');

    // ---- Options CRUD (Master) ----
    Route::resource('options', OptionController::class)->except(['show']);

    // ---- Option Groups CRUD ✅ NEW ----
    Route::resource('option-groups', OptionGroupController::class)
        ->parameters(['option-groups' => 'option_group'])
        ->except(['show']);
});
