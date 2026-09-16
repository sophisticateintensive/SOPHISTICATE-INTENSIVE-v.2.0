<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Resource extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'file_name',
        'file_path',
        'file_size',
        'file_type',
        'for_students',
        'uploaded_by',
        'subject_id',
    ];

    protected $casts = [
        'for_students' => 'boolean',
        'file_size' => 'integer',
        'subject_id' => 'integer',
    ];

    // Relationships
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Check if file exists in local storage, auto-syncing from Supabase if missing
    public function fileExists()
    {
        if (Storage::disk('local')->exists($this->file_path)) {
            return true;
        }

        // Auto-restore from Supabase Cloud Storage if missing on local disk
        try {
            $supabase = app(\App\Services\SupabaseStorageService::class);
            if ($supabase->isConfigured()) {
                $localFullPath = Storage::disk('local')->path($this->file_path);
                $content = $supabase->downloadFile($this->file_path, $localFullPath);
                return !empty($content) && Storage::disk('local')->exists($this->file_path);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Supabase auto-restore failed for [{$this->file_path}]: " . $e->getMessage());
        }

        return false;
    }

    // Get full file path on disk
    public function getFullPathAttribute()
    {
        return Storage::disk('local')->path($this->file_path);
    }

    // Automatically delete file locally and from Supabase when resource is deleted
    protected static function booted()
    {
        static::deleting(function ($resource) {
            if (Storage::disk('local')->exists($resource->file_path)) {
                Storage::disk('local')->delete($resource->file_path);
            }
            try {
                $supabase = app(\App\Services\SupabaseStorageService::class);
                if ($supabase->isConfigured()) {
                    $supabase->deleteFile($resource->file_path);
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("Could not delete Supabase resource file: " . $e->getMessage());
            }
        });
    }
}
