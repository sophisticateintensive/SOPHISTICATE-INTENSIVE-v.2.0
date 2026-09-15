<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications.
     */
    public function index()
    {
        $student = Auth::user()->student;

        if (! $student) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student profile not found.');
        }

        $notifications = Notification::forStudent($student->id)
            ->latest()
            ->paginate(15);

        return view('student.notifications.index', compact('notifications'));
    }

    /**
     * Display the specified notification.
     */
    public function show(Notification $notification)
    {
        $student = Auth::user()->student;

        // Ensure the notification belongs to the student or was broadcast to all
        if ($notification->student_id !== $student->id && !$notification->is_sent_to_all) {
            abort(403, 'Unauthorized access to this notification.');
        }

        // Mark as read when viewed
        if (! $notification->is_read) {
            $notification->update(['is_read' => true, 'read_at' => now()]);
        }

        return view('student.notifications.show', compact('notification'));
    }

    /**
     * Remove the specified notification.
     */
    public function destroy(Notification $notification)
    {
        $student = Auth::user()->student;

        // Ensure the notification belongs to the student
        if ($notification->student_id !== $student->id && !$notification->is_sent_to_all) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($notification->student_id === $student->id) {
            $notification->delete();
        }

        return redirect()->route('student.notifications.index')
            ->with('success', 'Notification deleted successfully.');
    }

    /**
     * Clear all notifications for the student.
     */
    public function clearAll()
    {
        $student = Auth::user()->student;

        if (! $student) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student profile not found.');
        }

        $deletedCount = Notification::where('student_id', $student->id)->delete();

        return redirect()->route('student.notifications.index')
            ->with('success', "{$deletedCount} notifications cleared successfully.");
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(Notification $notification)
    {
        $student = Auth::user()->student;

        if ($notification->student_id !== $student->id && !$notification->is_sent_to_all) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->update(['is_read' => true, 'read_at' => now()]);

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        $student = Auth::user()->student;

        if (! $student) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student profile not found.');
        }

        Notification::forStudent($student->id)->update(['is_read' => true, 'read_at' => now()]);

        return redirect()->route('student.notifications.index')
            ->with('success', 'All notifications marked as read.');
    }

    /**
     * Get unread notification count (for AJAX).
     */
    public function getUnreadCount()
    {
        $student = Auth::user()->student;

        if (! $student) {
            return response()->json(['unread_count' => 0]);
        }

        $unreadCount = Notification::forStudent($student->id)
            ->where('is_read', false)
            ->count();

        return response()->json(['unread_count' => $unreadCount]);
    }
}