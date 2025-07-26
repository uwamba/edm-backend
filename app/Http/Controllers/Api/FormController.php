<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Form;
use App\Models\Field;
use Illuminate\Support\Facades\Validator;
   use Illuminate\Support\Str;
class FormController extends Controller
{
    /**
     * Display a listing of all forms.
     */
    public function index()
    {
        $forms = Form::with('fields')->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $forms
        ], 200);
    }

    /**
     * Store a newly created form with dynamic fields.
     */
 

public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'fields' => 'required|array',
        'fields.*.label' => 'required|string|max:255',
        'fields.*.type' => 'required|string',
        'fields.*.options' => 'nullable|array',
        'fields.*.required' => 'required|boolean',
        'fields.*.validations' => 'nullable|array',
        'fields.*.conditions' => 'nullable|array',
        'fields.*.parentField' => 'nullable|string|max:255',
        'fields.*.parentMapping' => 'nullable|array',
        'fields.*.temp_id' => 'required|string', // Add this on frontend
        'fields.*.parent_temp_id' => 'nullable|string', // Add this on frontend
    ]);

    // 1. Create the form
    $form = Form::create([
        'title' => $validated['title'],
        'description' => $validated['description'] ?? null,
        'created_by' => auth()->id() ?? 1,
    ]);

    $tempMap = []; // temp_id => real Field model

    // 2. First pass - insert all fields without parent_field_id
    foreach ($validated['fields'] as $field) {
        $fieldData = [
            'form_id' => $form->id,
            'label' => $field['label'],
            'type' => $field['type'],
            'options' => $field['options'] ?? [],
            'required' => $field['required'],
            'validations' => $field['validations'] ?? [],
            'conditions' => $field['conditions'] ?? [],
            'parentField' => $field['parentField'] ?? null,
            'parentMapping' => $field['parentMapping'] ?? [],
        ];

        $newField = Field::create($fieldData);

        $tempMap[$field['temp_id']] = $newField;
    }

    // 3. Second pass - update parent_field_id using parent_temp_id
    foreach ($validated['fields'] as $field) {
        if (!empty($field['parent_temp_id']) && isset($tempMap[$field['temp_id']])) {
            $childField = $tempMap[$field['temp_id']];
            $parent = $tempMap[$field['parent_temp_id']] ?? null;

            if ($parent) {
                $childField->update([
                    'parent_field_id' => $parent->id,
                ]);
            }
        }
    }

    return response()->json([
        'message' => 'Form created successfully',
        'form' => $form->load('fields'),
    ], 201);
}



    /**
     * Display the specified form with its fields.
     */
    public function show($id)
{
    // Fetch the form
    $form = Form::find($id);

    if (!$form) {
        return response()->json([
            'success' => false,
            'message' => 'Form not found.'
        ], 404);
    }

    // Get all fields for the form
    $allFields = Field::where('form_id', $form->id)->get();

    // Separate into parent and children
    $parents = $allFields->whereNull('parent_field_id')->values();
    $childrenGrouped = $allFields->whereNotNull('parent_field_id')->groupBy('parent_field_id');

    // Attach children manually
    $parents->transform(function ($parent) use ($childrenGrouped) {
        $parent->children = $childrenGrouped->get($parent->id, collect())->values();
        return $parent;
    });

    // Add the parent fields to the form
    $form->setRelation('fields', $parents);

    return response()->json([
        'success' => true,
        'data' => $form
    ]);
}



    /**
     * Update the specified form and its fields.
     */
    public function update(Request $request, $id)
    {
        $form = Form::find($id);

        if (!$form) {
            return response()->json([
                'success' => false,
                'message' => 'Form not found.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'fields' => 'required|array|min:1',
            'fields.*.label' => 'required|string|max:255',
            'fields.*.type' => 'required|string|in:text,number,select,checkbox,date,file,radio', // added 'radio' type
            'fields.*.options' => 'nullable|array',
            'fields.*.required' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Update form
        $form->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        // Delete old fields
        $form->fields()->delete();

        // Save updated fields
        foreach ($request->fields as $field) {
            $form->fields()->create([
                'label' => $field['label'],
                'type' => $field['type'],
                'options' => $field['options'] ?? [],
                'required' => $field['required'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Form updated successfully.',
            'data' => $form->load('fields')
        ], 200);
    }

    /**
     * Remove the specified form and its fields from storage.
     */
    public function destroy($id)
    {
        $form = Form::find($id);

        if (!$form) {
            return response()->json([
                'success' => false,
                'message' => 'Form not found.'
            ], 404);
        }

        $form->fields()->delete();
        $form->delete();

        return response()->json([
            'success' => true,
            'message' => 'Form deleted successfully.'
        ], 200);
    }
}
