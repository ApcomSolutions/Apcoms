<?php

namespace App\Services;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class TeamService
{
    public function getAllTeams()
    {
        return Team::orderBy('order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getActiveTeams()
    {
        return Team::where('is_active', true)
            ->orderBy('order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getTeamById($id)
    {
        return Team::findOrFail($id);
    }

    public function createTeam(Request $request)
    {
        try {
            // Debug incoming request
            Log::info('Creating team with data:', $request->all());

            $teamData = $this->validateAndPrepareData($request);

            // Log after validation
            Log::info('Validated team data:', $teamData);

            // Proses gambar sementara jika menggunakan Dropzone
            if ($request->has('temp_image_path')) {
                $tempPath = $request->input('temp_image_path');

                // Validasi path untuk keamanan
                if (!Str::startsWith($tempPath, 'temp/') || Str::contains($tempPath, '..')) {
                    throw new \Exception('Invalid temporary file path');
                }

                // Pastikan file ada
                if (!Storage::disk('public')->exists($tempPath)) {
                    throw new \Exception('Temporary file not found');
                }

                // Pindahkan dari folder temp ke folder teams
                $filename = Str::random(20) . '_' . time() . '.' . pathinfo($tempPath, PATHINFO_EXTENSION);
                $finalPath = 'teams/' . $filename;

                // Buat folder jika belum ada
                if (!Storage::disk('public')->exists('teams')) {
                    Storage::disk('public')->makeDirectory('teams');
                }

                // Pindahkan file
                Storage::disk('public')->copy($tempPath, $finalPath);

                // Hapus file temp
                Storage::disk('public')->delete($tempPath);

                // Set image_url
                $teamData['image_url'] = '/storage/' . $finalPath;

                Log::info('File moved from temp to teams:', [
                    'temp_path' => $tempPath,
                    'final_path' => $finalPath
                ]);
            }
            // Upload image dengan cara tradisional jika tidak menggunakan Dropzone
            else if ($request->hasFile('image')) {
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
                        $directory = storage_path('app/public/teams');
                        if (!file_exists($directory)) {
                            mkdir($directory, 0755, true);
                        }

                        // Save as JPG
                        $jpgPath = 'teams/' . $filename . '.jpg';
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
                            $webpPath = 'teams/' . $filename . '.webp';
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
                            $teamData['image_url'] = '/storage/' . $webpPath;
                            Log::info('WebP image saved to: ' . $webpPath);
                        } else {
                            // Store the JPG path in the database if WebP not supported
                            $teamData['image_url'] = '/storage/' . $jpgPath;
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
                        $path = $image->store('teams', 'public');
                        $teamData['image_url'] = '/storage/' . $path;
                    }
                } else {
                    // Normal image handling with direct storage
                    $path = $image->store('teams', 'public');
                    $teamData['image_url'] = '/storage/' . $path;
                    Log::info('Standard image upload to: ' . $path);
                }
            }

            // Set a default order if not provided
            if (!isset($teamData['order'])) {
                $maxOrder = Team::max('order');
                $teamData['order'] = ($maxOrder ?? 0) + 1;
            }

            // Set default active status if not provided
            if (!isset($teamData['is_active'])) {
                $teamData['is_active'] = true;
            }

            $team = Team::create($teamData);
            Log::info('Team created with ID: ' . ($team->id ?? 'none'));

            return $team;
        } catch (\Exception $e) {
            // Log the error with detailed information
            Log::error('Team creation error: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            // Return a clear error response
            throw new \Exception('Error creating team member: ' . $e->getMessage());
        }
    }

    public function updateTeam(Request $request, $id)
    {
        try {
            $team = Team::findOrFail($id);
            $teamData = $this->validateAndPrepareData($request);

            // Handle temp image if provided via Dropzone
            if ($request->has('temp_image_path')) {
                $tempPath = $request->input('temp_image_path');

                // Validasi path untuk keamanan
                if (!Str::startsWith($tempPath, 'temp/') || Str::contains($tempPath, '..')) {
                    throw new \Exception('Invalid temporary file path');
                }

                // Pastikan file ada
                if (!Storage::disk('public')->exists($tempPath)) {
                    throw new \Exception('Temporary file not found');
                }

                // Delete old image if exists
                if ($team->image_url) {
                    $oldPath = str_replace('/storage/', '', $team->image_url);
                    Storage::disk('public')->delete($oldPath);

                    // Also delete any fallback images
                    $oldPathWithoutExt = pathinfo($oldPath, PATHINFO_DIRNAME) . '/' . pathinfo($oldPath, PATHINFO_FILENAME);
                    Storage::disk('public')->delete($oldPathWithoutExt . '.jpg');
                    Storage::disk('public')->delete($oldPathWithoutExt . '.webp');
                }

                // Pindahkan dari folder temp ke folder teams
                $filename = Str::random(20) . '_' . time() . '.' . pathinfo($tempPath, PATHINFO_EXTENSION);
                $finalPath = 'teams/' . $filename;

                // Buat folder jika belum ada
                if (!Storage::disk('public')->exists('teams')) {
                    Storage::disk('public')->makeDirectory('teams');
                }

                // Pindahkan file
                Storage::disk('public')->copy($tempPath, $finalPath);

                // Hapus file temp
                Storage::disk('public')->delete($tempPath);

                // Set image_url
                $teamData['image_url'] = '/storage/' . $finalPath;

                Log::info('File moved from temp to teams for update:', [
                    'temp_path' => $tempPath,
                    'final_path' => $finalPath
                ]);
            }
            // Upload new image if provided via traditional method
            else if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($team->image_url) {
                    $oldPath = str_replace('/storage/', '', $team->image_url);
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
                        $directory = storage_path('app/public/teams');
                        if (!file_exists($directory)) {
                            mkdir($directory, 0755, true);
                        }

                        // Save as JPG
                        $jpgPath = 'teams/' . $filename . '.jpg';
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
                            $webpPath = 'teams/' . $filename . '.webp';
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
                            $teamData['image_url'] = '/storage/' . $webpPath;
                            Log::info('WebP image saved to: ' . $webpPath);
                        } else {
                            // Store the JPG path in the database if WebP not supported
                            $teamData['image_url'] = '/storage/' . $jpgPath;
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
                        $path = $image->store('teams', 'public');
                        $teamData['image_url'] = '/storage/' . $path;
                    }
                } else {
                    // Normal image handling with direct storage
                    $path = $image->store('teams', 'public');
                    $teamData['image_url'] = '/storage/' . $path;
                    Log::info('Standard image upload to: ' . $path);
                }
            }

            $team->update($teamData);
            return $team;
        } catch (\Exception $e) {
            Log::error('Team update error: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception('Error updating team member: ' . $e->getMessage());
        }
    }

    public function deleteTeam($id)
    {
        try {
            $team = Team::findOrFail($id);

            // Delete image if exists
            if ($team->image_url) {
                $basePath = str_replace('/storage/', '', $team->image_url);
                $basePathWithoutExt = pathinfo($basePath, PATHINFO_DIRNAME) . '/' . pathinfo($basePath, PATHINFO_FILENAME);

                // Delete all possible formats
                Storage::disk('public')->delete($basePath);
                Storage::disk('public')->delete($basePathWithoutExt . '.jpg');
                Storage::disk('public')->delete($basePathWithoutExt . '.webp');
            }

            $team->delete();
            return ['message' => 'Team member deleted successfully'];
        } catch (\Exception $e) {
            Log::error('Team deletion error: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception('Error deleting team member: ' . $e->getMessage());
        }
    }

    public function updateOrder(Request $request)
    {
        try {
            $orderedIds = $request->input('ordered_ids', []);

            foreach ($orderedIds as $index => $id) {
                Team::where('id', $id)->update(['order' => $index]);
            }

            return ['message' => 'Team order updated successfully'];
        } catch (\Exception $e) {
            Log::error('Order update error: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception('Error updating team order: ' . $e->getMessage());
        }
    }

    private function validateAndPrepareData(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:100',
            'position' => 'required|string|max:100',
            'is_active' => 'nullable|boolean',
            'order' => 'nullable|integer',
        ];

        // Hanya validasi file jika tidak menggunakan temp_image_path
        if (!$request->has('temp_image_path')) {
            $rules['image'] = 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:5120';
        }

        return $request->validate($rules);
    }
}
