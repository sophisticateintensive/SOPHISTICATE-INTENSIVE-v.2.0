<?php
namespace App\Http\Controllers\Admin;

use App\Models\AcademicYear;
use Illuminate\Http\Request;

class AcademicYearController extends BaseController
{
    /**
     * Display a listing of academic years.
     */
    public function index()
    {
        $academicYears = AcademicYear::orderBy('created_at', 'desc')->get();
        return view('admin.academic-years.index', compact('academicYears'));
    }

    /**
     * Show the form for creating a new academic year.
     */
    public function create()
    {
        return view('admin.academic-years.create');
    }

    /**
     * Store a newly created academic year.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'year_name' => 'required|string|max:255|unique:academic_years',
        ]);

        AcademicYear::create($validated);

        return redirect()->route('admin.academic-years.index')
            ->with('success', 'Academic year created successfully.');
    }

    /**
     * Show the form for editing an academic year.
     */
    public function edit(AcademicYear $academicYear)
    {
        return view('admin.academic-years.edit', compact('academicYear'));
    }

    /**
     * Update the specified academic year.
     */
    public function update(Request $request, AcademicYear $academicYear)
    {
        $validated = $request->validate([
            'year_name' => 'required|string|max:255|unique:academic_years,year_name,' . $academicYear->id,
        ]);

        $academicYear->update($validated);

        return redirect()->route('admin.academic-years.index')
            ->with('success', 'Academic year updated successfully.');
    }

    /**
     * Remove the specified academic year.
     */
    public function destroy(AcademicYear $academicYear)
    {
        $academicYear->delete();

        return redirect()->route('admin.academic-years.index')
            ->with('success', 'Academic year deleted successfully.');
    }
    /**
     * Set selected academic year as current
     */
    public function setCurrent(AcademicYear $academicYear)
    {
        // Remove current flag from all years
        AcademicYear::query()->update(['is_current' => false]);

        // Set this one as current
        $academicYear->update(['is_current' => true]);

        return redirect()->route('admin.academic-years.index')
            ->with('success', 'Academic year set as current successfully.');
    }
}