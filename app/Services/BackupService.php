<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BackupService
{
    protected string $supabaseUrl;
    protected string $supabaseKey;
    protected string $supabaseBucket;
    protected string $backupDir;

    public function __construct()
    {
        $this->supabaseUrl    = rtrim((string) (config('services.supabase.url') ?: env('SUPABASE_URL', '')), '/');
        $this->supabaseKey    = (string) (config('services.supabase.service_role_key') ?: config('services.supabase.key') ?: env('SUPABASE_KEY', env('SUPABASE_ANON_KEY', '')));
        $this->supabaseBucket = (string) (config('services.supabase.bucket') ?: env('SUPABASE_STORAGE_BUCKET', 'backups'));
        $this->backupDir      = storage_path('app/backups');

        if (!File::exists($this->backupDir)) {
            File::makeDirectory($this->backupDir, 0755, true);
        }
    }

    /**
     * Create a database backup snapshot (JSON / SQL format).
     */
    public function createBackup(string $format = 'json'): array
    {
        try {
            $timestamp = now()->format('Y-m-d_His');
            $tables = [
                'users',
                'academic_years',
                'terms',
                'students',
                'subjects',
                'student_subject',
                'student_enrollments',
                'results',
                'fees',
                'messages',
                'notifications',
                'notification_student',
                'resources',
                'quizzes',
                'quiz_questions',
                'quiz_options',
                'quiz_attempts',
                'quiz_answers',
            ];

            $data = [
                'app_name'    => config('app.name', 'Sophisticate Classes'),
                'created_at'  => now()->toIso8601String(),
                'version'     => '1.0',
                'tables_data' => [],
                'counts'      => [],
            ];

            $sqlContent = "-- Sophisticate Classes Database Backup\n";
            $sqlContent .= "-- Generated at: " . now()->toIso8601String() . "\n\n";

            foreach ($tables as $table) {
                if (DB::getSchemaBuilder()->hasTable($table)) {
                    $rows = DB::table($table)->get()->map(fn($item) => (array) $item)->toArray();
                    $data['tables_data'][$table] = $rows;
                    $data['counts'][$table] = count($rows);

                    // Build SQL Inserts
                    if (!empty($rows)) {
                        $sqlContent .= "-- Table: {$table}\n";
                        foreach ($rows as $row) {
                            $columns = implode(', ', array_map(fn($col) => "`$col`", array_keys($row)));
                            $values = implode(', ', array_map(function ($val) {
                                if (is_null($val)) return 'NULL';
                                return "'" . addslashes((string) $val) . "'";
                            }, array_values($row)));
                            $sqlContent .= "INSERT INTO `{$table}` ({$columns}) VALUES ({$values});\n";
                        }
                        $sqlContent .= "\n";
                    }
                }
            }

            if ($format === 'sql') {
                $filename = "backup_db_{$timestamp}.sql";
                $filePath = $this->backupDir . DIRECTORY_SEPARATOR . $filename;
                File::put($filePath, $sqlContent);
            } else {
                $filename = "backup_snapshot_{$timestamp}.json";
                $filePath = $this->backupDir . DIRECTORY_SEPARATOR . $filename;
                File::put($filePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            }

            $fileSize = File::size($filePath);

            return [
                'success'   => true,
                'filename'  => $filename,
                'filepath'  => $filePath,
                'filesize'  => $fileSize,
                'counts'    => $data['counts'],
                'total_rows'=> array_sum($data['counts']),
            ];
        } catch (\Throwable $e) {
            Log::error('Backup creation failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Upload a local backup file directly to Supabase Storage.
     */
    public function uploadToSupabase(string $filePath, string $filename): array
    {
        if (empty($this->supabaseUrl) || empty($this->supabaseKey)) {
            return [
                'success' => false,
                'message' => 'Supabase URL or API Key is missing. Please configure SUPABASE_URL and SUPABASE_KEY in your environment.',
            ];
        }

        if (!File::exists($filePath)) {
            return [
                'success' => false,
                'message' => 'Backup file does not exist locally.',
            ];
        }

        try {
            $fileContent = File::get($filePath);
            $mimeType = str_ends_with($filename, '.json') ? 'application/json' : 'text/plain';

            // Ensure bucket exists or create it
            $this->ensureSupabaseBucketExists();

            // Upload via Supabase Storage REST API
            // POST /storage/v1/object/{bucket}/{path}
            $endpoint = "{$this->supabaseUrl}/storage/v1/object/{$this->supabaseBucket}/{$filename}";

            $response = Http::withHeaders([
                'apikey'        => $this->supabaseKey,
                'Authorization' => 'Bearer ' . $this->supabaseKey,
                'Content-Type'  => $mimeType,
                'x-upsert'      => 'true',
            ])->withBody($fileContent, $mimeType)->post($endpoint);

            if ($response->successful()) {
                $publicUrl = "{$this->supabaseUrl}/storage/v1/object/public/{$this->supabaseBucket}/{$filename}";
                return [
                    'success' => true,
                    'message' => 'Successfully backed up and uploaded to Supabase Storage!',
                    'url'     => $publicUrl,
                    'key'     => "{$this->supabaseBucket}/{$filename}",
                ];
            }

            Log::error('Supabase Storage upload error: ' . $response->body());
            return [
                'success' => false,
                'message' => 'Supabase upload failed: ' . ($response->json('message') ?? $response->body()),
            ];
        } catch (\Throwable $e) {
            Log::error('Supabase upload exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Connection error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Ensure the Supabase storage bucket exists.
     */
    protected function ensureSupabaseBucketExists(): void
    {
        try {
            $bucketEndpoint = "{$this->supabaseUrl}/storage/v1/bucket";
            
            // Check if bucket exists
            $res = Http::withHeaders([
                'apikey'        => $this->supabaseKey,
                'Authorization' => 'Bearer ' . $this->supabaseKey,
            ])->get("{$bucketEndpoint}/{$this->supabaseBucket}");

            if ($res->status() === 404) {
                // Create bucket
                Http::withHeaders([
                    'apikey'        => $this->supabaseKey,
                    'Authorization' => 'Bearer ' . $this->supabaseKey,
                    'Content-Type'  => 'application/json',
                ])->post($bucketEndpoint, [
                    'id'     => $this->supabaseBucket,
                    'name'   => $this->supabaseBucket,
                    'public' => false,
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('Could not verify/create Supabase bucket: ' . $e->getMessage());
        }
    }

    /**
     * Test Supabase credentials and connectivity.
     */
    public function testSupabaseConnection(): array
    {
        if (empty($this->supabaseUrl) || empty($this->supabaseKey)) {
            return [
                'connected' => false,
                'configured'=> false,
                'message'   => 'Supabase credentials are not set in .env. Configure SUPABASE_URL and SUPABASE_KEY.',
            ];
        }

        try {
            $endpoint = "{$this->supabaseUrl}/storage/v1/bucket";
            $res = Http::timeout(8)->withHeaders([
                'apikey'        => $this->supabaseKey,
                'Authorization' => 'Bearer ' . $this->supabaseKey,
            ])->get($endpoint);

            if ($res->successful()) {
                return [
                    'connected' => true,
                    'configured'=> true,
                    'message'   => 'Connected successfully to Supabase Storage!',
                    'buckets'   => $res->json() ?? [],
                ];
            }

            return [
                'connected' => false,
                'configured'=> true,
                'message'   => 'Supabase responded with code ' . $res->status() . ': ' . ($res->json('message') ?? $res->body()),
            ];
        } catch (\Throwable $e) {
            return [
                'connected' => false,
                'configured'=> true,
                'message'   => 'Failed to connect to Supabase: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * List all local and cloud backup files.
     */
    public function getBackups(): array
    {
        $localFiles = [];
        if (File::exists($this->backupDir)) {
            $files = File::files($this->backupDir);
            foreach ($files as $file) {
                $localFiles[] = [
                    'filename'   => $file->getFilename(),
                    'filepath'   => $file->getPathname(),
                    'size'       => $file->getSize(),
                    'created_at' => \Carbon\Carbon::createFromTimestamp($file->getMTime()),
                    'type'       => str_ends_with($file->getFilename(), '.sql') ? 'SQL Dump' : 'JSON Snapshot',
                ];
            }
        }

        // Sort latest first
        usort($localFiles, fn($a, $b) => $b['created_at']->timestamp <=> $a['created_at']->timestamp);

        return $localFiles;
    }

    /**
     * Delete a local backup file.
     */
    public function deleteBackup(string $filename): bool
    {
        $filePath = $this->backupDir . DIRECTORY_SEPARATOR . basename($filename);
        if (File::exists($filePath)) {
            return File::delete($filePath);
        }
        return false;
    }
}
