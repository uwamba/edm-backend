<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\DocumentVersionController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\AuditLogController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FormController;
use App\Http\Controllers\Api\SubmissionController;
use App\Http\Controllers\Api\ApprovalController;


Route::post('/token-login', [AuthController::class, 'tokenLogin']);
Route::middleware('auth:sanctum')->post('/token-logout', [AuthController::class, 'tokenLogout']);

Route::middleware('guest')->post('/login', [AuthController::class, 'login']);
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('form/submissions', [SubmissionController::class, 'submit']);

Route::get('form/submissions/list', [SubmissionController::class, 'index']);
Route::get('submissions/{id}', [SubmissionController::class, 'show']);

Route::apiResource('forms', FormController::class);
Route::post('workflow/{workflow}/approve', [ApprovalController::class, 'approve']);
Route::post('workflow/{workflow}/reject', [ApprovalController::class, 'reject']);


Route::apiResource('documents', DocumentController::class);
Route::apiResource('document-versions', DocumentVersionController::class);
Route::apiResource('tags', TagController::class);
Route::apiResource('audit-logs', AuditLogController::class);


Route::middleware('auth:sanctum')->group(function () {

});
