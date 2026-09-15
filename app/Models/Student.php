<?php
// app/Models/Student.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    public const FUNDING_SOURCES = [
        'Government Sponsorship',
        'Scholarship',
        'Student Loan',
        'Parent/Guardian',
        'Self-Funded',
        'Employer Sponsored',
        'Organization Sponsored',
        'Private Sponsor',
        'Other',
        'Not Specified',
    ];

    protected $fillable = [
        'user_id',
        'reg_number',
        'programme',
        'source_of_funding',
        'funding_source_other',
        'phone',
        'parent_name',
        'parent_phone',
        'address',
        'emergency_contact',
        'profile_picture',
    ];

    /**
     * Get the formatted display label for the student's funding source
     */
    public function getDisplayFundingSourceAttribute(): string
    {
        if ($this->source_of_funding === 'Other' && !empty($this->funding_source_other)) {
            return 'Other (' . $this->funding_source_other . ')';
        }

        return $this->source_of_funding ?: 'Not Specified';
    }

    /**
     * Get the user account associated with this student
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all results for this student
     */
    public function results()
    {
        return $this->hasMany(Result::class);
    }

    /**
     * Get all fees records for this student
     */
    public function fees()
    {
        return $this->hasMany(Fee::class);
    }

    /**
     * Check if the student has paid any amount towards their fees (> 0%).
     * Returns true if no fees are recorded (unrestricted).
     * Required to view online resources.
     */
    public function canViewResources(): bool
    {
        return $this->feePaymentPercentage() > 0;
    }

    /**
     * Check if the student has paid at least half (50%) of their fees.
     * Returns true if no fees are recorded (unrestricted).
     * Required to download resources.
     */
    public function canDownloadResources(): bool
    {
        return $this->feePaymentPercentage() >= 50.0;
    }

    /**
     * Check if the student has paid at least half (50%) of their fees
     * for the currently active enrollment term.
     * Falls back to all-time fees when no active enrollment exists.
     * Students with no fees on record are considered to have met the requirement.
     */
    public function hasHalfPaidFees(): bool
    {
        return $this->canDownloadResources();
    }

    /**
     * Get the percentage of fees paid for the active term (or all-time).
     * Returns 100.0 if no fees exist for the term.
     */
    public function feePaymentPercentage(): float
    {
        $query = $this->fees();

        // Scope to active enrollment term when one exists
        $active = $this->activeEnrollment;
        if ($active) {
            $query = $query
                ->where('academic_year_id', $active->academic_year_id)
                ->where('term_id', $active->term_id);
        }

        $totals = $query->selectRaw('SUM(amount) as total_amount, SUM(paid) as total_paid')->first();

        $totalAmount = (float) ($totals->total_amount ?? 0);
        $totalPaid   = (float) ($totals->total_paid   ?? 0);

        // No fees recorded for this term → 100% paid (unrestricted)
        if ($totalAmount <= 0) {
            return 100.0;
        }

        return min(100.0, round(($totalPaid / $totalAmount) * 100, 2));
    }

    /**
     * Check if the student has paid 100% of their fees.
     */
    public function hasFullPaidFees(): bool
    {
        return $this->feePaymentPercentage() >= 100.0;
    }

    /**
     * Get all notifications for this student
     */
    public function notifications()
{
    return $this->belongsToMany(Notification::class, 'notification_student')
                ->withPivot('is_read', 'read_at')
                ->withTimestamps();
}
    /**
     * Get all messages for this student
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Get all enrollments for this student (academic year and term tracking)
     */
    public function enrollments()
    {
        return $this->hasMany(StudentEnrollment::class);
    }

    /**
     * Get the current active enrollment
     */
    public function activeEnrollment()
    {
        return $this->hasOne(StudentEnrollment::class)
            ->where('status', 'active')
            ->latest();
    }

    /**
     * Get enrollment for a specific academic year and term
     */
    public function enrollmentFor($academicYearId, $termId)
    {
        return $this->enrollments()
            ->where('academic_year_id', $academicYearId)
            ->where('term_id', $termId)
            ->first();
    }

    /**
     * Get the subjects assigned to this student (many-to-many)
     */
    public function subjects()
    {
        return $this->belongsToMany(\App\Models\Subject::class, 'student_subject')
            ->withPivot('academic_year_id', 'term_id', 'status', 'enrolled_date', 'completion_date', 'notes')
            ->withTimestamps();
    }

    /**
     * Get active subjects for the current academic year/term
     */
    public function activeSubjects()
    {
        return $this->subjects()
            ->wherePivot('status', 'enrolled')
            ->wherePivot('academic_year_id', Term::getActiveAcademicYearId())
            ->wherePivot('term_id', Term::getActiveTermId());
    }

    /**
     * Get completed subjects
     */
    public function completedSubjects()
    {
        return $this->subjects()->wherePivot('status', 'completed');
    }

    /**
     * Get enrolled subjects
     */
    public function enrolledSubjects()
    {
        return $this->subjects()->wherePivot('status', 'enrolled');
    }

    /**
     * Get subjects for a specific term
     */
    public function subjectsForTerm($termId)
    {
        return $this->subjects()->wherePivot('term_id', $termId);
    }

    /**
     * Get subjects for a specific academic year
     */
    public function subjectsForAcademicYear($academicYearId)
    {
        return $this->subjects()->wherePivot('academic_year_id', $academicYearId);
    }

    /**
     * Get subjects for a specific academic year and term
     */
    public function subjectsFor($academicYearId, $termId)
    {
        return $this->subjects()
            ->wherePivot('academic_year_id', $academicYearId)
            ->wherePivot('term_id', $termId);
    }

    /**
     * Get the total fees balance for this student
     */
    public function getTotalFeesBalanceAttribute()
    {
        return $this->fees->sum(function ($fee) {
            return $fee->balance;
        });
    }

    /**
     * Check if student has any overdue fees
     */
    public function getHasOverdueFeesAttribute()
    {
        return $this->fees->contains(function ($fee) {
            return $fee->is_overdue;
        });
    }

    /**
     * Get the count of enrolled subjects
     */
    public function getEnrolledSubjectsCountAttribute()
    {
        return $this->enrolledSubjects()->count();
    }

    /**
     * Get the count of completed subjects
     */
    public function getCompletedSubjectsCountAttribute()
    {
        return $this->completedSubjects()->count();
    }

    /**
     * Calculate the total credit hours for enrolled subjects
     */
    public function getTotalCreditHoursAttribute()
    {
        return $this->enrolledSubjects()->sum('credit_hours');
    }

    /**
     * Check if student is enrolled in a specific subject
     */
    public function isEnrolledIn($subjectId)
    {
        return $this->enrolledSubjects()->where('subject_id', $subjectId)->exists();
    }

    /**
     * Check if student is enrolled in a specific subject for a term
     */
    public function isEnrolledInTerm($subjectId, $termId)
    {
        return $this->subjects()
            ->where('subject_id', $subjectId)
            ->wherePivot('term_id', $termId)
            ->wherePivot('status', 'enrolled')
            ->exists();
    }

    /**
     * Get the current programme (from active enrollment)
     */
    public function getCurrentProgrammeAttribute()
    {
        return $this->activeEnrollment?->programme ?? $this->programme;
    }

    /**
     * Get the current academic year
     */
    public function getCurrentAcademicYearAttribute()
    {
        return $this->activeEnrollment?->academicYear;
    }

    /**
     * Get the current term
     */
    public function getCurrentTermAttribute()
    {
        return $this->activeEnrollment?->term;
    }

    /**
     * Get enrollment status
     */
    public function getEnrollmentStatusAttribute()
    {
        return $this->activeEnrollment?->status ?? 'not_enrolled';
    }

    // Scopes for filtering
    public function scopeEnrolledInYear($query, $academicYearId)
    {
        return $query->whereHas('enrollments', function ($q) use ($academicYearId) {
            $q->where('academic_year_id', $academicYearId);
        });
    }

    public function scopeEnrolledInTerm($query, $termId)
    {
        return $query->whereHas('enrollments', function ($q) use ($termId) {
            $q->where('term_id', $termId);
        });
    }

    public function scopeByProgramme($query, $programme)
    {
        return $query->whereHas('enrollments', function ($q) use ($programme) {
            $q->where('programme', 'LIKE', "%{$programme}%");
        });
    }

    public function scopeWithEnrollmentStatus($query, $status)
    {
        return $query->whereHas('enrollments', function ($q) use ($status) {
            $q->where('status', $status);
        });
    }

    public function scopeActive($query)
    {
        return $query->whereHas('enrollments', function ($q) {
            $q->where('status', 'active');
        });
    }
}
