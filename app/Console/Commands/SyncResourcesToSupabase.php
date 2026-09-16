<?php

namespace App\Console\Commands;

use App\Models\Resource;
use App\Services\SupabaseStorageService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class SyncResourcesToSupabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resources:sync-supabase';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload all local resource files to Supabase Cloud Storage (resources bucket)';

    /**
     * Execute the console command.
     */
    public function handle(SupabaseStorageService $supabase): int
    {
        $this->info('🚀 Starting Resources Supabase Cloud Sync...');

        if (!$supabase->isConfigured()) {
            $this->error('❌ Supabase credentials are not configured in .env');
            return Command::FAILURE;
        }

        $resources = Resource::all();
        $total = $resources->count();

        if ($total === 0) {
            $this->info('ℹ️ No resources found in the database to sync.');
            return Command::SUCCESS;
        }

        $this->info("📦 Found {$total} resource records. Checking and uploading...");
        $synced = 0;
        $failed = 0;

        foreach ($resources as $resource) {
            $localDiskPath = Storage::disk('local')->path($resource->file_path);

            if (!file_exists($localDiskPath)) {
                $this->warn("⚠️ Local file missing for [{$resource->title}] ({$resource->file_path}) - skipping.");
                continue;
            }

            $this->line("☁️ Uploading [{$resource->title}] ({$resource->file_name})...");
            $result = $supabase->uploadFile(
                $resource->file_path,
                $localDiskPath,
                'resources',
                $resource->file_type
            );

            if ($result['success']) {
                $this->info("   ✅ Uploaded: {$resource->file_name}");
                $synced++;
            } else {
                $this->error("   ❌ Failed: " . ($result['message'] ?? 'Unknown error'));
                $failed++;
            }
        }

        $this->newLine();
        $this->info("🎉 Sync Complete: {$synced} uploaded, {$failed} failed out of {$total} total resources.");

        return Command::SUCCESS;
    }
}