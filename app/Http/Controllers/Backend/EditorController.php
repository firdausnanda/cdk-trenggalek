<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\ImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EditorController extends Controller
{
    public function __construct(private readonly ImageService $imageService)
    {
    }

    /**
     * Handle image uploads from the Editor
     */
    public function upload(Request $request): JsonResponse
    {
        abort_unless($request->user()?->canAny(['post.create', 'post.edit']), 403);

        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,webp,gif|max:10240',
        ]);

        // Return the URL to Editor.
        return response()->json([
            'location' => $this->imageService->store($request->file('file'), 'uploads/posts'),
        ]);
    }
}
