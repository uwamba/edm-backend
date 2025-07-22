<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubmissionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'form_id' => $this->form_id,
            'user_id' => $this->user_id,
            'data' => $this->data,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
    
            'user' => new UserResource($this->whenLoaded('user')),
            'form' => new FormResource($this->whenLoaded('form')),
            'fields' => SubmissionFieldResource::collection($this->whenLoaded('fields')),
    
            // approval process from form relation
            'workflow' => [
                'approval_process' => $this->whenLoaded('form', function () {
                    $approvalProcess = $this->form->approvalProcess;
    
                    if (!$approvalProcess) {
                        return null;
                    }
    
                    return [
                        'id' => $approvalProcess->id,
                        'name' => $approvalProcess->name,
                        'steps' => $approvalProcess->steps->map(function ($step) {
                            return [
                                'id' => $step->id,
                                'step_number' => $step->step_number,
                                'status' => $step->status,
                                'approver' => $step->approver ? [
                                    'id' => $step->approver->id,
                                    'name' => $step->approver->name,
                                    'email' => $step->approver->email,
                                ] : null,
                            ];
                        }),
                    ];
                }),
            ],
        ];
    }
    
}
