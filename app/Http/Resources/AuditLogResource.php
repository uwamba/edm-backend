<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AuditLogResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'action'      => $this->action,
            'ip_address'  => $this->ip_address,
            'user_agent'  => $this->user_agent,
            'created_at'  => $this->created_at,

            'user' => $this->whenLoaded('user', function () {
                return [
                    'id'    => $this->user->id,
                    'name'  => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),

            'document' => $this->whenLoaded('document', function () {
                return [
                    'id'   => $this->document->id,
                    'name' => $this->document->name,
                ];
            }),
        ];
    }
}
