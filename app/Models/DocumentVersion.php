<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Document;

class DocumentVersion extends Model
{
    protected $fillable = [
        'document_id',
        'version',
        'path',
        'size',
        'mime_type'
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}
