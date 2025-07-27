<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ApprovalStep;
use App\Models\ApprovalProcess;
use Illuminate\Support\Facades\Auth;
class ApprovalController extends Controller
{

    public function show($formId)
    {
        $process = ApprovalProcess::with(['steps.jobTitle'])
            ->where('form_id', $formId)
            ->first();

        if (!$process) {
            return response()->json(['error' => 'Approval process not found'], 404);
        }

        return response()->json($process);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'form_id' => 'required|exists:forms,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'steps' => 'required|array|min:1',
            'steps.*.step_number' => 'required|integer|min:1',
            'steps.*.job_title_id' => 'required|exists:job_titles,id',
        ]);

        $process = ApprovalProcess::create([
            'form_id' => $validated['form_id'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        foreach ($validated['steps'] as $step) {
            $process->steps()->create([
                'step_number' => $step['step_number'],
                'job_title_id' => $step['job_title_id'],
                'status' => 'pending',
            ]);
        }

        return response()->json(['message' => 'Approval process created successfully.'], 201);
    }






public function approve(Request $request)
{
    $user = Auth::user();
    if (!$user) {
    return response()->json(['error' => 'Unauthenticated.'], 401);
    }


    $approvalStepId = $request->input('approval_step_id');
    $approvalStep = ApprovalStep::find($approvalStepId);

    if (!$approvalStep) {
        return response()->json(['error' => 'Approval step not found'], 404);
    }

    $approvalProcess = $approvalStep->approvalProcess;

    if (!$approvalProcess) {
        return response()->json(['error' => 'Approval process not found'], 404);
    }

    $precedingStepsNotApproved = $approvalProcess->steps()
        ->where('step_number', '<', $approvalStep->step_number)
        ->where('status', '!=', 'approved')
        ->exists();

    if ($precedingStepsNotApproved) {
        return response()->json(['error' => 'Cannot approve before previous steps are approved'], 403);
    }

    if ($approvalStep->job_title_id !== $user->job_title_id) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $approvalStep->update([
        'status' => 'approved',
        'comment' => $request->input('comment', null),
    ]);

    $pendingStepsRemain = $approvalProcess->steps()
        ->where('status', 'pending')
        ->exists();

    if (!$pendingStepsRemain) {
        $approvalProcess->update(['status' => 'approved']);
        return response()->json(['message' => 'Final step approved. Process completed.']);
    }

    return response()->json(['message' => 'Step approved successfully.']);
}


public function reject(Request $request, ApprovalStep $approvalStep)
{
    $user = $request->user();

    // Ensure user is allowed to reject
    if ($approvalStep->job_title_id !== $user->job_title_id) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    // Reject the step
    $approvalStep->update([
        'status' => 'rejected',
        'comment' => $request->input('comment', null),
    ]);

    // End the approval process
    $approvalProcess = $approvalStep->approvalProcess;
    $approvalProcess->update([
        'status' => 'rejected',
    ]);

    return response()->json(['message' => 'Step rejected. Approval process terminated.']);
}



}
