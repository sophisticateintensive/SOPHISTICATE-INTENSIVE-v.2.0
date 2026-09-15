<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class ResourceController extends Controller
{
    public function index()
    {
        $resources = Resource::where('for_students', true)
            ->with(['uploadedBy', 'subject'])
            ->latest()
            ->paginate(15);

        $subjects = \App\Models\Subject::orderBy('code')->get();

        $student = Auth::user()?->student;
        $feePercentage = $student ? $student->feePaymentPercentage() : 0.0;
        $canView = $student ? $student->canViewResources() : false;
        $canDownload = $student ? $student->canDownloadResources() : false;
        $isFullyPaid = $student ? $student->hasFullPaidFees() : false;

        return view('student.resources.index', compact(
            'resources',
            'subjects',
            'canView',
            'canDownload',
            'feePercentage',
            'isFullyPaid'
        ));
    }

    /**
     * Display the secure in-browser resource viewer (no browser download toolbar).
     * Available to students who have made an initial payment (> 0% fees).
     */
    public function view(Resource $resource)
    {
        if (! $resource->for_students) {
            abort(403, 'This resource is not available to students.');
        }

        $student = Auth::user()?->student;
        if (! $student) {
            abort(403, 'Student profile not found.');
        }

        if (! $student->canViewResources()) {
            abort(403, 'Access denied. You must make an initial fee payment to view study resources online.');
        }

        if (! Storage::disk('local')->exists($resource->file_path)) {
            abort(404, 'File not found on storage.');
        }

        $canDownload = $student->canDownloadResources();
        $feePercentage = $student->feePaymentPercentage();

        return view('student.resources.viewer', compact('resource', 'canDownload', 'feePercentage'));
    }

    /**
     * Binary data stream endpoint for the embedded canvas PDF.js reader.
     * Strictly verifies student authorization.
     */
    public function stream(Resource $resource)
    {
        if (! $resource->for_students) {
            abort(403, 'This resource is not available to students.');
        }

        $student = Auth::user()?->student;
        if (! $student) {
            abort(403, 'Student profile not found.');
        }

        if (! $student->canViewResources()) {
            abort(403, 'Access denied. You must make an initial fee payment to view study resources online.');
        }

        if (! Storage::disk('local')->exists($resource->file_path)) {
            abort(404, 'File not found on storage.');
        }

        $fullPath = Storage::disk('local')->path($resource->file_path);
        $mimeType = $resource->file_type ?: (file_exists($fullPath) ? mime_content_type($fullPath) : 'application/pdf');

        return response()->file($fullPath, [
            'Content-Type'           => $mimeType,
            'Content-Disposition'    => 'inline; filename="' . $resource->file_name . '"',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control'          => 'private, no-cache, no-store, must-revalidate',
        ]);
    }

    /**
     * Force-download the file.
     * Only available to students who have paid at least 50% of their fees.
     */
    public function download(Resource $resource)
    {
        if (! $resource->for_students) {
            abort(403, 'This resource is not available to students.');
        }

        $student = Auth::user()?->student;
        if (! $student) {
            abort(403, 'Student profile not found.');
        }

        if (! $student->canDownloadResources()) {
            abort(403, 'Access denied. You must pay at least 50% of your term fees to download resources.');
        }

        if (! Storage::disk('local')->exists($resource->file_path)) {
            abort(404, 'File not found on storage.');
        }

        if (Schema::hasColumn('resources', 'download_count')) {
            $resource->increment('download_count');
        }

        return Storage::disk('local')->download($resource->file_path, $resource->file_name);
    }
}