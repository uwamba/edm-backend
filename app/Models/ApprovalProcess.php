<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApprovalProcess extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_id',
        'name',
        'description'
    ];

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function steps()
    {
        return $this->hasMany(ApprovalStep::class);
    }
}
