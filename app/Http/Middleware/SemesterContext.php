<?php

namespace App\Http\Middleware;

use App\Services\ActiveSemesterService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class SemesterContext
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $requestedTermId = $request->get('term_id') ?? $request->get('term');
        $context = ActiveSemesterService::resolve($requestedTermId);

        // Share globally with all views
        View::share('activeTerm', $context['active_term']);
        View::share('activeYear', $context['active_term']?->academicYear ?? ActiveSemesterService::getActiveYear());
        View::share('selectedTerm', $context['term']);
        View::share('selectedYear', $context['year']);
        View::share('selectedTermId', $context['term_id']);
        View::share('selectedAcademicYearId', $context['academic_year_id']);
        View::share('isHistoricalSemester', $context['is_historical']);
        View::share('allTermsForSelector', ActiveSemesterService::allTermsForSelect());

        return $next($request);
    }
}
