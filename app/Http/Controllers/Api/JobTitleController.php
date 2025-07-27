<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobTitle;
use Illuminate\Http\Request;

class JobTitleController extends Controller
{
    // List all job titles
    public function index()
    {
        $jobTitles = JobTitle::all();
        return response()->json($jobTitles);
    }

    // Store a new job title
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:job_titles,name',
        ]);

        $jobTitle = JobTitle::create($validated);

        return response()->json($jobTitle, 201);
    }

    // Show a single job title
    public function show($id)
    {
        $jobTitle = JobTitle::findOrFail($id);
        return response()->json($jobTitle);
    }

    // Update an existing job title
    public function update(Request $request, $id)
    {
        $jobTitle = JobTitle::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|unique:job_titles,name,' . $id,
        ]);

        $jobTitle->update($validated);

        return response()->json($jobTitle);
    }

    // Delete a job title
    public function destroy($id)
    {
        JobTitle::findOrFail($id)->delete();
        return response()->noContent();
    }
}
