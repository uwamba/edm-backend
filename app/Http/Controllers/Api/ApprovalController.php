<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Workflow;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{

    public function show($formId)
    {
        $process = \App\Models\ApprovalProcess::with(['steps.approver'])
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
            'steps.*.approver_id' => 'required|exists:users,id',
        ]);

        $process = \App\Models\ApprovalProcess::create([
            'form_id' => $validated['form_id'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        foreach ($validated['steps'] as $step) {
            $process->steps()->create([
                'step_number' => $step['step_number'],
                'approver_id' => $step['approver_id'],
                'status' => 'pending',
            ]);
        }

        return response()->json([
            'message' => 'Approval process created successfully',
            'id' => $process->id,
        ]);
    }


    /**
     * Approve the current workflow step.
     */
    public function approve(Request $request, Workflow $workflow)
    {
        $user = $request->user();
        $step = $workflow->currentStep;

        // Check if the current user is authorized to approve
        if (!$step || $step->approver_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized or no current step'], 403);
        }

        // Update step status
        $step->update([
            'status' => 'approved',
            'comment' => $request->input('comment', null), // Optional comment
        ]);

        // Find the next step in the approval process
        $nextStep = $workflow->submission->form->approvalProcess
            ->steps()
            ->where('step_number', '>', $step->step_number)
            ->orderBy('step_number')
            ->first();

        if ($nextStep) {
            // Move workflow to next step
            $workflow->update([
                'current_step_id' => $nextStep->id,
            ]);

            return response()->json([
                'message' => 'Step approved. Workflow moved to next step.',
                'next_step' => $nextStep->id,
            ]);
        } else {
            // All steps approved, mark workflow as complete
            $workflow->update([
                'status' => 'approved',
                'current_step_id' => null,
            ]);

            return response()->json([
                'message' => 'Workflow fully approved.',
            ]);
        }
    }

    /**
     * Reject the current workflow step.
     */
    public function reject(Request $request, Workflow $workflow)
    {
        $user = $request->user();
        $step = $workflow->currentStep;

        // Check if the current user is authorized to reject
        if (!$step || $step->approver_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized or no current step'], 403);
        }

        // Update step status to rejected
        $step->update([
            'status' => 'rejected',
            'comment' => $request->input('comment', null), // Optional comment
        ]);

        // Mark workflow as rejected and stop further steps
        $workflow->update([
            'status' => 'rejected',
            'current_step_id' => null,
        ]);

        return response()->json([
            'message' => 'Workflow rejected and stopped.',
        ]);
    }
}
