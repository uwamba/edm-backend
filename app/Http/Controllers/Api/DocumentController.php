<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Http\Resources\DocumentResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'file' => 'required|file|max:10240', // 10MB max
            'user_id' => 'required|exists:users,id',
            'tags' => 'nullable|string', // comma-separated
        ]);

        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }

        $file = $request->file('file');
        $year = now()->year;
        $path = $file->store("documents/{$year}", 'public');

        $document = Document::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'path' => $path,
            'size' => $file->getSize(),
            'mime_type' => $file->getClientMimeType(),
            'user_id' => $validated['user_id'],
        ]);

        // Process comma-separated tag IDs
        if (!empty($validated['tags'])) {
            $tagIds = array_map('intval', explode(',', $validated['tags']));
            $document->tags()->sync($tagIds);
        }

        return response()->json(new DocumentResource($document), 201);
    }

    public function update(Request $request, $id)
    {
        $document = Document::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'tags' => 'nullable|string', // comma-separated
        ]);

        $document->update([
            'name' => $validated['name'] ?? $document->name,
            'description' => $validated['description'] ?? $document->description,
        ]);

        if (isset($validated['tags'])) {
            $tagIds = array_map('intval', explode(',', $validated['tags']));
            $document->tags()->sync($tagIds);
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
