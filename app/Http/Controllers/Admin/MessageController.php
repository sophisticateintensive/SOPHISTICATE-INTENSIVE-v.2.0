<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Message;
use App\Models\Student;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Display a listing of messages.
     */
    public function index(Request $request)
    {
        $query = Message::with(['student.user', 'academicYear', 'term']);

        // Filter by academic year
        if ($request->filled('academic_year')) {
            $query->where('academic_year_id', $request->academic_year);
        }

        // Filter by term
        if ($request->filled('term')) {
            $query->where('term_id', $request->term);
        }

        // Filter by status (read/unread)
        if ($request->filled('status')) {
            $isRead = $request->status === 'read';
            $query->where('is_read', $isRead);
        }

        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Search by student name or registration number
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('reg_number', 'LIKE', "%{$search}%")
                    ->orWhereHas('user', function ($user) use ($search) {
                        $user->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        $messages = $query->latest()->paginate(15);

        // Get data for filters
        $academicYears = AcademicYear::all();
        $terms         = Term::with('academicYear')->get();

        return view('admin.messages.index', compact('messages', 'academicYears', 'terms'));
    }

    /**
     * Show the form for creating a new message.
     */
    public function create()
    {
        $students      = Student::with('user')->get();
        $academicYears = AcademicYear::all();
        $terms         = Term::with('academicYear')->get();

        return view('admin.messages.create', compact('students', 'academicYears', 'terms'));
    }

    /**
     * Store a newly created message.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id'       => 'required|exists:students,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'term_id'          => 'required|exists:terms,id',
            'message'          => 'required|string',
        ]);

        Message::create([
            'student_id'       => $validated['student_id'],
            'academic_year_id' => $validated['academic_year_id'],
            'term_id'          => $validated['term_id'],
            'admin_id'         => Auth::id(),
            'message'          => $validated['message'],
            'is_read'          => false,
        ]);

        return redirect()->route('admin.messages.index')
            ->with('success', 'Message sent successfully.');
    }

    /**
     * Display the specified message.
     */
    public function show(Message $message)
    {
        // Mark as read if it's from student to admin
        if (! $message->is_read) {
            $message->update(['is_read' => true]);
        }

        $message->load(['student.user', 'academicYear', 'term']);

        return view('admin.messages.show', compact('message'));
    }

    /**
     * Remove the specified message.
     */
    public function destroy(Message $message)
    {
        $message->delete();

        return redirect()->route('admin.messages.index')
            ->with('success', 'Message deleted successfully.');
    }

    /**
     * Mark message as read.
     */
    public function markAsRead(Message $message)
    {
        $message->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Bulk update message status.
     */
    public function bulkStatus(Request $request)
    {
        $request->validate([
            'message_ids' => 'required|string',
            'action'      => 'required|in:mark_read,mark_unread',
        ]);

        $ids    = explode(',', $request->message_ids);
        $isRead = $request->action === 'mark_read';

        Message::whereIn('id', $ids)->update(['is_read' => $isRead]);

        return redirect()->back()->with('success', 'Messages updated successfully.');
    }

    /**
     * Bulk delete messages.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'message_ids' => 'required|string',
        ]);

        $ids = explode(',', $request->message_ids);
        Message::whereIn('id', $ids)->delete();

        return redirect()->back()->with('success', 'Messages deleted successfully.');
    }

    /**
     * Get messages for a specific student.
     */
    public function getStudentMessages($studentId, Request $request)
    {
        $query = Message::where('student_id', $studentId)
            ->with(['admin', 'academicYear', 'term']);

        if ($request->filled('academic_year')) {
            $query->where('academic_year_id', $request->academic_year);
        }

        if ($request->filled('term')) {
            $query->where('term_id', $request->term);
        }

        $messages = $query->latest()->paginate(20);

        return response()->json($messages);
    }

    /**
     * Get unread message count.
     */
    public function getUnreadCount()
    {
        $count = Message::where('is_read', false)->count();

        return response()->json(['unread_count' => $count]);
    }

    /**
     * Export messages to CSV.
     */
    public function export(Request $request)
    {
        $query = Message::with(['student.user', 'academicYear', 'term']);

        // Apply filters
        if ($request->filled('academic_year')) {
            $query->where('academic_year_id', $request->academic_year);
        }

        if ($request->filled('term')) {
            $query->where('term_id', $request->term);
        }

        if ($request->filled('status')) {
            $isRead = $request->status === 'read';
            $query->where('is_read', $isRead);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('reg_number', 'LIKE', "%{$search}%")
                    ->orWhereHas('user', function ($user) use ($search) {
                        $user->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        $messages = $query->latest()->get();

        $filename = 'messages_export_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($messages) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Headers
            fputcsv($file, [
                'Student Name',
                'Registration Number',
                'Programme',
                'Academic Year',
                'Term',
                'Message',
                'Status',
                'Date Sent',
            ]);

            // Data rows
            foreach ($messages as $message) {
                fputcsv($file, [
                    $message->student->user->name ?? 'N/A',
                    $message->student->reg_number ?? 'N/A',
                    $message->student->programme ?? 'N/A',
                    $message->academicYear->year_name ?? 'N/A',
                    $message->term->term_name ?? 'N/A',
                    $message->message,
                    $message->is_read ? 'Read' : 'Unread',
                    $message->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}