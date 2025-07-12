<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\DocumentVersion;
use App\Models\AuditLog;
use App\Models\Tag;


class Document extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'path',
        'size',
        'mime_type',
        'tags',
        'user_id',
        'archived_at'
    ];

    protected $casts = [
        'tags' => 'array',
        'archived_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function versions()
    {
        return $this->hasMany(DocumentVersion::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'document_tag');
    }
}
