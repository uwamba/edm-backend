<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Form extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'created_by'
    ];


    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function fields()
    {
        return $this->hasMany(Field::class);
    }

    public function approvalProcess()
    {
        return $this->hasOne(ApprovalProcess::class);
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

   
}
