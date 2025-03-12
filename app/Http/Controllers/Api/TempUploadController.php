<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Support\FileValidator;

class TempUploadController extends Controller
{
    /**
     * Handle temporary image upload for Dropzone
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Validasi file yang diupload menggunakan Laravel Validator
            $validator = Validator::make($request->all(), [
                'temp_image' => 'required|image|mimes:jpg,jpeg,png,webp,gif|max:5120', // max 5MB
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first('temp_image')
                ], 422);
            }

            if (!$request->hasFile('temp_image')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No file uploaded'
                ], 400);
            }

            $image = $request->file('temp_image');

            // Log image information
            Log::info('Temp image upload details:', [
                'name' => $image->getClientOriginalName(),
                'extension' => $image->getClientOriginalExtension(),
                'mime' => $image->getMimeType(),
                'size' => $image->getSize()
            ]);

            // Validasi file menggunakan validator custom
            $validationResult = FileValidator::validateImage($image);
            if ($validationResult !== true) {
                return response()->json([
                    'success' => false,
                    'message' => $validationResult
                ], 422);
            }

            // Buat folder temp jika belum ada
            if (!Storage::disk('public')->exists('temp')) {
                Storage::disk('public')->makeDirectory('temp');
            }

            // Buat nama file unik dan simpan ke folder temp
            // Gunakan nama file yang sudah dibersihkan
            $originalName = FileValidator::sanitizeFilename($image->getClientOriginalName());
            $filename = Str::random(20) . '_' . time() . '_' . $originalName;
            $path = $image->storeAs('temp', $filename, 'public');

            // Return path yang bisa digunakan saat form disimpan
            return response()->json([
                'success' => true,
                'path' => $path,
                'url' => Storage::disk('public')->url($path),
                'message' => 'File uploaded successfully'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Temp upload error: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error uploading file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle temporary image upload specifically for Trix editor
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeTrixAttachment(Request $request)
    {
        try {
            // Validate the uploaded file
            $validator = Validator::make($request->all(), [
                'file' => 'required|image|mimes:jpg,jpeg,png,webp,gif|max:5120', // max 5MB
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first('file')
                ], 422);
            }

            if (!$request->hasFile('file')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No file uploaded'
                ], 400);
            }

            $image = $request->file('file');

            // Log image information
            Log::info('Trix image upload details:', [
                'name' => $image->getClientOriginalName(),
                'extension' => $image->getClientOriginalExtension(),
                'mime' => $image->getMimeType(),
                'size' => $image->getSize()
            ]);

            // Validate file using custom validator
            $validationResult = FileValidator::validateImage($image);
            if ($validationResult !== true) {
                return response()->json([
                    'success' => false,
                    'message' => $validationResult
                ], 422);
            }

            // Create trix-uploads folder if it doesn't exist
            if (!Storage::disk('public')->exists('trix-uploads')) {
                Storage::disk('public')->makeDirectory('trix-uploads');
            }

            // Create unique filename and save to folder
            $originalName = FileValidator::sanitizeFilename($image->getClientOriginalName());
            $filename = Str::random(20) . '_' . time() . '_' . $originalName;
            $path = $image->storeAs('trix-uploads', $filename, 'public');

            $url = Storage::disk('public')->url($path);

            // Format response for Trix editor
            return response()->json([
                'url' => $url,
                'href' => $url
            ], 200);
        } catch (\Exception $e) {
            Log::error('Trix upload error: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error uploading file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle cancellation of temporary uploads
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancel(Request $request)
    {
        try {
            $path = $request->input('path');

            // Validasi path untuk keamanan
            if (!$path || !Str::startsWith($path, 'temp/') || Str::contains($path, '..')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid file path'
                ], 400);
            }

            // Hapus file dari storage
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
                return response()->json([
                    'success' => true,
                    'message' => 'File deleted successfully'
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'File not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Temp delete error: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error deleting file: ' . $e->getMessage()
            ], 500);
        }
    }
}
