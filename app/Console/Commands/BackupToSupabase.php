<?php

namespace App\Console\Commands;

use App\Services\BackupService;
use Illuminate\Console\Command;

class BackupToSupabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup-supabase {--format=json : The backup format (json or sql)} {--local-only : Do not upload to Supabase}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a database backup snapshot and upload it to Supabase cloud storage';

    /**
     * Execute the console command.
     */
    public function handle(BackupService $backupService): int
    {
        $this->info('🚀 Starting database backup process...');

        $format = $this->option('format') ?: 'json';
        $result = $backupService->createBackup($format);

        if (!$result['success']) {
            $this->error('❌ Backup creation failed: ' . ($result['message'] ?? 'Unknown error'));
            return Command::FAILURE;
        }

        $this->info("✅ Local backup created: {$result['filename']} ({$result['total_rows']} total rows across tables)");

        if ($this->option('local-only')) {
            $this->info('ℹ️ --local-only specified. Skipping Supabase cloud upload.');
            return Command::SUCCESS;
        }

        $this->info('☁️ Uploading backup to Supabase Storage...');
        $uploadResult = $backupService->uploadToSupabase($result['filepath'], $result['filename']);

        if ($uploadResult['success']) {
            $this->info('🎉 ' . $uploadResult['message']);
            return Command::SUCCESS;
        }

        $this->warn('⚠️ Supabase upload skipped or failed: ' . $uploadResult['message']);
        $this->info('💡 Local backup is preserved at: ' . $result['filepath']);

        return Command::SUCCESS;
    }
}
