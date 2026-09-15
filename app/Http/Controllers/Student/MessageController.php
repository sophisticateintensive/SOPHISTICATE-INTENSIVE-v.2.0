<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Message;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $student = Auth::user()->student;

        if (! $student) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student profile not found.');
        }

        // Get the current active enrollment
        $activeEnrollment = $student->activeEnrollment;

        $query = $student->messages()
            ->with(['admin', 'academicYear', 'term']);

        // Filter by academic year
        if ($request->filled('academic_year')) {
            $query->where('academic_year_id', $request->academic_year);
        } elseif ($activeEnrollment && !$request->filled('academic_year') && !$request->filled('term') && !$request->filled('status')) {
            // Default to current enrollment's academic year
            $query->where('academic_year_id', $activeEnrollment->academic_year_id);
        }

        // Filter by term
        if ($request->filled('term')) {
            $query->where('term_id', $request->term);
        } elseif ($activeEnrollment && !$request->filled('academic_year') && !$request->filled('term') && !$request->filled('status')) {
            // Default to current enrollment's term
            $query->where('term_id', $activeEnrollment->term_id);
        }

        // Filter by status (read/unread)
        if ($request->filled('status')) {
            $isRead = $request->status === 'read';
            $query->where('is_read', $isRead);
        }

        $messages = $query->latest()->paginate(15);

        // Get data for filters
        $academicYears = AcademicYear::all();
        $terms = Term::with('academicYear')->get();

        return view('student.messages.index', compact('messages', 'activeEnrollment', 'academicYears', 'terms'));
    }

    public function create()
    {
        $student = Auth::user()->student;
        $activeEnrollment = $student->activeEnrollment;

        return view('student.messages.create', compact('activeEnrollment'));
    }

    public function store(Request $request)
    {
        $student = Auth::user()->student;

        if (! $student) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student profile not found.');
        }

        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        // Get active enrollment for academic year and term
        $activeEnrollment = $student->activeEnrollment;

        Message::create([
            'student_id' => $student->id,
            'academic_year_id' => $activeEnrollment ? $activeEnrollment->academic_year_id : null,
            'term_id' => $activeEnrollment ? $activeEnrollment->term_id : null,
            'admin_id' => null,
            'message' => $validated['message'],
            'is_read' => false,
        ]);

        return redirect()->route('student.messages.index')
            ->with('success', 'Message sent successfully.');
    }

    public function show(Message $message)
    {
        $student = Auth::user()->student;

        // Ensure the message belongs to the logged-in student
        if ($message->student_id !== $student->id) {
            abort(403, 'Unauthorized access to this message.');
        }

        // Mark as read if it's from admin
        if ($message->admin_id && ! $message->is_read) {
            $message->update(['is_read' => true]);
        }

        $message->load(['admin', 'academicYear', 'term']);

        return view('student.messages.show', compact('message'));
    }

    /**
     * Get unread message count for the student
     */
    public function getUnreadCount()
    {
        $student = Auth::user()->student;

        if (!$student) {
            return response()->json(['unread_count' => 0]);
        }

        $unreadCount = $student->messages()
            ->where('is_read', false)
            ->whereNotNull('admin_id')
            ->count();

        return response()->json(['unread_count' => $unreadCount]);
    }

    /**
     * Mark a message as read
     */
    public function markAsRead(Message $message)
    {
        $student = Auth::user()->student;

        if ($message->student_id !== $student->id) {
            abort(403, 'Unauthorized access to this message.');
        }

        $message->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Get messages for a specific academic year and term
     */
    public function getMessagesByPeriod(Request $request)
    {
        $student = Auth::user()->student;

        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $query = $student->messages()->with(['admin', 'academicYear', 'term']);

        if ($request->filled('academic_year')) {
            $query->where('academic_year_id', $request->academic_year);
        }

        if ($request->filled('term')) {
            $query->where('term_id', $request->term);
        }

        $messages = $query->latest()->get();

        return response()->json($messages);
    }
}
