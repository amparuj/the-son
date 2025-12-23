<?php

use App\Http\Controllers\Staff\OptionGroupController;

Route::prefix('staff')->name('staff.')->middleware(['auth'])->group(function () {
  Route::resource('option-groups', OptionGroupController::class)->parameters([
    'option-groups' => 'option_group',
  ])->except(['show']);
});
