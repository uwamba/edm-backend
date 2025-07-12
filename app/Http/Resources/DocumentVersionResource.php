<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DocumentVersionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'        => $this->id,
            'version'   => $this->version,
            'path'      => $this->path,
            'size'      => $this->size,
            'mime_type' => $this->mime_type,
            'created_at'=> $this->created_at,

            'document' => $this->whenLoaded('document', function () {
                return [
                    'id'   => $this->document->id,
                    'name' => $this->document->name,
                ];
            }),
        ];
    }
}
