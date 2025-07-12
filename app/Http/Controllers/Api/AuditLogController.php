<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuditLogResource;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index()
    {
        return AuditLogResource::collection(AuditLog::with(['user', 'document'])->paginate(10));
    }

    public function show(AuditLog $auditLog)
    {
        $auditLog->load(['user', 'document']);
        return new AuditLogResource($auditLog);
    }
}
