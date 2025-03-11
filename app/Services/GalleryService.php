<?php

namespace App\Services;

use App\Models\GalleryImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class GalleryService
{
    public function getAllImages()
    {
        return GalleryImage::orderBy('order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getActiveImages()
    {
        return GalleryImage::where('is_active', true)
            ->orderBy('order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getCarouselImages()
    {
        return GalleryImage::where('is_active', true)
            ->where('is_carousel', true)
            ->orderBy('order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getImageById($id)
    {
        return GalleryImage::findOrFail($id);
    }

    public function createImage(Request $request)
    {
        try {
            // Debug incoming request
            Log::info('Creating gallery image with data:', $request->all());

            $imageData = $this->validateAndPrepareData($request);

            // Log after validation
            Log::info('Validated gallery image data:', $imageData);

            // Upload image if provided
            if ($request->hasFile('image')) {
                $image = $request->file('image');

                // Log image information
                Log::info('Image details:', [
                    'name' => $image->getClientOriginalName(),
                    'extension' => $image->getClientOriginalExtension(),
                    'mime' => $image->getMimeType(),
                    'size' => $image->getSize()
                ]);

                // Check if this is a "problematic" image (you can add specific conditions)
                $isProblem = false;

                // If image is large and has transparency, mark as potentially problematic
                if ($image->getSize() > 1000000 &&
                    ($image->getMimeType() == 'image/png' ||
                        strtolower($image->getClientOriginalExtension()) == 'png')) {
                    $isProblem = true;
                    Log::info('Potentially problematic image detected, using direct GD handling');
                }

                if ($isProblem) {
                    // Direct GD handling for problematic images
                    try {
                        // Generate filename
                        $filename = Str::random(20) . '_' . time();

                        // Load with GD
                        $sourceImage = imagecreatefrompng($image->getRealPath());

                        if (!$sourceImage) {
                            throw new \Exception("Failed to create image resource from PNG");
                        }

                        // Get dimensions
                        $width = imagesx($sourceImage);
                        $height = imagesy($sourceImage);

                        // Resize if needed
                        if ($width > 1200) {
                            $newWidth = 1200;
                            $newHeight = intval($height * ($newWidth / $width));

                            $newImage = imagecreatetruecolor($newWidth, $newHeight);

                            // Preserve transparency
                            imagealphablending($newImage, false);
                            imagesavealpha($newImage, true);
                            $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
                            imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);

                            // Resize
                            imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                            // Replace source with resized image
                            imagedestroy($sourceImage);
                            $sourceImage = $newImage;

                            $width = $newWidth;
                            $height = $newHeight;
                        }

                        // Create directory if needed
                        $directory = storage_path('app/public/gallery');
                        if (!file_exists($directory)) {
                            mkdir($directory, 0755, true);
                        }

                        // Save as JPG
                        $jpgPath = 'gallery/' . $filename . '.jpg';
                        $jpgFullPath = storage_path('app/public/' . $jpgPath);

                        // Create white background image (JPG doesn't support transparency)
                        $jpgImage = imagecreatetruecolor($width, $height);
                        $white = imagecolorallocate($jpgImage, 255, 255, 255);
                        imagefilledrectangle($jpgImage, 0, 0, $width, $height, $white);

                        // Copy PNG onto white background
                        imagecopy($jpgImage, $sourceImage, 0, 0, 0, 0, $width, $height);

                        // Save as JPG
                        imagejpeg($jpgImage, $jpgFullPath, 85);
                        imagedestroy($jpgImage);

                        // Try to save as WebP too if GD supports it
                        if (function_exists('imagewebp')) {
                            $webpPath = 'gallery/' . $filename . '.webp';
                            $webpFullPath = storage_path('app/public/' . $webpPath);

                            // Create WebP image
                            $webpImage = imagecreatetruecolor($width, $height);
                            $white = imagecolorallocate($webpImage, 255, 255, 255);
                            imagefilledrectangle($webpImage, 0, 0, $width, $height, $white);

                            // Copy PNG onto white background
                            imagecopy($webpImage, $sourceImage, 0, 0, 0, 0, $width, $height);

                            // Save as WebP
                            imagewebp($webpImage, $webpFullPath, 85);
                            imagedestroy($webpImage);

                            // Store WebP path as primary if supported
                            $imageData['image_url'] = '/storage/' . $webpPath;
                            Log::info('WebP image saved to: ' . $webpPath);
                        } else {
                            // Store the JPG path in the database if WebP not supported
                            $imageData['image_url'] = '/storage/' . $jpgPath;
                        }

                        // Clean up
                        imagedestroy($sourceImage);

                        Log::info('Problematic image processed and saved to: ' . $jpgPath);

                    } catch (\Exception $e) {
                        Log::error('Error processing problematic image with GD: ' . $e->getMessage(), [
                            'exception' => $e,
                            'trace' => $e->getTraceAsString()
                        ]);

                        // If GD processing fails, try simple file storage
                        $path = $image->store('gallery', 'public');
                        $imageData['image_url'] = '/storage/' . $path;
                    }
                } else {
                    // Normal image handling with direct storage
                    $path = $image->store('gallery', 'public');
                    $imageData['image_url'] = '/storage/' . $path;
                    Log::info('Standard image upload to: ' . $path);
                }
            }

            // Set a default order if not provided
            if (!isset($imageData['order'])) {
                $maxOrder = GalleryImage::max('order');
                $imageData['order'] = ($maxOrder ?? 0) + 1;
            }

            // Set default active status if not provided
            if (!isset($imageData['is_active'])) {
                $imageData['is_active'] = true;
            }

            $galleryImage = GalleryImage::create($imageData);
            Log::info('Gallery image created with ID: ' . ($galleryImage->id ?? 'none'));

            return $galleryImage;
        } catch (\Exception $e) {
            // Log the error with detailed information
            Log::error('Gallery image creation error: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            // Return a clear error response
            throw new \Exception('Error creating gallery image: ' . $e->getMessage());
        }
    }

    public function updateImage(Request $request, $id)
    {
        try {
            $galleryImage = GalleryImage::findOrFail($id);
            $imageData = $this->validateAndPrepareData($request);

            // Upload new image if provided
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($galleryImage->image_url) {
                    $oldPath = str_replace('/storage/', '', $galleryImage->image_url);
                    Storage::disk('public')->delete($oldPath);

                    // Also delete any fallback images
                    $oldPathWithoutExt = pathinfo($oldPath, PATHINFO_DIRNAME) . '/' . pathinfo($oldPath, PATHINFO_FILENAME);
                    Storage::disk('public')->delete($oldPathWithoutExt . '.jpg');
                    Storage::disk('public')->delete($oldPathWithoutExt . '.webp');
                }

                $image = $request->file('image');

                // Log image information
                Log::info('Image details for update:', [
                    'name' => $image->getClientOriginalName(),
                    'extension' => $image->getClientOriginalExtension(),
                    'mime' => $image->getMimeType(),
                    'size' => $image->getSize()
                ]);

                // Check if this is a "problematic" image (you can add specific conditions)
                $isProblem = false;

                // If image is large and has transparency, mark as potentially problematic
                if ($image->getSize() > 1000000 &&
                    ($image->getMimeType() == 'image/png' ||
                        strtolower($image->getClientOriginalExtension()) == 'png')) {
                    $isProblem = true;
                    Log::info('Potentially problematic image detected, using direct GD handling');
                }

                if ($isProblem) {
                    // Direct GD handling for problematic images
                    try {
                        // Generate filename
                        $filename = Str::random(20) . '_' . time();

                        // Load with GD
                        $sourceImage = imagecreatefrompng($image->getRealPath());

                        if (!$sourceImage) {
                            throw new \Exception("Failed to create image resource from PNG");
                        }

                        // Get dimensions
                        $width = imagesx($sourceImage);
                        $height = imagesy($sourceImage);

                        // Resize if needed
                        if ($width > 1200) {
                            $newWidth = 1200;
                            $newHeight = intval($height * ($newWidth / $width));

                            $newImage = imagecreatetruecolor($newWidth, $newHeight);

                            // Preserve transparency
                            imagealphablending($newImage, false);
                            imagesavealpha($newImage, true);
                            $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
                            imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);

                            // Resize
                            imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                            // Replace source with resized image
                            imagedestroy($sourceImage);
                            $sourceImage = $newImage;

                            $width = $newWidth;
                            $height = $newHeight;
                        }

                        // Create directory if needed
                        $directory = storage_path('app/public/gallery');
                        if (!file_exists($directory)) {
                            mkdir($directory, 0755, true);
                        }

                        // Save as JPG
                        $jpgPath = 'gallery/' . $filename . '.jpg';
                        $jpgFullPath = storage_path('app/public/' . $jpgPath);

                        // Create white background image (JPG doesn't support transparency)
                        $jpgImage = imagecreatetruecolor($width, $height);
                        $white = imagecolorallocate($jpgImage, 255, 255, 255);
                        imagefilledrectangle($jpgImage, 0, 0, $width, $height, $white);

                        // Copy PNG onto white background
                        imagecopy($jpgImage, $sourceImage, 0, 0, 0, 0, $width, $height);

                        // Save as JPG
                        imagejpeg($jpgImage, $jpgFullPath, 85);
                        imagedestroy($jpgImage);

                        // Try to save as WebP too if GD supports it
                        if (function_exists('imagewebp')) {
                            $webpPath = 'gallery/' . $filename . '.webp';
                            $webpFullPath = storage_path('app/public/' . $webpPath);

                            // Create WebP image
                            $webpImage = imagecreatetruecolor($width, $height);
                            $white = imagecolorallocate($webpImage, 255, 255, 255);
                            imagefilledrectangle($webpImage, 0, 0, $width, $height, $white);

                            // Copy PNG onto white background
                            imagecopy($webpImage, $sourceImage, 0, 0, 0, 0, $width, $height);

                            // Save as WebP
                            imagewebp($webpImage, $webpFullPath, 85);
                            imagedestroy($webpImage);

                            // Store WebP path as primary if supported
                            $imageData['image_url'] = '/storage/' . $webpPath;
                            Log::info('WebP image saved to: ' . $webpPath);
                        } else {
                            // Store the JPG path in the database if WebP not supported
                            $imageData['image_url'] = '/storage/' . $jpgPath;
                        }

                        // Clean up
                        imagedestroy($sourceImage);

                        Log::info('Problematic image processed and saved to: ' . $jpgPath);

                    } catch (\Exception $e) {
                        Log::error('Error processing problematic image with GD: ' . $e->getMessage(), [
                            'exception' => $e,
                            'trace' => $e->getTraceAsString()
                        ]);

                        // If GD processing fails, try simple file storage
                        $path = $image->store('gallery', 'public');
                        $imageData['image_url'] = '/storage/' . $path;
                    }
                } else {
                    // Normal image handling with direct storage
                    $path = $image->store('gallery', 'public');
                    $imageData['image_url'] = '/storage/' . $path;
                    Log::info('Standard image upload to: ' . $path);
                }
            }

            $galleryImage->update($imageData);
            return $galleryImage;
        } catch (\Exception $e) {
            Log::error('Gallery image update error: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception('Error updating gallery image: ' . $e->getMessage());
        }
    }

    public function deleteImage($id)
    {
        try {
            $galleryImage = GalleryImage::findOrFail($id);

            // Delete image if exists
            if ($galleryImage->image_url) {
                $basePath = str_replace('/storage/', '', $galleryImage->image_url);
                $basePathWithoutExt = pathinfo($basePath, PATHINFO_DIRNAME) . '/' . pathinfo($basePath, PATHINFO_FILENAME);

                // Delete all possible formats
                Storage::disk('public')->delete($basePath);
                Storage::disk('public')->delete($basePathWithoutExt . '.jpg');
                Storage::disk('public')->delete($basePathWithoutExt . '.webp');
            }

            $galleryImage->delete();
            return ['message' => 'Gallery image deleted successfully'];
        } catch (\Exception $e) {
            Log::error('Gallery image deletion error: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception('Error deleting gallery image: ' . $e->getMessage());
        }
    }

    public function updateOrder(Request $request)
    {
        try {
            $orderedIds = $request->input('ordered_ids', []);

            foreach ($orderedIds as $index => $id) {
                GalleryImage::where('id', $id)->update(['order' => $index]);
            }

            return ['message' => 'Gallery order updated successfully'];
        } catch (\Exception $e) {
            Log::error('Order update error: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception('Error updating gallery order: ' . $e->getMessage());
        }
    }

    private function validateAndPrepareData(Request $request)
    {
        return $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'nullable|string',
            'image' => $request->isMethod('post') ? 'required|image|mimes:jpg,jpeg,png,webp|max:10240' : 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
            'is_carousel' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'order' => 'nullable|integer',
        ]);
    }
}
