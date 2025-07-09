<?php

use Illuminate\Support\Facades\Route;
use Modules\WorkFlow\Http\Controllers\WorkFlowController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('workflows', WorkFlowController::class)->names('workflow');
});
