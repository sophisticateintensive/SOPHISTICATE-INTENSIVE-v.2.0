<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'credit_hours',
    ];

    protected $casts = [
        'credit_hours' => 'integer',
    ];

    /**
     * Get all results for this subject
     */
    public function results()
    {
        return $this->hasMany(Result::class);
    }

    /**
     * Get all students assigned to this subject (many-to-many)
     * This uses the student_subject pivot table
     */
    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_subject')
            ->withPivot('academic_year_id', 'term_id', 'status', 'enrolled_date', 'completion_date', 'notes')
            ->withTimestamps();
    }

    /**
     * Get enrolled students for current term
     */
    public function enrolledStudents()
    {
        return $this->students()->wherePivot('status', 'enrolled');
    }

    /**
     * Get students who completed this subject
     */
    public function completedStudents()
    {
        return $this->students()->wherePivot('status', 'completed');
    }

    /**
     * Get students who dropped this subject
     */
    public function droppedStudents()
    {
        return $this->students()->wherePivot('status', 'dropped');
    }

    /**
     * Get students enrolled in a specific term
     */
    public function studentsForTerm($termId)
    {
        return $this->students()->wherePivot('term_id', $termId);
    }

    /**
     * Get students enrolled in a specific academic year
     */
    public function studentsForAcademicYear($academicYearId)
    {
        return $this->students()->wherePivot('academic_year_id', $academicYearId);
    }

    /**
     * Get the count of enrolled students
     */
    public function getEnrolledCountAttribute()
    {
        return $this->enrolledStudents()->count();
    }

    /**
     * Get the count of completed students
     */
    public function getCompletedCountAttribute()
    {
        return $this->completedStudents()->count();
    }

    /**
     * Get the average score for this subject across all students
     */
    public function getAverageScoreAttribute()
    {
        return $this->results()->avg('marks') ?? 0;
    }

    /**
     * Get the pass rate for this subject (students who scored >= 40)
     */
    public function getPassRateAttribute()
    {
        $totalResults = $this->results()->count();
        if ($totalResults === 0) {
            return 0;
        }

        $passedResults = $this->results()->where('marks', '>=', 40)->count();
        return round(($passedResults / $totalResults) * 100, 1);
    }

    /**
     * Check if this subject has any enrolled students
     */
    public function getHasEnrolledStudentsAttribute()
    {
        return $this->enrolledStudents()->exists();
    }

    /**
     * Get the subject code with name (for dropdowns)
     */
    public function getDisplayNameAttribute()
    {
        return $this->code . ' - ' . $this->name . ' (' . $this->credit_hours . ' cr)';
    }
}