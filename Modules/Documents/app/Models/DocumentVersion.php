<?php
namespace App\Modules\Documents\Models;

use Illuminate\Database\Eloquent\Model;

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
