<?php

namespace Modules\WorkFlow\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Workflow extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_id',
        'submission_id',
        'status'
    ];

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }
}
