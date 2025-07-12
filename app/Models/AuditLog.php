<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'document_id',
        'action',
        'ip_address',
        'user_agent'
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}
