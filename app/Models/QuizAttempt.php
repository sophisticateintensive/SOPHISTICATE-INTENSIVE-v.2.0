<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizAttempt extends Model
{
    protected $fillable = [
        'quiz_id',
        'student_id',
        'started_at',
        'submitted_at',
        'time_taken_seconds',
        'total_points_earned',
        'score',
        'is_passed',
        'needs_manual_grading',
        'status',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'is_passed' => 'boolean',
        'needs_manual_grading' => 'boolean',
        'score' => 'decimal:2',
        'total_points_earned' => 'integer',
        'time_taken_seconds' => 'integer',
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(QuizAnswer::class, 'attempt_id');
    }

    /**
     * Get remaining seconds for this attempt based on quiz timer.
     */
    public function getRemainingSeconds(): ?int
    {
        if (!$this->quiz || !$this->quiz->time_limit_minutes) {
            return null; // Unlimited time
        }

        if (!$this->started_at) {
            return $this->quiz->time_limit_minutes * 60;
        }

        $durationSeconds = $this->quiz->time_limit_minutes * 60;
        $elapsedSeconds = now()->diffInSeconds($this->started_at);
        $remaining = $durationSeconds - $elapsedSeconds;

        return (int) max(0, $remaining);
    }

    /**
     * Check if time has expired
     */
    public function isExpired(): bool
    {
        $remaining = $this->getRemainingSeconds();
        if ($remaining === null) {
            return false;
        }
        return $remaining <= 0;
    }

    /**
     * Formatted string of time taken (e.g. 14 mins 32 secs)
     */
    public function getFormattedTimeTakenAttribute(): string
    {
        $seconds = $this->time_taken_seconds;
        if (!$seconds && $this->started_at && $this->submitted_at) {
            $seconds = $this->started_at->diffInSeconds($this->submitted_at);
        }

        if (!$seconds) {
            return '0 secs';
        }

        $mins = floor($seconds / 60);
        $remSecs = $seconds % 60;

        if ($mins > 0) {
            return "{$mins}m {$remSecs}s";
        }
        return "{$remSecs}s";
    }

    /**
     * Check if attempt was auto-submitted or finalized due to time expiration
     */
    public function isTimeExpired(): bool
    {
        return $this->status === 'expired';
    }

    /**
     * Formatted badge label for submission status
     */
    public function getSubmissionStatusLabelAttribute(): string
    {
        if ($this->status === 'expired') {
            return 'Time Expired';
        }
        if ($this->status === 'submitted') {
            return 'Submitted on Time';
        }
        return 'In Progress';
    }
}
