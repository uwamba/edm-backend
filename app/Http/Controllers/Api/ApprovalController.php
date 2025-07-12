<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Workflow;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function approve(Request $request, Workflow $workflow)
    {
        $user = $request->user();
        $step = $workflow->currentStep;

        if (!$step || $step->approver_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized or no current step'], 403);
        }

        $step->update(['status' => 'approved']);

        // Move to next step
        $nextStep = $workflow->submission->form->approvalProcess
            ->steps()
            ->where('step_number', '>', $step->step_number)
            ->orderBy('step_number')
            ->first();

        if ($nextStep) {
            $workflow->update(['current_step_id' => $nextStep->id]);
        } else {
            $workflow->update(['status' => 'approved', 'current_step_id' => null]);
        }

        return response()->json(['message' => 'Step approved']);
    }

    public function reject(Request $request, Workflow $workflow)
    {
        $user = $request->user();
        $step = $workflow->currentStep;

        if (!$step || $step->approver_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized or no current step'], 403);
        }

        $step->update(['status' => 'rejected']);
        $workflow->update(['status' => 'rejected', 'current_step_id' => null]);

        return response()->json(['message' => 'Workflow rejected']);
    }
}
