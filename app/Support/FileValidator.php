<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class FileValidator
{
    /**
     * Kelas untuk validasi file tambahan untuk keamanan
     */

    /**
     * Validasi file gambar secara ketat
     *
     * @param UploadedFile $file File yang akan divalidasi
     * @return bool|string True jika valid, pesan error jika tidak valid
     */
    public static function validateImage(UploadedFile $file)
    {
        // Validasi ukuran file (maks 10MB)
        $maxSize = 10 * 1024 * 1024; // 10MB
        if ($file->getSize() > $maxSize) {
            return "Ukuran file terlalu besar (maksimum 10MB)";
        }

        // Validasi ekstensi file
        $extension = strtolower($file->getClientOriginalExtension());
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($extension, $allowedExtensions)) {
            return "Ekstensi file tidak diizinkan (hanya jpg, jpeg, png, gif, webp)";
        }

        // Validasi MIME type menggunakan finfo
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file->getPathname());
        finfo_close($finfo);

        $allowedMimeTypes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp'
        ];

        if (!in_array($mimeType, $allowedMimeTypes)) {
            // Log untuk debugging
            Log::warning('File MIME type validation failed', [
                'filename' => $file->getClientOriginalName(),
                'claimed_mime' => $file->getMimeType(),
                'detected_mime' => $mimeType
            ]);

            return "Tipe file tidak valid, hanya file gambar yang diizinkan";
        }

        // Validasi gambar menggunakan getimagesize()
        try {
            $imageInfo = getimagesize($file->getPathname());
            if ($imageInfo === false) {
                return "File bukan gambar yang valid";
            }

            // Cek dimensi gambar (min 16x16px)
            if ($imageInfo[0] < 16 || $imageInfo[1] < 16) {
                return "Dimensi gambar terlalu kecil (minimum 16x16 piksel)";
            }

            // Cek dimensi gambar (max 5000x5000px)
            if ($imageInfo[0] > 5000 || $imageInfo[1] > 5000) {
                return "Dimensi gambar terlalu besar (maksimum 5000x5000 piksel)";
            }

        } catch (\Exception $e) {
            Log::error('Error validating image dimensions', [
                'error' => $e->getMessage(),
                'filename' => $file->getClientOriginalName()
            ]);
            return "Gagal memvalidasi gambar: " . $e->getMessage();
        }

        // Semua validasi berhasil
        return true;
    }

    /**
     * Membersihkan nama file untuk keamanan tambahan
     *
     * @param string $filename Nama file yang akan dibersihkan
     * @return string Nama file yang sudah dibersihkan
     */
    public static function sanitizeFilename($filename)
    {
        // Hapus karakter berbahaya
        $filename = preg_replace("/[^a-zA-Z0-9.-_]/", "", $filename);

        // Batasi panjang nama file
        if (strlen($filename) > 100) {
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $filename = substr(pathinfo($filename, PATHINFO_FILENAME), 0, 90) . '.' . $extension;
        }

        return $filename;
    }
}
