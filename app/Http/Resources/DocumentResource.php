<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description,
            'path'        => $this->path,
            'size'        => $this->size,
            'mime_type'   => $this->mime_type,
            'archived_at' => $this->archived_at,
            'created_at'  => $this->created_at,
            'updated_at'  => $this->updated_at,

            'user' => $this->whenLoaded('user', function () {
                return [
                    'id'    => $this->user->id,
                    'name'  => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),

            'tags' => TagResource::collection($this->whenLoaded('tags')),

            'versions' => DocumentVersionResource::collection(
                $this->whenLoaded('versions')
            ),

            'audit_logs' => AuditLogResource::collection(
                $this->whenLoaded('auditLogs')
            ),
        ];
    }
}
