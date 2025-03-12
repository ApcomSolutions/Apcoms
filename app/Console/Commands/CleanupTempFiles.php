<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CleanupTempFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup:temp-files {--hours=24 : Hapus file temp yang lebih tua dari N jam}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membersihkan file-file temporary yang sudah tidak digunakan';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = $this->option('hours');
        $cutoffTime = Carbon::now()->subHours($hours);

        $this->info("Membersihkan file temp yang lebih tua dari {$hours} jam ({$cutoffTime})...");

        // Pastikan folder temp ada
        if (!Storage::disk('public')->exists('temp')) {
            $this->info("Folder temp tidak ditemukan. Tidak ada yang perlu dibersihkan.");
            return;
        }

        $tempFiles = Storage::disk('public')->files('temp');
        $count = 0;
        $errorCount = 0;

        foreach ($tempFiles as $file) {
            try {
                $lastModified = Carbon::createFromTimestamp(
                    Storage::disk('public')->lastModified($file)
                );

                if ($lastModified->lt($cutoffTime)) {
                    Storage::disk('public')->delete($file);
                    $count++;
                    $this->line("Menghapus {$file} (terakhir dimodifikasi: {$lastModified})");
                }
            } catch (\Exception $e) {
                $this->error("Gagal menghapus file {$file}: {$e->getMessage()}");
                Log::error("Gagal menghapus file temp: {$e->getMessage()}", [
                    'file' => $file,
                    'exception' => $e
                ]);
                $errorCount++;
            }
        }

        $this->info("Selesai! {$count} file telah dihapus.");

        if ($errorCount > 0) {
            $this->warn("{$errorCount} file gagal dihapus. Lihat log untuk detail.");
        }
    }
}
