<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DocumentVersionResource;
use App\Models\DocumentVersion;
use Illuminate\Http\Request;

class DocumentVersionController extends Controller
{
    public function index()
    {
        $versions = DocumentVersion::with('document')->paginate(10);
        return DocumentVersionResource::collection($versions);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'document_id' => 'required|exists:documents,id',
            'version' => 'required|string|max:50',
            'path' => 'required|string',
            'size' => 'required|integer',
            'mime_type' => 'required|string'
        ]);

        $version = DocumentVersion::create($validated);

        return new DocumentVersionResource($version);
    }

    public function show(DocumentVersion $documentVersion)
    {
        $documentVersion->load('document');
        return new DocumentVersionResource($documentVersion);
    }

    public function update(Request $request, DocumentVersion $documentVersion)
    {
        $validated = $request->validate([
            'version' => 'sometimes|required|string|max:50',
            'path' => 'sometimes|required|string',
            'size' => 'sometimes|required|integer',
            'mime_type' => 'sometimes|required|string'
        ]);

        $documentVersion->update($validated);

        return new DocumentVersionResource($documentVersion);
    }

    public function destroy(DocumentVersion $documentVersion)
    {
        $documentVersion->delete();
        return response()->json(null, 204);
    }
}
