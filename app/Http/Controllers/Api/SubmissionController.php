<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubmissionResource;
use App\Models\ApprovalProcess;
use App\Models\Submission;
use Illuminate\Http\Request;
use App\Models\Form;
use App\Models\SubmissionField;


use Illuminate\Support\Facades\Log;

class SubmissionController extends Controller
{
    public function index()
    {
        $submissions = Submission::with(['form', 'user', 'workflow.currentStep.approver'])->paginate(10);
        return SubmissionResource::collection($submissions);
    }



   // You are trying to get form_id from request body, but it's coming from the URL.
// Fix: get it directly from route parameter

public function submissions(Request $request, $form_id)
{
    \Log::info('Submit called', ['request_all' => $request->all()]);

    // form_id comes from the URL now
    \Log::info('Form ID from route param', ['form_id' => $form_id]);

    $form = Form::with('fields')->findOrFail($form_id);

    \Log::info('Form found', ['form_id' => $form->id, 'data' => $form->fields]);

    $rules = [];

    $validated = $request->validate([
        'data' => ['required', 'array'],
    ]);

    $data = $validated['data'];

    foreach ($form->fields as $field) {
        $key = 'field_' . $field->id;

        $fieldRules = $field['required'] ? ['required'] : ['nullable'];

        foreach ($field['validations'] ?? [] as $validation) {
            switch ($validation['type']) {
                case 'min':
                    $fieldRules[] = "min:{$validation['value']}";
                    break;
                case 'max':
                    $fieldRules[] = "max:{$validation['value']}";
                    break;
                case 'regex':
                    $fieldRules[] = "regex:{$validation['value']}";
                    break;
                case 'fileSize':
                    $fieldRules[] = "file|max:{$validation['value']}";
                    break;
                case 'fileType':
                    $fieldRules[] = "file|mimes:{$validation['value']}";
                    break;
            }
        }

        $rules["data.$key"] = $fieldRules;
    }

    $validated = $request->validate($rules);
    $data = $validated['data'];

    foreach ($form->fields as $field) {
        $key = 'field_' . $field->id;
        if ($field->type === 'file' && $request->hasFile("data.$key")) {
            $uploadedFile = $request->file("data.$key");
            $path = $uploadedFile->store('uploads/forms');
            $data[$key] = $path;
        }
    }

    $submission = $form->submissions()->create([
        'user_id' => 1,
    ]);

    \Log::info('validation', ['data' => $data]);

    foreach ($data as $key => $value) {
        SubmissionField::create([
            'submission_id' => $submission->id,
            'field_id' => (int) str_replace('field_', '', $key),
            'value' => is_array($value) ? json_encode($value) : $value,
        ]);
    }

    return response()->json(['message' => 'Submission successful']);
}

// Route should match







    public function store(Request $request)
    {
        $validated = $request->validate([
            'form_id' => 'required|exists:forms,id',
            'data' => 'required|array',
        ]);

        $submission = Submission::create([
            'form_id' => $validated['form_id'],
            'user_id' => $request->user()->id,
            'data' => $validated['data'],
        ]);

        $approvalProcess = ApprovalProcess::where('form_id', $validated['form_id'])->first();

        if ($approvalProcess && $approvalProcess->steps()->count() > 0) {
            $firstStep = $approvalProcess->steps()->orderBy('step_number')->first();

            $submission->workflow()->create([
                'current_step_id' => $firstStep->id,
                'status' => 'pending',
            ]);
        }

        return new SubmissionResource($submission->load(['form', 'user', 'workflow.currentStep.approver']));
    }

    public function show($id)
    {
        $submission = Submission::find($id);

        if (!$submission) {
            return response()->json(['message' => 'Submission not found'], 404);
        }

        return response()->json($submission);
    }

    public function destroy(Submission $submission)
    {
        $submission->delete();
        return response()->json(null, 204);
    }
}
