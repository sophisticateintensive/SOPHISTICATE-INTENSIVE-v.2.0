<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Student;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = Notification::with(['admin', 'student.user'])
            ->orderBy('created_at', 'desc');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%")
                  ->orWhereHas('student.user', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('student', function ($q2) use ($search) {
                      $q2->where('reg_number', 'like', "%{$search}%");
                  });
            });
        }

        // Recipient filter
        if ($request->filled('recipient')) {
            if ($request->recipient == 'all') {
                $query->where('is_sent_to_all', true);
            } elseif ($request->recipient == 'individual') {
                $query->where('is_sent_to_all', false);
            }
        }

        // Date filter
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $notifications = $query->paginate(15);

        return view('admin.notifications.index', compact('notifications'));
    }

    public function create(Request $request)
    {
        $students = Student::with('user')->get();
        $selectedStudentId = $request->get('student_id');

        return view('admin.notifications.create', compact('students', 'selectedStudentId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'recipient_type' => 'required|in:all,one',
            'student_id' => 'required_if:recipient_type,one|exists:students,id|nullable',
        ]);

        $notification = new Notification();
        $notification->admin_id = auth()->id();
        $notification->title = $request->title;
        $notification->message = $request->message;

        if ($request->recipient_type === 'all') {
            $notification->is_sent_to_all = true;
            $notification->student_id = null;
        } else {
            $notification->is_sent_to_all = false;
            $notification->student_id = $request->student_id;
        }

        // Set academic_year_id and term_id to null (they're optional in DB)
        $notification->academic_year_id = null;
        $notification->term_id = null;

        $notification->save();

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notification sent successfully!');
    }

    public function show(Notification $notification)
    {
        $notification->load(['student.user', 'admin']);
        return view('admin.notifications.show', compact('notification'));
    }

    public function edit(Notification $notification)
    {
        $students = Student::with('user')->get();
        return view('admin.notifications.edit', compact('notification', 'students'));
    }

    public function update(Request $request, Notification $notification)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'recipient_type' => 'required|in:all,one',
            'student_id' => 'required_if:recipient_type,one|exists:students,id|nullable',
        ]);

        $notification->title = $request->title;
        $notification->message = $request->message;

        if ($request->recipient_type === 'all') {
            $notification->is_sent_to_all = true;
            $notification->student_id = null;
        } else {
            $notification->is_sent_to_all = false;
            $notification->student_id = $request->student_id;
        }

        $notification->save();

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notification updated successfully!');
    }

    public function destroy(Notification $notification)
    {
        $notification->delete();
        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notification deleted successfully.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'notification_ids' => 'required|string',
        ]);

        $ids = explode(',', $request->notification_ids);
        $count = count($ids);
        
        Notification::whereIn('id', $ids)->delete();

        return redirect()->route('admin.notifications.index')
            ->with('success', "{$count} notification(s) deleted successfully.");
    }

    public function clearAll()
    {
        $count = Notification::count();
        Notification::truncate();
        
        return redirect()->route('admin.notifications.index')
            ->with('success', "All {$count} notifications cleared successfully!");
    }

    public function export(Request $request)
    {
        $query = Notification::with(['student.user']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        if ($request->filled('recipient')) {
            if ($request->recipient == 'all') {
                $query->where('is_sent_to_all', true);
            } elseif ($request->recipient == 'individual') {
                $query->where('is_sent_to_all', false);
            }
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $notifications = $query->orderBy('created_at', 'desc')->get();

        $filename = 'notifications_' . date('Y-m-d_His') . '.csv';
        
        $handle = fopen('php://temp', 'w+');
        fwrite($handle, "\xEF\xBB\xBF");
        
        fputcsv($handle, ['ID', 'Title', 'Message', 'Recipient Type', 'Student Name', 'Reg Number', 'Sent Date', 'Status']);
        
        foreach ($notifications as $notification) {
            fputcsv($handle, [
                $notification->id,
                $notification->title,
                $notification->message,
                $notification->is_sent_to_all ? 'All Students' : 'Individual',
                !$notification->is_sent_to_all && $notification->student ? $notification->student->user->name : 'N/A',
                !$notification->is_sent_to_all && $notification->student ? $notification->student->reg_number : 'N/A',
                $notification->created_at->format('Y-m-d H:i:s'),
                $notification->is_read ? 'Read' : 'Unread',
            ]);
        }
        
        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);
        
        return response($csvContent, 200)
            ->header('Content-Type', 'text/csv; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
