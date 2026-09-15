<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Timetable extends Model
{
    protected $fillable = [
        'academic_year_id',
        'term_id',
        'week_start_date',
        'subject_id',
        'day_of_week',
        'start_time',
        'end_time',
        'venue',
        'lecturer_name',
        'notes',
    ];

    protected $casts = [
        'week_start_date' => 'date:Y-m-d',
        'start_time'      => 'datetime:H:i',
        'end_time'        => 'datetime:H:i',
    ];

    /** Canonical ordered list of working days */
    const DAYS = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

    /**
     * Get the Monday date for the current week.
     */
    public static function currentWeekStart(): string
    {
        return now()->startOfWeek(\Carbon\Carbon::MONDAY)->toDateString();
    }

    /**
     * Format a week range label given any date in that week.
     */
    public static function formatWeekRange($date): string
    {
        $start = \Carbon\Carbon::parse($date)->startOfWeek(\Carbon\Carbon::MONDAY);
        $end = $start->copy()->addDays(5); // Saturday
        return $start->format('M j') . ' – ' . $end->format('M j, Y');
    }

    /**
     * Scope query to a specific week (resolves to Monday).
     */
    public function scopeForWeek($query, $date)
    {
        if (!$date) return $query;
        $monday = \Carbon\Carbon::parse($date)->startOfWeek(\Carbon\Carbon::MONDAY)->toDateString();
        return $query->where('week_start_date', $monday);
    }

    // ─── Relationships ───────────────────────────────────────────────────────

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    // ─── Accessors ───────────────────────────────────────────────────────────

    /**
     * Human-readable 12-hour start time (e.g. "08:00 AM")
     */
    public function getFormattedStartTimeAttribute(): string
    {
        return \Carbon\Carbon::parse($this->start_time)->format('h:i A');
    }

    /**
     * Human-readable 12-hour end time (e.g. "10:00 AM")
     */
    public function getFormattedEndTimeAttribute(): string
    {
        return \Carbon\Carbon::parse($this->end_time)->format('h:i A');
    }

    /**
     * Formatted week label for this entry.
     */
    public function getFormattedWeekAttribute(): string
    {
        return $this->week_start_date ? self::formatWeekRange($this->week_start_date) : 'N/A';
    }
}
