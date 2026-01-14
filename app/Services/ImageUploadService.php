<?php

namespace App\Services;

use App\Models\ImageCache;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

/**
 * Service for handling image uploads with hash-based deduplication
 *
 * This service generates a hash of uploaded images and checks if the same
 * image already exists in the cache. If it does, it returns the cached URL
 * instead of uploading again, saving storage space and upload time.
 */
class ImageUploadService
{
    public function __construct(
        private CloudinaryService $cloudinary
    ) {}

    /**
     * Upload an image with deduplication check
     *
     * @param  UploadedFile  $file  The file to upload
     * @param  array  $options  Additional upload options
     * @return array Returns image data including URL and public ID
     */
    public function uploadWithCache(UploadedFile $file, array $options = []): array
    {
        // Generate hash of the file
        $imageHash = $this->generateFileHash($file);

        // Check if image already exists in cache
        $cachedImage = ImageCache::where('image_hash', $imageHash)->first();

        if ($cachedImage) {
            // Image already exists, increment usage and return cached data
            $cachedImage->incrementUsage();

            Log::info("Image cache hit for hash: {$imageHash}");

            return [
                'url' => $cachedImage->url,
                'secure_url' => $cachedImage->secure_url,
                'public_id' => $cachedImage->public_id,
                'width' => $cachedImage->width,
                'height' => $cachedImage->height,
                'format' => $cachedImage->format,
                'cached' => true,
            ];
        }

        // Image doesn't exist, upload to Cloudinary

        try {
            $uploadResult = $this->cloudinary->uploadImage($file, $options);

            // Store in cache
            ImageCache::create([
                'image_hash' => $imageHash,
                'url' => $uploadResult['url'],
                'secure_url' => $uploadResult['secure_url'],
                'public_id' => $uploadResult['public_id'],
                'width' => $uploadResult['width'] ?? null,
                'height' => $uploadResult['height'] ?? null,
                'format' => $uploadResult['format'] ?? null,
                'file_size' => $file->getSize(),
                'usage_count' => 1,
            ]);

            Log::info("Image uploaded and cached with hash: {$imageHash}");

            return array_merge($uploadResult, ['cached' => false]);
        } catch (\Exception $e) {
            Log::error('Image upload failed: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Upload multiple images with deduplication
     *
     * @param  array  $files  Array of UploadedFile objects
     * @param  string  $folder  The folder path in Cloudinary
     * @param  array  $options  Additional upload options
     * @return array Array of upload results
     */
    public function uploadMultipleWithCache(array $files, string $folder = 'assets', array $options = []): array
    {
        $results = [];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                try {
                    $results[] = $this->uploadWithCache($file, $folder, $options);
                } catch (\Exception $e) {
                    Log::error('Failed to upload file: '.$e->getMessage());
                    $results[] = ['error' => $e->getMessage()];
                }
            }
        }

        return $results;
    }

    /**
     * Delete an image and update cache
     *
     * @param  string  $publicId  The public ID of the image to delete
     */
    public function deleteWithCache(string $publicId): bool
    {
        // Find the cached image
        $cachedImage = ImageCache::where('public_id', $publicId)->first();

        if (! $cachedImage) {
            // Not in cache, just delete from Cloudinary
            return $this->cloudinary->deleteImage($publicId);
        }

        // Decrement usage count
        $cachedImage->decrementUsage();

        // If usage count is 0, delete from Cloudinary and cache
        if ($cachedImage->usage_count <= 0) {
            $deleted = $this->cloudinary->deleteImage($publicId);

            if ($deleted) {
                $cachedImage->delete();
                Log::info("Image deleted from cache and Cloudinary: {$publicId}");
            }

            return $deleted;
        }

        // Still in use by other records, don't delete from Cloudinary
        Log::info("Image usage decremented but not deleted (still in use): {$publicId}");

        return true;
    }

    /**
     * Generate SHA-256 hash of a file
     */
    private function generateFileHash(UploadedFile $file): string
    {
        return hash_file('sha256', $file->getRealPath());
    }

    /**
     * Check if an image hash exists in cache
     */
    public function findByHash(string $hash): ?ImageCache
    {
        return ImageCache::where('image_hash', $hash)->first();
    }

    /**
     * Get cache statistics
     */
    public function getCacheStats(): array
    {
        return [
            'total_cached_images' => ImageCache::count(),
            'total_usage_count' => ImageCache::sum('usage_count'),
            'total_size_saved' => ImageCache::sum('file_size'),
        ];
    }
}
