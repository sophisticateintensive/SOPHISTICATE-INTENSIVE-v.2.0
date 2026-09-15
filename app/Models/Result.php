<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'subject_id',
        'exam_type',        // ADD THIS LINE - CRITICAL!
        'term_id',
        'academic_year_id',
        'marks',
        'grade',
    ];

    protected $casts = [
        'marks' => 'decimal:2',
    ];

    /**
     * Get the student that owns this result
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the subject of this result
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the term of this result
     */
    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    /**
     * Get the academic year of this result
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
    
    /**
     * Accessor for exam type display
     */
    public function getExamTypeDisplayAttribute()
    {
        return $this->exam_type == 'exam1' ? 'Exam 1' : 'Exam 2';
    }
}
