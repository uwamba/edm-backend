<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubmissionResource;
use App\Models\ApprovalProcess;
use App\Models\Submission;
use Illuminate\Http\Request;
use App\Models\Form;

class SubmissionController extends Controller
{
    public function index()
    {
        $submissions = Submission::with(['form', 'user', 'workflow.currentStep.approver'])->paginate(10);
        return SubmissionResource::collection($submissions);
    }

    public function submit(Request $request, Form $form)
{
    $rules = [];
    foreach ($form->fields as $field) {
        $fieldRules = [];
        if ($field['required']) {
            $fieldRules[] = 'required';
        }
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
                    $fieldRules[] = "max:{$validation['value']}"; // size in KB
                    break;
                case 'fileType':
                    $fieldRules[] = "mimes:{$validation['value']}";
                    break;
            }
        }
        $rules[$field['label']] = implode('|', $fieldRules);
    }

    $validated = $request->validate($rules);

    $form->submissions()->create([
        'data' => $validated,
        'submitted_by' => auth()->id() ?? null,
    ]);

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

    public function show(Submission $submission)
    {
        return new SubmissionResource($submission->load(['form', 'user', 'workflow.currentStep.approver']));
    }

    public function destroy(Submission $submission)
    {
        $submission->delete();
        return response()->json(null, 204);
    }
}
