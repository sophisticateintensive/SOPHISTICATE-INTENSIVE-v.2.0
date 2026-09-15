<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;

        // Get the current active enrollment
        $activeEnrollment = $student?->activeEnrollment;

        // Get results for current academic year and term only
        if ($activeEnrollment) {
            $recentResults = $student->results()
                ->where('academic_year_id', $activeEnrollment->academic_year_id)
                ->where('term_id', $activeEnrollment->term_id)
                ->with(['subject', 'term', 'academicYear'])
                ->latest()
                ->take(3)
                ->get();

            // Get all results for current enrollment for statistics
            $allCurrentResults = $student->results()
                ->where('academic_year_id', $activeEnrollment->academic_year_id)
                ->where('term_id', $activeEnrollment->term_id)
                ->get();

            $feesQuery = $student->fees()
                ->where('academic_year_id', $activeEnrollment->academic_year_id)
                ->where('term_id', $activeEnrollment->term_id);

            $totalFees = (clone $feesQuery)->sum('amount');
            $paidFees = (clone $feesQuery)->sum('paid');
            $balanceFees = $totalFees - $paidFees;
        } else {
            $recentResults = collect();
            $allCurrentResults = collect();
            $totalFees = $student ? $student->fees()->sum('amount') : 0;
            $paidFees = $student ? $student->fees()->sum('paid') : 0;
            $balanceFees = $totalFees - $paidFees;
        }

        $recentNotifications = $student ? \App\Models\Notification::forStudent($student->id)
            ->latest()
            ->take(3)
            ->get() : collect();

        $unreadMessages = $student ? $student->messages()
            ->where('is_read', false)
            ->count() : 0;

        // Calculate statistics for current results
        $averageMarks = $allCurrentResults->avg('marks');
        $highestMark = $allCurrentResults->max('marks');
        $passCount = $allCurrentResults->where('marks', '>=', 40)->count();
        $passRate = $allCurrentResults->count() > 0 ? ($passCount / $allCurrentResults->count()) * 100 : 0;
        $totalSubjects = $allCurrentResults->count();
        $totalCreditHours = $allCurrentResults->sum('subject.credit_hours');

        return view('student.dashboard.index', compact(
            'student',
            'activeEnrollment',
            'recentResults',
            'allCurrentResults',
            'recentNotifications',
            'unreadMessages',
            'totalFees',
            'paidFees',
            'balanceFees',
            'averageMarks',
            'highestMark',
            'passRate',
            'totalSubjects',
            'totalCreditHours'
        ));
    }
}
