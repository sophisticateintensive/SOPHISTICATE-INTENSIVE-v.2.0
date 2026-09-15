<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class BackupController extends Controller
{
    protected BackupService $backupService;

    public function __construct(BackupService $backupService)
    {
        $this->backupService = $backupService;
    }

    /**
     * Display backup dashboard with connection status and backup list.
     */
    public function index()
    {
        $backups = $this->backupService->getBackups();
        $connectionStatus = $this->backupService->testSupabaseConnection();

        return view('admin.backups.index', compact('backups', 'connectionStatus'));
    }

    /**
     * Trigger manual backup and optional Supabase upload.
     */
    public function create(Request $request)
    {
        $format = $request->input('format', 'json');
        $uploadToCloud = $request->boolean('upload_cloud', true);

        $backupResult = $this->backupService->createBackup($format);

        if (!$backupResult['success']) {
            return redirect()->route('admin.backups.index')
                ->with('error', 'Backup failed: ' . ($backupResult['message'] ?? 'Unknown error'));
        }

        $message = "Backup {$backupResult['filename']} generated successfully ({$backupResult['total_rows']} total rows).";

        if ($uploadToCloud) {
            $uploadResult = $this->backupService->uploadToSupabase($backupResult['filepath'], $backupResult['filename']);
            if ($uploadResult['success']) {
                $message .= " Uploaded to Supabase Storage.";
            } else {
                return redirect()->route('admin.backups.index')
                    ->with('warning', $message . " (However, Supabase upload reported: {$uploadResult['message']})");
            }
        }

        return redirect()->route('admin.backups.index')
            ->with('success', $message);
    }

    /**
     * Download a specific backup file.
     */
    public function download(string $filename)
    {
        $filePath = storage_path('app/backups/' . basename($filename));

        if (!File::exists($filePath)) {
            return redirect()->route('admin.backups.index')
                ->with('error', 'Requested backup file not found.');
        }

        return response()->download($filePath);
    }

    /**
     * Upload an existing local backup to Supabase.
     */
    public function uploadExisting(string $filename)
    {
        $filePath = storage_path('app/backups/' . basename($filename));

        if (!File::exists($filePath)) {
            return redirect()->route('admin.backups.index')
                ->with('error', 'Backup file not found locally.');
        }

        $result = $this->backupService->uploadToSupabase($filePath, $filename);

        if ($result['success']) {
            return redirect()->route('admin.backups.index')
                ->with('success', $result['message']);
        }

        return redirect()->route('admin.backups.index')
            ->with('error', $result['message']);
    }

    /**
     * Delete a backup file.
     */
    public function destroy(string $filename)
    {
        $deleted = $this->backupService->deleteBackup($filename);

        if ($deleted) {
            return redirect()->route('admin.backups.index')
                ->with('success', "Backup {$filename} deleted successfully.");
        }

        return redirect()->route('admin.backups.index')
            ->with('error', 'Could not delete backup file.');
    }

    /**
     * Test Supabase connectivity.
     */
    public function testConnection()
    {
        $status = $this->backupService->testSupabaseConnection();

        if ($status['connected']) {
            return redirect()->route('admin.backups.index')
                ->with('success', 'Connected to Supabase Storage API successfully!');
        }

        return redirect()->route('admin.backups.index')
            ->with('error', 'Supabase test failed: ' . $status['message']);
    }
}
