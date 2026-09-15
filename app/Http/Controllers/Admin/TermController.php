<?php
namespace App\Http\Controllers\Admin;

use App\Models\AcademicYear;
use App\Models\Term;
use Illuminate\Http\Request;

class TermController extends BaseController
{
    /**
     * Display a listing of terms.
     */
    public function index(Request $request)
    {
        $query = Term::with('academicYear');

        if ($request->filled('search')) {
            $query->where('term_name', 'LIKE', "%{$request->search}%");
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_locked', false);
            } elseif ($request->status === 'locked') {
                $query->where('is_locked', true);
            }
        }

        if ($request->filled('academic_year')) {
            $query->where('academic_year_id', $request->academic_year);
        }

        $terms = $query->orderBy('created_at', 'desc')->paginate(15);
        $terms->appends($request->all());

        $academicYears = AcademicYear::all();

        return view('admin.terms.index', compact('terms', 'academicYears'));
    }

    /**
     * Show the form for creating a new term.
     */
    public function create()
    {
        $academicYears = AcademicYear::all();
        return view('admin.terms.create', compact('academicYears'));
    }

    /**
     * Store a newly created term.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'term_name'        => 'required|string|max:255',
            'is_locked'        => 'sometimes|boolean',
            'is_current'       => 'sometimes|boolean',
        ]);

        $validated['is_locked'] = $request->has('is_locked');
        $isCurrent = $request->has('is_current');

        $term = Term::create($validated);

        if ($isCurrent || Term::count() === 1) {
            $term->setAsCurrent();
        }

        return redirect()->route('admin.terms.index')
            ->with('success', 'Semester / Term created successfully.');
    }

    /**
     * Show the form for editing a term.
     */
    public function edit(Term $term)
    {
        $academicYears = AcademicYear::all();
        return view('admin.terms.edit', compact('term', 'academicYears'));
    }

    /**
     * Update the specified term.
     */
    public function update(Request $request, Term $term)
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'term_name'        => 'required|string|max:255',
            'is_locked'        => 'sometimes|boolean',
            'is_current'       => 'sometimes|boolean',
        ]);

        $validated['is_locked'] = $request->has('is_locked');
        $isCurrent = $request->has('is_current');

        $term->update($validated);

        if ($isCurrent) {
            $term->setAsCurrent();
        }

        return redirect()->route('admin.terms.index')
            ->with('success', 'Semester / Term updated successfully.');
    }

    /**
     * Remove the specified term.
     */
    public function destroy(Term $term)
    {
        // Prevent deleting active semester
        if ($term->is_current) {
            return redirect()->route('admin.terms.index')
                ->with('error', 'Cannot delete the currently active semester. Please set another semester as active first.');
        }

        // Check if term has results before deleting
        if ($term->results()->count() > 0) {
            return redirect()->route('admin.terms.index')
                ->with('error', 'Cannot delete term with existing results. Historical data is protected.');
        }

        $term->delete();

        return redirect()->route('admin.terms.index')
            ->with('success', 'Semester / Term deleted successfully.');
    }

    /**
     * Toggle term lock status.
     */
    public function toggleLock(Term $term)
    {
        $term->update(['is_locked' => ! $term->is_locked]);

        $status = $term->is_locked ? 'locked (read-only)' : 'unlocked (active for modifications)';
        return redirect()->route('admin.terms.index')
            ->with('success', "Semester is now {$status}.");
    }

    /**
     * Set this term as the active semester for the entire system
     */
    public function setActive(Term $term)
    {
        $term->setAsCurrent();

        return redirect()->route('admin.terms.index')
            ->with('success', "Semester '{$term->academicYear?->year_name} - {$term->term_name}' is now the ACTIVE semester across the entire system.");
    }
}