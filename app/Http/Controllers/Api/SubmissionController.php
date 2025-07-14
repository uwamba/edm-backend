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



    public function submit(Request $request)
    {
        Log::info('Submit called', ['request_all' => $request->all()]);

        $formId = $request->input('form_id');
        Log::info('Form ID from request', ['form_id' => $formId]);

        $form = Form::with('fields')->findOrFail($formId);

        Log::info('Form found', ['form_id' => $form->id, 'data' => $form->fields]);

        $rules = [];

        $validated = $request->validate([
            'data' => ['required', 'array'],
        ]);

        $data = $validated['data'];

        // Now validate each field inside data
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

        // Re-validate with field rules
        $validated = $request->validate($rules);
        $data = $validated['data']; // Extract only the field data


        foreach ($form->fields as $field) {
            $key = 'field_' . $field->id;
            if ($field->type === 'file' && $request->hasFile($key)) {
                $uploadedFile = $request->file($key);
                $path = $uploadedFile->store('uploads/forms');
                $validated[$key] = $path;
            }
        }

        $submission = $form->submissions()->create([
            'user_id' => 1,
        ]);

        Log::info('validation', ['data' => $data]);

        foreach ($data as $key => $value) {
            SubmissionField::create([
                'submission_id' => $submission->id,
                'field_id' => (int) str_replace('field_', '', $key),
                'value' => is_array($value) ? json_encode($value) : $value,
            ]);

        }


        return response()->json(['message' => 'Submission successful']);
    }






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
