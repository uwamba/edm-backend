<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WorkflowResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'submission_id' => $this->submission_id,
            'current_step_id' => $this->current_step_id,
            'status' => $this->status,
            'current_step' => new ApprovalStepResource($this->whenLoaded('currentStep')),
        ];
    }
}
