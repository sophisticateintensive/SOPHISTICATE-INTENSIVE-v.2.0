<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
Route::post('/contact', function (\Illuminate\Http\Request $request) {
    $validated = $request->validate([
        'fname'   => 'required|string|max:100',
        'lname'   => 'nullable|string|max:100',
        'email'   => 'required|email|max:150',
        'subject' => 'nullable|string|max:200',
        'message' => 'required|string|max:3000',
    ]);

    try {
        $adminEmail = env('MAIL_FROM_ADDRESS', 'recchirwa@gmail.com');
        \Illuminate\Support\Facades\Mail::to($adminEmail)->send(new \App\Mail\ContactMessageMail($validated));
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Contact form email failed: ' . $e->getMessage());
    }

    return redirect()->to(url('/#contact'))->with('contact_success', 'Thank you! Your message has been received. Our academic team will get back to you shortly.');
})->name('contact.send');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    if (auth()->user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('student.dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update.password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| Admin Routes (Protected by auth)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Academic Years
    Route::resource('academic-years', App\Http\Controllers\Admin\AcademicYearController::class);
    Route::post('/academic-years/{academicYear}/set-current', [App\Http\Controllers\Admin\AcademicYearController::class, 'setCurrent'])->name('academic-years.set-current');

    // Terms
    Route::resource('terms', App\Http\Controllers\Admin\TermController::class);
    Route::post('/terms/{term}/toggle-lock', [App\Http\Controllers\Admin\TermController::class, 'toggleLock'])->name('terms.toggle-lock');
    Route::post('/terms/{term}/set-active', [App\Http\Controllers\Admin\TermController::class, 'setActive'])->name('terms.set-active');

    // Subjects
    Route::resource('subjects', App\Http\Controllers\Admin\SubjectController::class);

    // Students
    Route::get('students/next-reg-number', [App\Http\Controllers\Admin\StudentController::class, 'nextRegNumber'])->name('students.next-reg-number');
    Route::get('students/export', [App\Http\Controllers\Admin\StudentController::class, 'export'])->name('students.export');
    Route::post('students/{student}/toggle-status', [App\Http\Controllers\Admin\StudentController::class, 'toggleAccountStatus'])->name('students.toggle-status');
    Route::resource('students', App\Http\Controllers\Admin\StudentController::class);

    // Results
    Route::get('/results/export', [App\Http\Controllers\Admin\ResultController::class, 'export'])->name('results.export');
    Route::post('/results/bulk-delete', [App\Http\Controllers\Admin\ResultController::class, 'bulkDelete'])->name('results.bulk-delete');
    Route::get('/results/student/{studentId}/subjects', [App\Http\Controllers\Admin\ResultController::class, 'getStudentSubjects'])->name('results.student.subjects');
    Route::resource('results', App\Http\Controllers\Admin\ResultController::class);

    // AJAX Routes for Results
    Route::get('/check-existing-result', [App\Http\Controllers\Admin\ResultController::class, 'checkExistingResult'])->name('results.check');
    Route::get('/get-student-subjects/{studentId}', [App\Http\Controllers\Admin\ResultController::class, 'getStudentSubjects'])->name('results.student-subjects');

    Route::get('/get-terms/{year}', function ($yearId) {
        return \App\Models\Term::where('academic_year_id', $yearId)->get();
    });

    // Fees
    Route::get('fees/export', [App\Http\Controllers\Admin\FeeController::class, 'export'])->name('fees.export');
    Route::resource('fees', App\Http\Controllers\Admin\FeeController::class);
    Route::post('/fees/{fee}/payment', [App\Http\Controllers\Admin\FeeController::class, 'recordPayment'])->name('fees.payment');
    Route::get('/fees/export/all', [App\Http\Controllers\Admin\FeeController::class, 'exportAll'])->name('fees.export.all');

    // Notifications
    Route::resource('notifications', App\Http\Controllers\Admin\NotificationController::class);
    Route::post('/notifications/bulk-delete', [App\Http\Controllers\Admin\NotificationController::class, 'bulkDelete'])->name('notifications.bulk-delete');
    Route::get('/notifications/export', [App\Http\Controllers\Admin\NotificationController::class, 'export'])->name('notifications.export');
    Route::delete('/notifications/clear-all', [App\Http\Controllers\Admin\NotificationController::class, 'clearAll'])->name('notifications.clear-all');
    Route::post('/notifications/{id}/mark-as-read', [App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');

    // Messages
    Route::post('/messages/bulk-status', [App\Http\Controllers\Admin\MessageController::class, 'bulkStatus'])->name('messages.bulk-status');
    Route::post('/messages/bulk-delete', [App\Http\Controllers\Admin\MessageController::class, 'bulkDelete'])->name('messages.bulk-delete');
    Route::resource('messages', App\Http\Controllers\Admin\MessageController::class);

    // Resources
    Route::get('/resources/{resource}/download', [App\Http\Controllers\Admin\ResourceController::class, 'download'])->name('resources.download');
    Route::resource('resources', App\Http\Controllers\Admin\ResourceController::class);

    // Enrollments
    Route::post('enrollments/bulk-enroll', [App\Http\Controllers\Admin\StudentEnrollmentController::class, 'bulkEnroll'])->name('enrollments.bulk-enroll');
    Route::get('enrollments/export', [App\Http\Controllers\Admin\StudentEnrollmentController::class, 'export'])->name('enrollments.export');
    Route::get('enrollments/statistics', [App\Http\Controllers\Admin\StudentEnrollmentController::class, 'statistics'])->name('enrollments.statistics');
    Route::get('enrollments/academic-year/{academicYearId}/term/{termId}/students', [App\Http\Controllers\Admin\StudentEnrollmentController::class, 'getEnrolledStudents'])->name('enrollments.get-students');
    Route::resource('enrollments', App\Http\Controllers\Admin\StudentEnrollmentController::class);

    // Reports
    Route::get('/reports', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/results', [App\Http\Controllers\Admin\ReportController::class, 'exportResults'])->name('reports.export.results');
    Route::get('/reports/export/fees', [App\Http\Controllers\Admin\ReportController::class, 'exportFees'])->name('reports.export.fees');
    Route::get('/reports/export/students', [App\Http\Controllers\Admin\ReportController::class, 'exportStudents'])->name('reports.export.students');

    // Subject Assignment (ORDER MATTERS - specific routes FIRST, then parameter routes)
    Route::get('/new-subject-assignment/export', [App\Http\Controllers\Admin\NewSubjectAssignmentController::class, 'export'])->name('new-subject-assignment.export');
    Route::get('/new-subject-assignment', [App\Http\Controllers\Admin\NewSubjectAssignmentController::class, 'index'])->name('new-subject-assignment.index');
    Route::get('/new-subject-assignment/bulk', [App\Http\Controllers\Admin\NewSubjectAssignmentController::class, 'createBulk'])->name('new-subject-assignment.bulk');
    Route::get('/new-subject-assignment/bulk/create', [App\Http\Controllers\Admin\NewSubjectAssignmentController::class, 'createBulk'])->name('new-subject-assignment.bulk.create');
    Route::post('/new-subject-assignment/bulk', [App\Http\Controllers\Admin\NewSubjectAssignmentController::class, 'bulkAssign'])->name('new-subject-assignment.bulkAssign');

    // ✅ FIXED: Quick assign route – now matches the blade's route name
    Route::post('/new-subject-assignment/quick-assign', [App\Http\Controllers\Admin\NewSubjectAssignmentController::class, 'quickAssign'])->name('new-subject-assignment.quick-assign');

    Route::get('/new-subject-assignment/{student}/summary', [App\Http\Controllers\Admin\NewSubjectAssignmentController::class, 'getSummary'])->name('new-subject-assignment.summary');
    Route::get('/new-subject-assignment/{student}/available-subjects', [App\Http\Controllers\Admin\NewSubjectAssignmentController::class, 'getAvailableSubjects'])->name('new-subject-assignment.availableSubjects');
    Route::post('/new-subject-assignment/{student}/assign', [App\Http\Controllers\Admin\NewSubjectAssignmentController::class, 'assign'])->name('new-subject-assignment.assign');
    Route::patch('/new-subject-assignment/{student}/subjects/{subject}/status', [App\Http\Controllers\Admin\NewSubjectAssignmentController::class, 'updateStatus'])->name('new-subject-assignment.updateStatus');
    Route::delete('/new-subject-assignment/{student}/subjects/{subject}', [App\Http\Controllers\Admin\NewSubjectAssignmentController::class, 'remove'])->name('new-subject-assignment.remove');
    Route::get('/new-subject-assignment/{student}', [App\Http\Controllers\Admin\NewSubjectAssignmentController::class, 'show'])->name('new-subject-assignment.show');

    // Quiz Routes
    Route::resource('quizzes', App\Http\Controllers\Admin\QuizController::class);
    Route::get('quizzes/{quiz}/add-question', [App\Http\Controllers\Admin\QuizController::class, 'addQuestion'])->name('quizzes.add-question');
    Route::post('quizzes/{quiz}/questions', [App\Http\Controllers\Admin\QuizController::class, 'storeQuestion'])->name('quizzes.store-question');
    Route::get('quizzes/{quiz}/questions/{question}/edit', [App\Http\Controllers\Admin\QuizController::class, 'editQuestion'])->name('quizzes.edit-question');
    Route::put('quizzes/{quiz}/questions/{question}', [App\Http\Controllers\Admin\QuizController::class, 'updateQuestion'])->name('quizzes.update-question');
    Route::delete('quizzes/{quiz}/questions/{question}', [App\Http\Controllers\Admin\QuizController::class, 'destroyQuestion'])->name('quizzes.destroy-question');
    Route::get('quizzes/{quiz}/attempts', [App\Http\Controllers\Admin\QuizController::class, 'attempts'])->name('quizzes.attempts');
    Route::get('quizzes/attempts/{attempt}', [App\Http\Controllers\Admin\QuizController::class, 'showAttempt'])->name('quizzes.attempts.show');
    Route::post('quizzes/attempts/{attempt}/grade-question/{question}', [App\Http\Controllers\Admin\QuizController::class, 'gradeQuestion'])->name('quizzes.attempts.grade-question');
    Route::post('quizzes/attempts/{attempt}/reset', [App\Http\Controllers\Admin\QuizController::class, 'resetAttempt'])->name('quizzes.attempts.reset');
    Route::get('quizzes/{quiz}/export-attempts', [App\Http\Controllers\Admin\QuizController::class, 'exportAttempts'])->name('quizzes.export-attempts');
    Route::post('quizzes/{quiz}/toggle-status', [App\Http\Controllers\Admin\QuizController::class, 'toggleStatus'])->name('quizzes.toggle-status');
    Route::post('quizzes/{quiz}/duplicate', [App\Http\Controllers\Admin\QuizController::class, 'duplicate'])->name('quizzes.duplicate');

    // Backups & Cloud Sync (Supabase / Railway)
    Route::get('/backups', [App\Http\Controllers\Admin\BackupController::class, 'index'])->name('backups.index');
    Route::post('/backups/create', [App\Http\Controllers\Admin\BackupController::class, 'create'])->name('backups.create');
    Route::get('/backups/{filename}/download', [App\Http\Controllers\Admin\BackupController::class, 'download'])->name('backups.download');
    Route::post('/backups/{filename}/upload-cloud', [App\Http\Controllers\Admin\BackupController::class, 'uploadExisting'])->name('backups.upload-cloud');
    Route::delete('/backups/{filename}', [App\Http\Controllers\Admin\BackupController::class, 'destroy'])->name('backups.destroy');
    Route::post('/backups/test-connection', [App\Http\Controllers\Admin\BackupController::class, 'testConnection'])->name('backups.test-connection');

    // Timetable Management
    Route::get('/timetable/weekly', [App\Http\Controllers\Admin\TimetableController::class, 'weekly'])->name('timetable.weekly');
    Route::get('/timetable/export', [App\Http\Controllers\Admin\TimetableController::class, 'export'])->name('timetable.export');
    Route::post('/timetable/copy-week', [App\Http\Controllers\Admin\TimetableController::class, 'copyWeek'])->name('timetable.copy-week');
    Route::resource('timetable', App\Http\Controllers\Admin\TimetableController::class);

}); // Close admin group

/*
|--------------------------------------------------------------------------
| Student Routes (Protected by auth)
|--------------------------------------------------------------------------
*/
Route::prefix('student')->name('student.')->middleware(['auth', 'verified', 'student'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Student\DashboardController::class, 'index'])->name('dashboard');

    // Timetable
    Route::get('/timetable', [App\Http\Controllers\Student\TimetableController::class, 'index'])->name('timetable.index');

    // Profile
    Route::get('/profile', [App\Http\Controllers\Student\ProfileController::class, 'edit'])->name('profile');
    Route::patch('/profile', [App\Http\Controllers\Student\ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [App\Http\Controllers\Student\ProfileController::class, 'updatePassword'])->name('profile.update.password');

    // Results
    Route::get('/results', [App\Http\Controllers\Student\ResultController::class, 'index'])->name('results.index');
    Route::get('/results/transcript/download', [App\Http\Controllers\Student\ResultController::class, 'downloadTranscript'])->name('results.transcript');
    Route::get('/results/{result}', [App\Http\Controllers\Student\ResultController::class, 'show'])->name('results.show');

    // Fees
    Route::get('/fees', [App\Http\Controllers\Student\FeeController::class, 'index'])->name('fees.index');

    // Messages
    Route::get('/messages', [App\Http\Controllers\Student\MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/create', [App\Http\Controllers\Student\MessageController::class, 'create'])->name('messages.create');
    Route::post('/messages', [App\Http\Controllers\Student\MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/{message}', [App\Http\Controllers\Student\MessageController::class, 'show'])->name('messages.show');

    // Notifications
    Route::delete('/notifications/clear-all', [App\Http\Controllers\Student\NotificationController::class, 'clearAll'])->name('notifications.clear-all');
    Route::get('/notifications', [App\Http\Controllers\Student\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{notification}', [App\Http\Controllers\Student\NotificationController::class, 'show'])->name('notifications.show');
    Route::delete('/notifications/{notification}', [App\Http\Controllers\Student\NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::post('/notifications/{notification}/mark-as-read', [App\Http\Controllers\Student\NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');

    // Resources
    Route::get('/resources', [App\Http\Controllers\Student\ResourceController::class, 'index'])->name('resources.index');
    Route::get('/resources/{resource}/view', [App\Http\Controllers\Student\ResourceController::class, 'view'])->name('resources.view');
    Route::get('/resources/{resource}/stream', [App\Http\Controllers\Student\ResourceController::class, 'stream'])->name('resources.stream');
    Route::get('/resources/{resource}/download', [App\Http\Controllers\Student\ResourceController::class, 'download'])->name('resources.download');

    // Subjects
    Route::get('/subjects/export', [App\Http\Controllers\Student\CourseController::class, 'export'])->name('subjects.export');
    Route::get('/subjects', [App\Http\Controllers\Student\CourseController::class, 'index'])->name('subjects.index');
    Route::get('/subjects/{subject}', [App\Http\Controllers\Student\CourseController::class, 'show'])->name('subjects.show');

    // Quizzes
    Route::get('quizzes', [App\Http\Controllers\Student\QuizController::class, 'index'])->name('quizzes.index');
    Route::get('quizzes/{quiz}/take', [App\Http\Controllers\Student\QuizController::class, 'take'])->name('quizzes.take');
    Route::post('quizzes/{quiz}/autosave', [App\Http\Controllers\Student\QuizController::class, 'autosave'])->name('quizzes.autosave');
    Route::post('quizzes/{quiz}/submit', [App\Http\Controllers\Student\QuizController::class, 'submit'])->name('quizzes.submit');
    Route::get('quizzes/attempts/{attempt}/result', [App\Http\Controllers\Student\QuizController::class, 'result'])->name('quizzes.result');
    
}); 