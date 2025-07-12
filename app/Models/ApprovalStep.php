<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApprovalStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'approval_process_id',
        'step_number',
        'approver_id',
        'status'
    ];

    public function approvalProcess()
    {
        return $this->belongsTo(ApprovalProcess::class);
    }

    public function approver()
    {
        return $this->belongsTo(\App\Models\User::class, 'approver_id');
    }
}
