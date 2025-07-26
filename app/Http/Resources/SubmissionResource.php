<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubmissionResource extends JsonResource
{
    public function toArray($request)
    {
        $grouped = [];

        foreach ($this->fields as $fieldSubmission) {
            $field = $fieldSubmission->field;

            // Child field
            if ($field->parent_field_id) {
                $parentId = $field->parent_field_id;
                $grouped[$parentId]['children'][] = [
                    'label' => $field->label,
                    'name' => $field->name,
                    'value' => $fieldSubmission->value,
                    'type' => $field->type,
                    'options' => is_string($field->options) ? json_decode($field->options, true) : ($field->options ?? []),

                ];
            } else {
                // Parent field
                $grouped[$field->id] ??= [
                    'id' => $fieldSubmission->id,
                    'value' => $fieldSubmission->value,
                    'field' => [
                        'id' => $field->id,
                        'label' => $field->label,
                        'name' => $field->name,
                        'type' => $field->type,
                        'options' => is_string($field->options) ? json_decode($field->options, true) : ($field->options ?? []),

                    ],
                    'children' => [],
                ];
            }
        }

        return [
            'id' => $this->id,
            'user' => new UserResource($this->user),
            'form' => new FormResource($this->form),
            'fields' => array_values($grouped),
            'created_at' => $this->created_at,
        ];
    }
}
