<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FormResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'created_by' => $this->created_by,
            'creator' => $this->whenLoaded('creator', function () {
                return [
                    'id' => $this->creator->id,
                    'name' => $this->creator->name,
                    'email' => $this->creator->email,
                ];
            }),
            'fields' => FieldResource::collection($this->whenLoaded('fields')),
            'approval_process' => new ApprovalProcessResource($this->whenLoaded('approvalProcess')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
