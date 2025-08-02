<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\{DocumentVersion, AuditLog, Tag, Folder};

class Document extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'description', 'path', 'size', 'mime_type','document_number',
        'tags', 'user_id', 'archived_at',
        'model_type', 'security_level','model_type_id'
    ];

    protected $casts = [
        'tags' => 'array',
        'archived_at' => 'datetime',
        'created_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Security Level Constants
    

    /** Relationships */
    public function user()        { return $this->belongsTo(\App\Models\User::class); }
    public function versions()    { return $this->hasMany(DocumentVersion::class); }
    public function auditLogs()   { return $this->hasMany(AuditLog::class); }
    public function tags()
{
    return $this->belongsToMany(Tag::class);
}
   

    /** Scopes */
    public function scopeActive($query)   { return $query->whereNull('archived_at'); }
    public function scopeOwnedBy($query, $userId) { return $query->where('user_id', $userId); }

    /** Accessors */
    public function getReadableSizeAttribute()
    {
        $bytes = $this->size;
        if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024) return number_format($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }

    /** Utilities */
    public function syncTags(array $tagIds) { $this->tags()->sync($tagIds); }

    public function attachTagByNameValue(string $name, string $value)
    {
        $tag = Tag::firstOrCreate(['name' => $name, 'value' => $value]);
        $this->tags()->syncWithoutDetaching([$tag->id]);
    }

    public function latestVersion()
    {
        return $this->versions()->latest('created_at')->first();
    }

    public function addVersion($filePath, $size, $mimeType)
    {
        return $this->versions()->create([
            'path' => $filePath,
            'size' => $size,
            'mime_type' => $mimeType,
        ]);
    }

    public function logAction($action, $performedBy)
    {
        return $this->auditLogs()->create([
            'action' => $action,
            'performed_by' => $performedBy,
        ]);
    }

    public function isArchived(): bool
    {
        return !is_null($this->archived_at);
    } 
     public function modelType()
{
    return $this->belongsTo(ModelType::class);
}

}
