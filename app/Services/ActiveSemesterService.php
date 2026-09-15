<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\Term;

class ActiveSemesterService
{
    /**
     * Get the active Term
     */
    public static function getActiveTerm(): ?Term
    {
        return Term::current();
    }

    /**
     * Get the active Academic Year
     */
    public static function getActiveYear(): ?AcademicYear
    {
        $term = static::getActiveTerm();
        if ($term && $term->academicYear) {
            return $term->academicYear;
        }

        return AcademicYear::current();
    }

    /**
     * Resolve semester context for a given request (or default to active semester)
     *
     * @param int|string|null $requestedTermId
     * @return array{term: ?Term, year: ?AcademicYear, term_id: ?int, academic_year_id: ?int, is_historical: bool}
     */
    public static function resolve($requestedTermId = null): array
    {
        $activeTerm = static::getActiveTerm();
        $selectedTerm = null;

        if ($requestedTermId) {
            $selectedTerm = Term::with('academicYear')->find($requestedTermId);
        }

        if (!$selectedTerm) {
            $selectedTerm = $activeTerm;
        }

        $selectedYear = $selectedTerm?->academicYear ?? static::getActiveYear();
        $isHistorical = $activeTerm && $selectedTerm ? ($activeTerm->id !== $selectedTerm->id) : false;

        return [
            'term' => $selectedTerm,
            'year' => $selectedYear,
            'term_id' => $selectedTerm?->id,
            'selectedTermId' => $selectedTerm?->id,
            'academic_year_id' => $selectedYear?->id,
            'selectedYearId' => $selectedYear?->id,
            'active_term' => $activeTerm,
            'activeTerm' => $activeTerm,
            'is_historical' => $isHistorical,
            'isHistorical' => $isHistorical,
        ];
    }

    /**
     * Get all terms for the selector dropdown
     */
    public static function allTermsForSelect()
    {
        return Term::with('academicYear')
            ->join('academic_years', 'terms.academic_year_id', '=', 'academic_years.id')
            ->select('terms.*')
            ->orderBy('academic_years.id', 'desc')
            ->orderBy('terms.id', 'desc')
            ->get();
    }

    /**
     * Get all academic years
     */
    public static function allAcademicYears()
    {
        return AcademicYear::with('terms')->orderBy('id', 'desc')->get();
    }
}
