<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FieldResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'type' => $this->type,
            'options' => $this->options,
            'required' => $this->required,
            'validations' => $this->validations,
            'conditions' => $this->conditions,
            'parent_field_id' => $this->parent_field_id,
            'repeatable' => $this->repeatable ?? false,
            'children' => FieldResource::collection($this->whenLoaded('children')),
        ];
    }
}
