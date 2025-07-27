<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CompanyResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'email'       => $this->email,
            'job_title'   => $this->jobTitle ? [
                'id'   => $this->jobTitle->id,
                'name' => $this->jobTitle->name,
            ] : null,
            'company_id'  => $this->company_id,
            'manager_id'  => $this->manager_id,
            'company'     => new CompanyResource($this->whenLoaded('company')),
            'manager'     => new UserResource($this->whenLoaded('manager')),
        
        ];
    }
}
