<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ApprovalStepResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'step_number' => $this->step_number,
            'approver_id' => $this->approver_id,
            'status' => $this->status,
            'approver' => $this->whenLoaded('approver', function () {
                return [
                    'id' => $this->approver->id,
                    'name' => $this->approver->name,
                    'email' => $this->approver->email,
                ];
            }),
        ];
    }
}
