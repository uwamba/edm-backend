<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Field extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_id',
        'label',
        'type',
        'options',
        'required',
        'validations',
        'conditions',
        'parentField',
        'parentMapping',
    ];

   
    protected $casts = [
    'options' => 'array',
    'validations' => 'array',
    'conditions' => 'array',
    'parentMapping' => 'array',
];


    public function form()
    {
        return $this->belongsTo(Form::class);
    }
}
