<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    // Fetch all tags (id and name)
    public function index()
    {
        $tags = Tag::select('id', 'name')->orderBy('name')->get();
        return response()->json($tags);
    }

    // Create a new tag with name and optional value
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tags,name',
            'value' => 'nullable|string|max:255',
        ]);

        $tag = Tag::create([
            'name' => $request->name,
            'value' => $request->value ?? null,
        ]);

        return response()->json($tag, 201);
    }
}
