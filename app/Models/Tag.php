<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['name', 'value'];

    public function documents()
    {
        return $this->belongsToMany(Document::class, 'document_tag');
    }

    public function getFullTagAttribute(): string
    {
        return "{$this->name}: {$this->value}";
    }
}

