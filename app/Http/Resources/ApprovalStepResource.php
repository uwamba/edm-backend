<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ApprovalStepResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'step_number' => $this->step_number,
            'status'      => $this->status,

            'job_title' => $this->jobTitle ? [
                'id'   => $this->jobTitle->id,
                'name' => $this->jobTitle->name,
            ] : null,
        ];
    }
}
