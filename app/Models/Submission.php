<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\ApprovalProcess;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_id',
        'user_id',
        'data'
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    
    public function fields()
{
    return $this->hasMany(SubmissionField::class);
}


public function approvalProcess()
{
    return $this->hasOneThrough(
        ApprovalProcess::class,
        Form::class,
        'id',          
        'form_id',   
        'form_id',      
        'id'             
    );
}



}
