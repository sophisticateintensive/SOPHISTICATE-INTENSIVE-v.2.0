<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SupabaseStorageService
{
    protected string $supabaseUrl;
    protected string $supabaseKey;
    protected string $resourceBucket;

    public function __construct()
    {
        $this->supabaseUrl     = rtrim((string) (config('services.supabase.url') ?: env('SUPABASE_URL', '')), '/');
        $this->supabaseKey     = (string) (config('services.supabase.service_role_key') ?: config('services.supabase.key') ?: env('SUPABASE_KEY', ''));
        $this->resourceBucket  = (string) (config('services.supabase.resource_bucket') ?: env('SUPABASE_RESOURCE_BUCKET', 'resources'));
    }

    /**
     * Check if Supabase credentials are configured.
     */
    public function isConfigured(): bool
    {
        return !empty($this->supabaseUrl) && !empty($this->supabaseKey);
    }

    /**
     * Ensure a Supabase Storage bucket exists.
     */
    public function ensureBucketExists(string $bucket = 'resources', bool $isPublic = false): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        try {
            $endpoint = "{$this->supabaseUrl}/storage/v1/bucket";
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->supabaseKey}",
                'apikey'        => $this->supabaseKey,
            ])->timeout(10)->get($endpoint);

            if ($response->successful()) {
                $buckets = $response->json();
                $names = is_array($buckets) ? array_column($buckets, 'name') : [];
                if (in_array($bucket, $names)) {
                    return true;
                }
            }

            // Create bucket
            $create = Http::withHeaders([
                'Authorization' => "Bearer {$this->supabaseKey}",
                'apikey'        => $this->supabaseKey,
                'Content-Type'  => 'application/json',
            ])->timeout(10)->post($endpoint, [
                'id'     => $bucket,
                'name'   => $bucket,
                'public' => $isPublic,
            ]);

            return $create->successful() || $create->status() === 201;
        } catch (\Throwable $e) {
            Log::warning("Failed to ensure Supabase bucket [{$bucket}]: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Upload a local file to Supabase Storage.
     */
    public function uploadFile(string $remotePath, string $localFilePath, ?string $bucket = null, ?string $mimeType = null): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'Supabase credentials not configured.',
            ];
        }

        if (!File::exists($localFilePath)) {
            return [
                'success' => false,
                'message' => 'Local file does not exist: ' . $localFilePath,
            ];
        }

        $bucket = $bucket ?: $this->resourceBucket;
        $this->ensureBucketExists($bucket);

        try {
            $fileContent = File::get($localFilePath);
            $mimeType    = $mimeType ?: (File::mimeType($localFilePath) ?: 'application/octet-stream');
            $cleanPath   = ltrim(str_replace('\\', '/', $remotePath), '/');

            $endpoint = "{$this->supabaseUrl}/storage/v1/object/{$bucket}/{$cleanPath}";

            // Upload (upsert)
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->supabaseKey}",
                'apikey'        => $this->supabaseKey,
                'Content-Type'  => $mimeType,
                'x-upsert'      => 'true',
            ])->timeout(60)
              ->withBody($fileContent, $mimeType)
              ->post($endpoint);

            if ($response->successful() || $response->status() === 200 || $response->status() === 201) {
                return [
                    'success'     => true,
                    'bucket'      => $bucket,
                    'remote_path' => $cleanPath,
                    'message'     => 'File uploaded to Supabase successfully.',
                ];
            }

            Log::error("Supabase upload failed for [{$cleanPath}]: HTTP " . $response->status() . ' - ' . $response->body());
            return [
                'success' => false,
                'message' => 'Supabase upload failed: ' . $response->body(),
            ];
        } catch (\Throwable $e) {
            Log::error("Supabase upload exception for [{$remotePath}]: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Download a file from Supabase Storage and optionally write it to local path.
     */
    public function downloadFile(string $remotePath, ?string $localDestinationPath = null, ?string $bucket = null): ?string
    {
        if (!$this->isConfigured()) {
            return null;
        }

        $bucket    = $bucket ?: $this->resourceBucket;
        $cleanPath = ltrim(str_replace('\\', '/', $remotePath), '/');
        $endpoint  = "{$this->supabaseUrl}/storage/v1/object/{$bucket}/{$cleanPath}";

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->supabaseKey}",
                'apikey'        => $this->supabaseKey,
            ])->timeout(60)->get($endpoint);

            if ($response->successful()) {
                $body = $response->body();
                if ($localDestinationPath) {
                    $dir = dirname($localDestinationPath);
                    if (!File::exists($dir)) {
                        File::makeDirectory($dir, 0755, true);
                    }
                    File::put($localDestinationPath, $body);
                }
                return $body;
            }

            return null;
        } catch (\Throwable $e) {
            Log::error("Supabase download failed for [{$cleanPath}]: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete a file from Supabase Storage.
     */
    public function deleteFile(string $remotePath, ?string $bucket = null): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        $bucket    = $bucket ?: $this->resourceBucket;
        $cleanPath = ltrim(str_replace('\\', '/', $remotePath), '/');
        $endpoint  = "{$this->supabaseUrl}/storage/v1/object/{$bucket}";

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->supabaseKey}",
                'apikey'        => $this->supabaseKey,
                'Content-Type'  => 'application/json',
            ])->timeout(10)->delete($endpoint, [
                'prefixes' => [$cleanPath],
            ]);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::warning("Supabase delete failed for [{$cleanPath}]: " . $e->getMessage());
            return false;
        }
    }
}