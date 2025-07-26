<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubmissionResource;
use App\Models\ApprovalProcess;
use App\Models\Submission;
use Illuminate\Http\Request;
use App\Models\Form;
use App\Models\SubmissionField;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Log;

class SubmissionController extends Controller
{
   public function index()
{
    $submissions = Submission::with([
        'user',
        'form.creator',
        'fields.field.parent', // for parent info
        'approvalProcess.steps.approver'
    ])->get();

    return SubmissionResource::collection($submissions);
}




   // You are trying to get form_id from request body, but it's coming from the URL.
// Fix: get it directly from route parameter


public function submissions(Request $request, $formId)
{
    $form = Form::with('fields')->findOrFail($formId);

    $validated = $request->validate([
        'data' => 'required|array',
        'user_id' => 'required|integer|exists:users,id',
    ]);

    $dataInputs = $validated['data'];
    $userId = $validated['user_id'];

    // Create submission WITHOUT 'data' column
    $submission = Submission::create([
        'form_id' => $form->id,
        'user_id' => $userId,
    ]);

    foreach ($dataInputs as $key => $value) {
        if (preg_match('/^field_(\d+)/', $key, $matches)) {
            $fieldId = intval($matches[1]);

            $uploadedFiles = $request->file("data.$key");

            if ($uploadedFiles) {
                if (is_array($uploadedFiles)) {
                    $storedPaths = [];
                    foreach ($uploadedFiles as $file) {
                        $storedPaths[] = $file->store('submissions_files');
                    }
                    $fieldValue = json_encode($storedPaths);
                } else {
                    $fieldValue = $uploadedFiles->store('submissions_files');
                }
            } else {
                $fieldValue = is_array($value) ? json_encode($value) : $value;
            }

            SubmissionField::create([
                'submission_id' => $submission->id,
                'field_id' => $fieldId,
                'value' => $fieldValue,
            ]);
        }
    }

    return response()->json([
        'message' => 'Submission saved successfully',
        'submission_id' => $submission->id,
    ], 201);
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
