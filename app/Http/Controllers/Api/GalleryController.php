<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GalleryService;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    protected $galleryService;

    public function __construct(GalleryService $galleryService)
    {
        $this->galleryService = $galleryService;
    }

    public function index()
    {
        $images = $this->galleryService->getAllImages();
        return response()->json($images, 200);
    }

    public function active()
    {
        $images = $this->galleryService->getActiveImages();
        return response()->json($images, 200);
    }

    public function carousel()
    {
        $images = $this->galleryService->getCarouselImages();
        return response()->json($images, 200);
    }

    public function show($id)
    {
        $image = $this->galleryService->getImageById($id);
        return response()->json($image, 200);
    }

    public function store(Request $request)
    {
        $image = $this->galleryService->createImage($request);

        return response()->json([
            'message' => 'Gallery image successfully created',
            'data' => $image
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $image = $this->galleryService->updateImage($request, $id);

        return response()->json([
            'message' => 'Gallery image successfully updated',
            'data' => $image
        ], 200);
    }

    public function destroy($id)
    {
        $result = $this->galleryService->deleteImage($id);

        return response()->json([
            'message' => $result['message']
        ], 200);
    }

    public function updateOrder(Request $request)
    {
        $result = $this->galleryService->updateOrder($request);

        return response()->json([
            'message' => $result['message']
        ], 200);
    }
}
