<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ResourceController extends Controller
{
    public function index(Request $request)
    {
        $query = Resource::with(['uploadedBy', 'subject']);

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('file_name', 'LIKE', "%{$search}%");
            });
        }

        $resources = $query->latest()->paginate(15);
        $resources->appends($request->all());

        $subjects = Subject::orderBy('code')->get();

        return view('admin.resources.index', compact('resources', 'subjects'));
    }

    public function create()
    {
        $subjects = Subject::orderBy('code')->get();
        return view('admin.resources.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'subject_id'   => 'nullable|exists:subjects,id',
            'file'         => 'required|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,txt,zip,rar,png,jpg,jpeg,webp|max:20480', // 20MB whitelist
            'for_students' => 'boolean',
            'description'  => 'nullable|string',
        ]);

        try {
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            
            // Generate unique filename to prevent conflicts
            $uniqueFileName = time() . '_' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            
            // Store in private 'uploads' directory
            $path = $file->storeAs('uploads', $uniqueFileName, 'local');
            
            // Verify file was saved
            if (!Storage::disk('local')->exists($path)) {
                throw new \Exception('File was not saved properly');
            }

            // Create resource record
            $resource = Resource::create([
                'title'        => $validated['title'],
                'subject_id'   => $validated['subject_id'] ?? null,
                'file_name'    => $originalName,
                'file_path'    => $path,
                'file_size'    => $file->getSize(),
                'file_type'    => $file->getMimeType(),
                'for_students' => $request->boolean('for_students', true),
                'description'  => $validated['description'] ?? null,
                'uploaded_by'  => Auth::id(),
            ]);

            // Sync upload to Supabase Cloud Storage
            try {
                $supabase = app(\App\Services\SupabaseStorageService::class);
                if ($supabase->isConfigured()) {
                    $supabase->uploadFile($path, Storage::disk('local')->path($path), 'resources', $file->getMimeType());
                }
            } catch (\Throwable $e) {
                \Log::warning("Supabase resource upload failed: " . $e->getMessage());
            }

            return redirect()->route('admin.resources.index')
                ->with('success', 'Resource uploaded successfully and synced to cloud storage!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Upload failed: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function download(Resource $resource)
    {
        try {
            if (!$resource->fileExists()) {
                \Log::error('File not found for resource ID: ' . $resource->id . ' at path: ' . $resource->file_path);
                return redirect()->back()->with('error', 'File not found. The resource file may be missing.');
            }

            return Storage::disk('local')->download($resource->file_path, $resource->file_name);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Download failed: ' . $e->getMessage());
        }
    }

    public function show(Resource $resource)
    {
        $resource->load(['subject', 'uploadedBy']);
        return view('admin.resources.show', compact('resource'));
    }

    public function edit(Resource $resource)
    {
        $subjects = Subject::orderBy('code')->get();
        return view('admin.resources.edit', compact('resource', 'subjects'));
    }

    public function update(Request $request, Resource $resource)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'subject_id'   => 'nullable|exists:subjects,id',
            'description'  => 'nullable|string',
            'for_students' => 'boolean',
            'file'         => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,txt,zip,rar,png,jpg,jpeg,webp|max:20480',
        ]);

        $resource->title = $validated['title'];
        $resource->subject_id = $validated['subject_id'] ?? null;
        $resource->description = $validated['description'] ?? null;
        $resource->for_students = $request->boolean('for_students', true);

        // Handle new file upload
        if ($request->hasFile('file')) {
            $oldPath = $resource->file_path;
            if ($resource->fileExists()) {
                Storage::disk('local')->delete($oldPath);
            }
            try {
                $supabase = app(\App\Services\SupabaseStorageService::class);
                if ($supabase->isConfigured()) {
                    $supabase->deleteFile($oldPath);
                }
            } catch (\Throwable $e) {
                \Log::warning("Supabase delete old file failed: " . $e->getMessage());
            }
            
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $uniqueFileName = time() . '_' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('uploads', $uniqueFileName, 'local');
            
            $resource->file_name = $originalName;
            $resource->file_path = $path;
            $resource->file_size = $file->getSize();
            $resource->file_type = $file->getMimeType();

            // Sync to Supabase
            try {
                $supabase = app(\App\Services\SupabaseStorageService::class);
                if ($supabase->isConfigured()) {
                    $supabase->uploadFile($path, Storage::disk('local')->path($path), 'resources', $file->getMimeType());
                }
            } catch (\Throwable $e) {
                \Log::warning("Supabase resource upload failed: " . $e->getMessage());
            }
        }

        $resource->save();

        return redirect()->route('admin.resources.index')
            ->with('success', 'Resource updated successfully.');
    }

    public function destroy(Resource $resource)
    {
        $resource->delete();

        return redirect()->route('admin.resources.index')
            ->with('success', 'Resource deleted successfully.');
    }
}
