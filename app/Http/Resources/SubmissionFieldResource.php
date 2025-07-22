<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubmissionFieldResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'value' => $this->value,
            'parent_field_id' => $this->parent_field_id,  // Add this line
            'field' => [
                'id' => $this->field->id,
                'name' => $this->field->name,
                'label' => $this->field->label,
            ],
        ];
    }
}
