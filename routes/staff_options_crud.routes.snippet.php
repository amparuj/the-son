<?php

use App\Http\Controllers\Staff\OptionController;

Route::prefix('staff')->name('staff.')->middleware(['auth'])->group(function () {
  Route::resource('options', OptionController::class)->except(['show']);
});
