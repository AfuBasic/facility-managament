<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service for uploading files to Cloudinary
 * 
 * Handles image uploads to Cloudinary with organized folder structure
 * and automatic transformations.
 */
class CloudinaryService
{
    private string $cloudName;
    private string $apiKey;
    private string $apiSecret;
    private string $uploadPreset;

    public function __construct()
    {
        $this->cloudName = config('services.cloudinary.cloud_name');
        $this->apiKey = config('services.cloudinary.api_key');
        $this->apiSecret = config('services.cloudinary.api_secret');
        $this->uploadPreset = config('services.cloudinary.upload_preset', 'ml_default');
    }

    /**
     * Upload an image to Cloudinary
     * 
     * @param UploadedFile $file The file to upload
     * @param string $folder The folder path in Cloudinary (e.g., 'assets/123/456')
     * @param array $options Additional upload options
     * @return array Returns ['url' => string, 'public_id' => string, 'secure_url' => string]
     * @throws \Exception
     */
    public function uploadImage(UploadedFile $file, array $options = []): array
    {
        try {
            // Read file content and encode as base64
            $fileContent = file_get_contents($file->getRealPath());
            $base64File = 'data:' . $file->getMimeType() . ';base64,' . base64_encode($fileContent);
            // Prepare upload parameters for unsigned upload
            $params = [
                'file' => $base64File,
                'upload_preset' => $this->uploadPreset,
            ];
            
            // Merge any additional options (but don't override file or upload_preset)
            $params = array_merge($params, $options);
            // Upload to Cloudinary using unsigned upload
            $response = Http::asForm()
                ->post("https://api.cloudinary.com/v1_1/{$this->cloudName}/image/upload", $params);

            if ($response->failed()) {
                throw new \Exception('Cloudinary upload failed: ' . $response->body());
            }

            $data = $response->json();

            return [
                'url' => $data['url'] ?? '',
                'secure_url' => $data['secure_url'] ?? '',
                'public_id' => $data['public_id'] ?? '',
                'width' => $data['width'] ?? 0,
                'height' => $data['height'] ?? 0,
                'format' => $data['format'] ?? '',
                'resource_type' => $data['resource_type'] ?? 'image',
            ];
        } catch (\Exception $e) {
            Log::error('Cloudinary upload error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Upload multiple images to Cloudinary
     * 
     * @param array $files Array of UploadedFile objects
     * @param string $folder The folder path in Cloudinary
     * @param array $options Additional upload options
     * @return array Array of upload results
     */
    public function uploadMultiple(array $files, string $folder = 'assets', array $options = []): array
    {
        $results = [];
        
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                try {
                    $results[] = $this->uploadImage($file, $folder, $options);
                } catch (\Exception $e) {
                    Log::error('Failed to upload file: ' . $e->getMessage());
                    $results[] = ['error' => $e->getMessage()];
                }
            }
        }
        
        return $results;
    }

    /**
     * Delete an image from Cloudinary
     * 
     * @param string $publicId The public ID of the image to delete
     * @return bool
     */
    public function deleteImage(string $publicId): bool
    {
        try {
            $timestamp = time();
            
            $params = [
                'public_id' => $publicId,
                'timestamp' => $timestamp,
                'api_key' => $this->apiKey,
            ];

            $params['signature'] = $this->generateSignature($params);

            $response = Http::asForm()
                ->post("https://api.cloudinary.com/v1_1/{$this->cloudName}/image/destroy", $params);

            return $response->successful() && ($response->json()['result'] ?? '') === 'ok';
        } catch (\Exception $e) {
            Log::error('Cloudinary delete error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete multiple images from Cloudinary
     * 
     * @param array $publicIds Array of public IDs to delete
     * @return array Results of deletion attempts
     */
    public function deleteMultiple(array $publicIds): array
    {
        $results = [];
        
        foreach ($publicIds as $publicId) {
            $results[$publicId] = $this->deleteImage($publicId);
        }
        
        return $results;
    }

    /**
     * Generate Cloudinary signature for authenticated requests
     * 
     * @param array $params Parameters to sign
     * @return string
     */
    private function generateSignature(array $params): string
    {
        // Remove signature and file from params before signing
        unset($params['signature'], $params['file'], $params['api_key']);
        
        // Sort parameters alphabetically
        ksort($params);
        
        // Build query string
        $queryString = http_build_query($params, '', '&');
        
        // Generate SHA-1 signature
        return sha1($queryString . $this->apiSecret);
    }

    /**
     * Get optimized image URL with transformations
     * 
     * @param string $publicId The public ID of the image
     * @param array $transformations Array of transformation options
     * @return string
     */
    public function getOptimizedUrl(string $publicId, array $transformations = []): string
    {
        $baseUrl = "https://res.cloudinary.com/{$this->cloudName}/image/upload";
        
        if (empty($transformations)) {
            return "{$baseUrl}/{$publicId}";
        }
        
        $transformString = $this->buildTransformationString($transformations);
        
        return "{$baseUrl}/{$transformString}/{$publicId}";
    }

    /**
     * Build transformation string from array
     * 
     * @param array $transformations
     * @return string
     */
    private function buildTransformationString(array $transformations): string
    {
        $parts = [];
        
        foreach ($transformations as $key => $value) {
            $parts[] = "{$key}_{$value}";
        }
        
        return implode(',', $parts);
    }
}
