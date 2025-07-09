<?php

use Illuminate\Support\Facades\Route;
use Modules\WorkFlow\Http\Controllers\WorkFlowController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('workflows', WorkFlowController::class)->names('workflow');
});
