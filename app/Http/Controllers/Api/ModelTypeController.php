<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ModelType;
use Illuminate\Http\Request;

class ModelTypeController extends Controller
{
    public function index()
    {
        return response()->json(
            ModelType::select('id', 'name')->orderBy('name')->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:model_types,name',
        ]);

        $modelType = ModelType::create($validated);

        return response()->json($modelType, 201);
    }
}
