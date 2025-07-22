<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Form;
use App\Models\Field;
use Illuminate\Support\Facades\Validator;

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
            'fields.*.parent_field_id' => 'nullable|integer',
        ]);
    
        // Create the form
        $form = Form::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'created_by' => auth()->id() ?? 1,
        ]);
    
        // Insert each field linked to this form
        foreach ($validated['fields'] as $field) {
            Field::create([
                'form_id' => $form->id,
                'label' => $field['label'],
                'type' => $field['type'],
                'options' => $field['options'] ?? [],
                'required' => $field['required'],
                'validations' => $field['validations'] ?? [],
                'conditions' => $field['conditions'] ?? [],
                'parentField' => $field['parentField'] ?? null,
                'parentMapping' => $field['parentMapping'] ?? [],
                'parent_field_id' => $field['parent_field_id'] ?? null,
            ]);
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
        $form = Form::with('fields')->find($id);

        if (!$form) {
            return response()->json([
                'success' => false,
                'message' => 'Form not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $form
        ], 200);
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
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'fields'      => 'required|array|min:1',
            'fields.*.label'    => 'required|string|max:255',
            'fields.*.type'     => 'required|string|in:text,number,select,checkbox,date,file,radio', // added 'radio' type
            'fields.*.options'  => 'nullable|array',
            'fields.*.required' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        // Update form
        $form->update([
            'title'       => $request->title,
            'description' => $request->description,
        ]);

        // Delete old fields
        $form->fields()->delete();

        // Save updated fields
        foreach ($request->fields as $field) {
            $form->fields()->create([
                'label'    => $field['label'],
                'type'     => $field['type'],
                'options'  => $field['options'] ?? [],
                'required' => $field['required'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Form updated successfully.',
            'data'    => $form->load('fields')
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
