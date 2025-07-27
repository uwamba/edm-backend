<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\JobTitle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index()
    {
       $users = User::with(['company', 'manager', 'jobTitle'])->get();

        Log::info('Fetched users', $users->toArray());

        return response()->json($users);
    }

    // Get distinct available job titles
    public function availableJobTitles()
    {
        $jobTitles = JobTitle::select('id', 'name')->get();

        return response()->json([
            'job_titles' => $jobTitles,
        ]);
    }

    public function store(Request $request)
    {
        $validated=$request->validate([
            'name'          => 'required|string',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|string|min:8',
            'job_title_id'  => 'required|exists:job_titles,id',
            'company_id'    => 'nullable|exists:companies,id',
            'manager_id'    => 'nullable|exists:users,id',
        ]);

        Log::info('Creating user with data', $request->all());

        $user = User::create($validated);

        return response()->json($user, 201);
    }

    public function show($id)
    {
        $user = User::with(['company', 'manager', 'jobTitle'])->findOrFail($id);
        return response()->json($user);
    }

    public function update(Request $request, $id)
{
    $validated = $request->validate([
        'name' => 'required|string',
        'email' => 'required|email',
        'password' => 'nullable|string|min:6',
        'job_title_id' => 'nullable|exists:job_titles,id',
        'company_id' => 'nullable|exists:companies,id',
        'manager_id' => 'nullable|exists:users,id',
    ]);

    $user = User::findOrFail($id);
    $user->name = $validated['name'];
    $user->email = $validated['email'];

    if (!empty($validated['password'])) {
        $user->password = bcrypt($validated['password']);
    }

    $user->job_title_id = $validated['job_title_id'] ?? null;
    $user->company_id = $validated['company_id'] ?? null;
    $user->manager_id = $validated['manager_id'] ?? null;

    $user->save();

    return response()->json($user);
}


    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return response()->noContent();
    }
}
