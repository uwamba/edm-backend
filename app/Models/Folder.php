<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    // A folder can have many documents
    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}
