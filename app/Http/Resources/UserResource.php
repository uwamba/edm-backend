<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the user resource into an array.
     */
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'email'      => $this->email,
            'job_title'  => $this->job_title,
            'company_id' => $this->company_id,
            'manager_id' => $this->manager_id,

            // Optional: include manager details if loaded
            'manager'    => new UserResource($this->whenLoaded('manager')),
        ];
    }
}
