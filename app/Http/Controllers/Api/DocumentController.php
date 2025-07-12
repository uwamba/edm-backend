<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Http\Resources\DocumentResource;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function index()
    {
        return DocumentResource::collection(Document::with(['user', 'versions', 'tags'])->paginate(10));
    }

    public function show($id)
    {
        $document = Document::with(['user', 'versions', 'tags', 'auditLogs'])->findOrFail($id);
        return new DocumentResource($document);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'path' => 'required|string',
            'size' => 'required|integer',
            'mime_type' => 'required|string',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'user_id' => 'required|exists:users,id'
        ]);

        $document = Document::create($validated);
        if (isset($validated['tags'])) {
            $document->tags()->sync($validated['tags']);
        }

        return response()->json(new DocumentResource($document), 201);
    }

    public function update(Request $request, $id)
    {
        $document = Document::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id'
        ]);

        $document->update($validated);

        if (isset($validated['tags'])) {
            $document->tags()->sync($validated['tags']);
        }

        return response()->json(new DocumentResource($document));
    }

    public function destroy($id)
    {
        $document = Document::findOrFail($id);
        $document->delete();

        return response()->json(null, 204);
    }
}
