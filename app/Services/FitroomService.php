<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class FitroomService
{
    protected $apiKey;
    protected $apiUrl;

    public function __construct()
    {
        $this->apiKey = config('services.fitroom.api_key');
        $this->apiUrl = 'https://platform.fitroom.app/api/tryon/v2';
    }

    /**
     * Create virtual try-on task by uploading files directly
     * 
     * @param string $modelImagePath - Local file path to model image
     * @param string $clothImagePath - Local file path to cloth image
     * @param string $clothType - 'upper', 'lower', or 'combo'
     * @param bool $hdMode - Enable HD mode (default: true)
     * @return array
     */
    public function createTryOnTask(
        string $modelImagePath,
        string $clothImagePath,
        string $clothType = 'upper',
        bool $hdMode = true
    ): array {
        try {
            $response = Http::withHeaders([
                'X-API-KEY' => $this->apiKey,
            ])->attach(
                'model_image',
                file_get_contents($modelImagePath),
                'model.jpg'
            )->attach(
                'cloth_image',
                file_get_contents($clothImagePath),
                'cloth.jpg'
            )->post("{$this->apiUrl}/tasks", [
                'cloth_type' => $clothType,
                'hd_mode' => $hdMode ? 'true' : 'false',
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception("Try-on task creation failed: " . $response->body());
        } catch (Exception $e) {
            Log::error('Fitroom try-on creation error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get task status and result
     * 
     * @param string $taskId
     * @return array
     */
    public function getTaskStatus(string $taskId): array
    {
        try {
            $response = Http::withHeaders([
                'X-API-KEY' => $this->apiKey,
            ])->get("{$this->apiUrl}/tasks/{$taskId}");

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception("Failed to get task status: " . $response->body());
        } catch (Exception $e) {
            Log::error('Fitroom task status error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Download result image from signed URL
     * 
     * @param string $signedUrl - The download_signed_url from completed task
     * @param string $savePath - Local path to save the image
     * @return bool
     */
    public function downloadResultImage(string $signedUrl, string $savePath): bool
    {
        try {
            // Use Laravel HTTP client with sink to stream download directly to file
            // This avoids memory issues and provides better timeout handling
            $response = Http::timeout(120)
                ->sink($savePath)
                ->get($signedUrl);

            return $response->successful();
        } catch (Exception $e) {
            Log::error('Failed to download result image: ' . $e->getMessage());
            return false;
        }
    }
}