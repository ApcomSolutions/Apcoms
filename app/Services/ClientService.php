<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ClientService
{
    public function getAllClients()
    {
        return Client::orderBy('order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getActiveClients()
    {
        return Client::where('is_active', true)
            ->orderBy('order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getClientById($id)
    {
        return Client::findOrFail($id);
    }

    public function createClient(Request $request)
    {
        try {
            // Debug incoming request
            Log::info('Creating client with data:', $request->all());

            $clientData = $this->validateAndPrepareData($request);

            // Log after validation
            Log::info('Validated client data:', $clientData);

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
                        $directory = storage_path('app/public/clients');
                        if (!file_exists($directory)) {
                            mkdir($directory, 0755, true);
                        }

                        // Save as JPG
                        $jpgPath = 'clients/' . $filename . '.jpg';
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
                            $webpPath = 'clients/' . $filename . '.webp';
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
                            $clientData['image_url'] = '/storage/' . $webpPath;
                            Log::info('WebP image saved to: ' . $webpPath);
                        } else {
                            // Store the JPG path in the database if WebP not supported
                            $clientData['image_url'] = '/storage/' . $jpgPath;
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
                        $path = $image->store('clients', 'public');
                        $clientData['image_url'] = '/storage/' . $path;
                    }
                } else {
                    // Normal image handling with direct storage
                    $path = $image->store('clients', 'public');
                    $clientData['image_url'] = '/storage/' . $path;
                    Log::info('Standard image upload to: ' . $path);
                }
            }

            // Set a default order if not provided
            if (!isset($clientData['order'])) {
                $maxOrder = Client::max('order');
                $clientData['order'] = ($maxOrder ?? 0) + 1;
            }

            // Set default active status if not provided
            if (!isset($clientData['is_active'])) {
                $clientData['is_active'] = true;
            }

            $client = Client::create($clientData);
            Log::info('Client created with ID: ' . ($client->id ?? 'none'));

            return $client;
        } catch (\Exception $e) {
            // Log the error with detailed information
            Log::error('Client creation error: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            // Return a clear error response
            throw new \Exception('Error creating client: ' . $e->getMessage());
        }
    }

    public function updateClient(Request $request, $id)
    {
        try {
            $client = Client::findOrFail($id);
            $clientData = $this->validateAndPrepareData($request);

            // Upload new image if provided
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($client->image_url) {
                    $oldPath = str_replace('/storage/', '', $client->image_url);
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
                        $directory = storage_path('app/public/clients');
                        if (!file_exists($directory)) {
                            mkdir($directory, 0755, true);
                        }

                        // Save as JPG
                        $jpgPath = 'clients/' . $filename . '.jpg';
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
                            $webpPath = 'clients/' . $filename . '.webp';
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
                            $clientData['image_url'] = '/storage/' . $webpPath;
                            Log::info('WebP image saved to: ' . $webpPath);
                        } else {
                            // Store the JPG path in the database if WebP not supported
                            $clientData['image_url'] = '/storage/' . $jpgPath;
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
                        $path = $image->store('clients', 'public');
                        $clientData['image_url'] = '/storage/' . $path;
                    }
                } else {
                    // Normal image handling with direct storage
                    $path = $image->store('clients', 'public');
                    $clientData['image_url'] = '/storage/' . $path;
                    Log::info('Standard image upload to: ' . $path);
                }
            }

            $client->update($clientData);
            return $client;
        } catch (\Exception $e) {
            Log::error('Client update error: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception('Error updating client: ' . $e->getMessage());
        }
    }

    public function deleteClient($id)
    {
        try {
            $client = Client::findOrFail($id);

            // Delete image if exists
            if ($client->image_url) {
                $basePath = str_replace('/storage/', '', $client->image_url);
                $basePathWithoutExt = pathinfo($basePath, PATHINFO_DIRNAME) . '/' . pathinfo($basePath, PATHINFO_FILENAME);

                // Delete all possible formats
                Storage::disk('public')->delete($basePath);
                Storage::disk('public')->delete($basePathWithoutExt . '.jpg');
                Storage::disk('public')->delete($basePathWithoutExt . '.webp');
            }

            $client->delete();
            return ['message' => 'Client deleted successfully'];
        } catch (\Exception $e) {
            Log::error('Client deletion error: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception('Error deleting client: ' . $e->getMessage());
        }
    }

    public function updateOrder(Request $request)
    {
        try {
            $orderedIds = $request->input('ordered_ids', []);

            foreach ($orderedIds as $index => $id) {
                Client::where('id', $id)->update(['order' => $index]);
            }

            return ['message' => 'Client order updated successfully'];
        } catch (\Exception $e) {
            Log::error('Order update error: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception('Error updating client order: ' . $e->getMessage());
        }
    }

    private function validateAndPrepareData(Request $request)
    {
        return $request->validate([
            'name' => 'required|string|max:100',
            'position' => 'required|string|max:100',
            'company' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:10240',
            'is_active' => 'nullable|boolean',
            'order' => 'nullable|integer',
        ]);
    }
}
