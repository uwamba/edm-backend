<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ApprovalProcessResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'form_id' => $this->form_id,
            'name' => $this->name,
            'description' => $this->description,
            'steps' => ApprovalStepResource::collection($this->whenLoaded('steps')),
        ];
    }
}
