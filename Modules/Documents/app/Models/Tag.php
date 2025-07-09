<?php

namespace App\Modules\Documents\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['name'];

    public function documents()
    {
        return $this->belongsToMany(Document::class, 'document_tag');
    }
}
