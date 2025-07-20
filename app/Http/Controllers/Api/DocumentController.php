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
        return DocumentResource::collection(
            Document::with(['user', 'versions', 'tags'])->paginate(10)
        );
    }

    public function show($id)
    {
        $document = Document::with([
            'user',
            'versions',
            'auditLogs' // tags removed here
        ])->find($id);
    
        if (!$document) {
            return response()->json(['message' => 'Document not found'], 404);
        }
    
        return new DocumentResource($document);
    }
    
    
    
    

    public function store(Request $request)
    {
        $request->validate([
            'document' => 'required|file|max:20480',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'tags' => 'nullable|string', // JSON array
            'user_id' => 'required|exists:users,id',
        ]);
    
        $year = now()->year;
        $folder = "documents/{$year}";
    
        // Laravel automatically reuses the folder — no need to re-create if exists
        $path = $request->file('document')->store($folder, 'public');
    
        // Avoid duplicate document record for same file name and user
        $existing = Document::where('name', $request->name)
            ->where('user_id', $request->user_id)
            ->first();
    
        if ($existing) {
            return response()->json([
                'message' => 'Document with this name already exists for this user.',
            ], 409); // Conflict
        }
    
        // Create a new record for this upload
        $document = Document::create([
            'name' => $request->name,
            'description' => $request->description,
            'path' => $path,
            'size' => $request->file('document')->getSize(),
            'mime_type' => $request->file('document')->getMimeType(),
            'user_id' => $request->user_id,
        ]);
    
        // Attach tags if provided
        if ($request->filled('tags')) {
            $tagIds = json_decode($request->tags, true);
            if (is_array($tagIds)) {
                $document->tags()->sync($tagIds);
            }
        }
    
        return response()->json([
            'message' => 'Document uploaded successfully.',
            'data' => $document,
        ]);
    }

    public function update(Request $request, $id)
    {
        $document = Document::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'tags' => 'nullable|string', // JSON string from frontend
        ]);

        $document->update([
            'name' => $validated['name'] ?? $document->name,
            'description' => $validated['description'] ?? $document->description,
        ]);

        if (isset($validated['tags'])) {
            $tagIds = json_decode($validated['tags'], true);
            if (is_array($tagIds)) {
                $document->tags()->sync($tagIds);
            }
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
