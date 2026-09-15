<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Quiz extends Model
{
    protected $fillable = [
        'title',
        'description',
        'instructions',
        'subject_id',
        'academic_year_id',
        'term_id',
        'total_points',
        'passing_score',
        'max_attempts',
        'questions_per_attempt',
        'shuffle_questions',
        'shuffle_options',
        'show_correct_answers',
        'is_active',
        'time_limit_minutes',
        'available_from',
        'available_until',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'shuffle_questions' => 'boolean',
        'shuffle_options' => 'boolean',
        'show_correct_answers' => 'boolean',
        'available_from' => 'datetime',
        'available_until' => 'datetime',
        'total_points' => 'integer',
        'passing_score' => 'integer',
        'max_attempts' => 'integer',
        'questions_per_attempt' => 'integer',
        'time_limit_minutes' => 'integer',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function hasStudentAttempted($studentId): bool
    {
        return $this->attempts()
            ->where('student_id', $studentId)
            ->where('status', 'submitted')
            ->exists();
    }

    public function getActiveAttempt($studentId)
    {
        return $this->attempts()
            ->where('student_id', $studentId)
            ->where('status', 'in_progress')
            ->first();
    }

    public function attemptsCountForStudent($studentId): int
    {
        return $this->attempts()
            ->where('student_id', $studentId)
            ->where('status', 'submitted')
            ->count();
    }

    public function remainingAttemptsForStudent($studentId): int
    {
        if ($this->max_attempts <= 0) {
            return 999; // unlimited
        }
        return max(0, $this->max_attempts - $this->attemptsCountForStudent($studentId));
    }

    public function canStudentAttempt($studentId): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if (!$this->isAvailable()) {
            return false;
        }

        // If there is already an in-progress attempt, student can continue it
        if ($this->getActiveAttempt($studentId)) {
            return true;
        }

        return $this->remainingAttemptsForStudent($studentId) > 0;
    }

    public function isAvailable(): bool
    {
        $now = now();
        if ($this->available_from && $now->lt($this->available_from)) {
            return false;
        }
        if ($this->available_until && $now->gt($this->available_until)) {
            return false;
        }
        return true;
    }

    public function bestAttempt($studentId)
    {
        return $this->attempts()
            ->where('student_id', $studentId)
            ->where('status', 'submitted')
            ->orderByDesc('score')
            ->first();
    }

    public function getQuestionsForAttempt(QuizAttempt $attempt)
    {
        $answeredQuestionIds = $attempt->answers()->pluck('question_id')->toArray();

        $questionsQuery = $this->questions()->with('options');

        if (!empty($answeredQuestionIds)) {
            // Retrieve questions previously answered or generated for this attempt
            return $this->questions()->with('options')->whereIn('id', $answeredQuestionIds)->get();
        }

        if ($this->questions_per_attempt && $this->questions_per_attempt > 0) {
            return $questionsQuery->inRandomOrder($attempt->id)
                ->limit($this->questions_per_attempt)
                ->get();
        } elseif ($this->shuffle_questions) {
            return $questionsQuery->inRandomOrder($attempt->id)->get();
        } else {
            return $questionsQuery->orderBy('order')->get();
        }
    }
}

