<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmissionField extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'field_id',
        'value',
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    public function field()
    {
        return $this->belongsTo(Field::class);
    }
}

