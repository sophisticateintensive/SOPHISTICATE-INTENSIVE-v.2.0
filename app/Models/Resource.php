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

    // Check if file exists in storage
    public function fileExists()
    {
        return Storage::disk('local')->exists($this->file_path);
    }

    // Get full file path on disk
    public function getFullPathAttribute()
    {
        return Storage::disk('local')->path($this->file_path);
    }

    // Automatically delete file when resource is deleted
    protected static function booted()
    {
        static::deleting(function ($resource) {
            if ($resource->fileExists()) {
                Storage::disk('local')->delete($resource->file_path);
            }
        });
    }
}
