<?php

namespace App\Console\Commands;

use App\Models\QuizAttempt;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExpireQuizAttempts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quizzes:expire-attempts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically finalize and expire active quiz attempts that exceeded the configured time limit';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired in-progress quiz attempts...');

        $inProgressAttempts = QuizAttempt::with(['quiz.questions.options', 'answers'])
            ->where('status', 'in_progress')
            ->get();

        $expiredCount = 0;

        foreach ($inProgressAttempts as $attempt) {
            $quiz = $attempt->quiz;
            if (!$quiz || !$quiz->time_limit_minutes) {
                continue; // Quizzes with no time limit don't expire automatically
            }

            if ($attempt->isExpired()) {
                DB::beginTransaction();
                try {
                    $attemptQuestions = $quiz->getQuestionsForAttempt($attempt);
                    $totalPossiblePoints = $attemptQuestions->sum('points');
                    if ($totalPossiblePoints <= 0) {
                        $totalPossiblePoints = $quiz->total_points > 0 ? $quiz->total_points : 1;
                    }

                    $totalPointsEarned = $attempt->answers()->where('is_correct', true)->sum('points_earned');
                    $percentageScore = round(($totalPointsEarned / $totalPossiblePoints) * 100, 2);
                    $timeLimitSeconds = $quiz->time_limit_minutes * 60;

                    $hasUngradedEssay = $attempt->answers()
                        ->whereHas('question', function ($q) {
                            $q->whereIn('type', ['essay', 'short_answer']);
                        })
                        ->where('is_graded', false)
                        ->exists();

                    $attempt->update([
                        'submitted_at' => now(),
                        'time_taken_seconds' => $timeLimitSeconds,
                        'total_points_earned' => $totalPointsEarned,
                        'score' => $percentageScore,
                        'is_passed' => $percentageScore >= $quiz->passing_score,
                        'needs_manual_grading' => $hasUngradedEssay,
                        'status' => 'expired',
                    ]);

                    DB::commit();
                    $expiredCount++;
                    $this->line("Attempt #{$attempt->id} (Quiz: {$quiz->title}) was expired and scored.");
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error("Failed to auto-expire attempt #{$attempt->id}: " . $e->getMessage());
                    $this->error("Failed to auto-expire attempt #{$attempt->id}: " . $e->getMessage());
                }
            }
        }

        $this->info("Completed. Total expired attempts finalized: {$expiredCount}");
        return Command::SUCCESS;
    }
}
