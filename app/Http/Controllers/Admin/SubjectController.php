<?php
namespace App\Http\Controllers\Admin;

use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends BaseController
{
    /**
     * Display a listing of subjects.
     */
    public function index()
    {
        $subjects = Subject::orderBy('code')->get();
        return view('admin.subjects.index', compact('subjects'));
    }

    /**
     * Show the form for creating a new subject.
     */
    public function create()
    {
        return view('admin.subjects.create');
    }

    /**
     * Store a newly created subject.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'         => 'required|string|max:255|unique:subjects',
            'name'         => 'required|string|max:255',
            'credit_hours' => 'required|integer|min:1|max:6',
        ]);

        Subject::create($validated);

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Subject created successfully.');
    }

    /**
     * Show the form for editing a subject.
     */
    public function edit(Subject $subject)
    {
        return view('admin.subjects.edit', compact('subject'));
    }

    /**
     * Update the specified subject.
     */
    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'code'         => 'required|string|max:255|unique:subjects,code,' . $subject->id,
            'name'         => 'required|string|max:255',
            'credit_hours' => 'required|integer|min:1|max:6',
        ]);

        $subject->update($validated);

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Subject updated successfully.');
    }

    /**
     * Remove the specified subject.
     */
    public function destroy(Subject $subject)
    {
        // Check if subject has results before deleting
        if ($subject->results()->count() > 0) {
            return redirect()->route('admin.subjects.index')
                ->with('error', 'Cannot delete subject with existing results.');
        }

        $subject->delete();

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Subject deleted successfully.');
    }
}