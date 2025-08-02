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
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\RoleController;



use App\Http\Controllers\Api\ModelTypeController;

// routes/api.php
Route::get('/model-types', [ModelTypeController::class, 'index']);
Route::post('/model-types', [ModelTypeController::class, 'store']);
Route::get('/tags', [TagController::class, 'index']);
Route::post('/tags', [TagController::class, 'store']);

Route::apiResource('companies', CompanyController::class);

Route::apiResource('users', UserController::class);

Route::apiResource('job-titles', \App\Http\Controllers\Api\JobTitleController::class);


Route::post('/approval-processes', [ApprovalController::class, 'store']);
Route::get('/forms/{form}/approval-process', [ApprovalController::class, 'show']);



Route::post('/token-login', [AuthController::class, 'tokenLogin']);
Route::middleware('auth:sanctum')->post('/token-logout', [AuthController::class, 'tokenLogout']);

Route::middleware('guest')->post('/login', [AuthController::class, 'login']);
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('form/{form_id}/submissions', [SubmissionController::class, 'submissions']);

Route::get('form/submissions/list', [SubmissionController::class, 'index']);
Route::get('submissions/{id}', [SubmissionController::class, 'show']);

Route::apiResource('forms', FormController::class);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('approval-step/approve', [ApprovalController::class, 'approve']);
    Route::post('approval-step/reject', [ApprovalController::class, 'reject']);
});




Route::apiResource('documents', DocumentController::class);
Route::apiResource('document-versions', DocumentVersionController::class);
Route::apiResource('tags', TagController::class);
Route::apiResource('audit-logs', AuditLogController::class);


Route::middleware('auth:sanctum')->group(function () {

});
