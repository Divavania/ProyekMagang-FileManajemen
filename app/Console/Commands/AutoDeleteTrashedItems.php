<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\File;
use App\Models\Folder;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AutoDeleteTrashedItems extends Command
{
    protected $signature = 'trash:auto-delete';
    protected $description = 'Hapus permanen file dan folder yang sudah di trash lebih dari 14 hari';

    public function handle()
    {
        $this->info('🗑️  Memulai penghapusan otomatis...');

        $fourteenDaysAgo = Carbon::now()->subDays(14);

        // Hapus file lama
        $deletedFiles = $this->deleteOldFiles($fourteenDaysAgo);
        
        // Hapus folder lama
        $deletedFolders = $this->deleteOldFolders($fourteenDaysAgo);

        $this->info("✅ Berhasil menghapus {$deletedFiles} file dan {$deletedFolders} folder.");
        
        return Command::SUCCESS;
    }

    private function deleteOldFiles($date)
    {
        $files = File::onlyTrashed()
            ->where('deleted_at', '<=', $date)
            ->get();

        $count = 0;

        foreach ($files as $file) {
            if ($file->file_path && Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }
            $file->forceDelete();
            $count++;
        }

        return $count;
    }

    private function deleteOldFolders($date)
    {
        $folders = Folder::onlyTrashed()
            ->where('deleted_at', '<=', $date)
            ->get();

        $count = 0;

        foreach ($folders as $folder) {
            $this->forceDeleteFolderRecursive($folder);
            $count++;
        }

        return $count;
    }

    private function forceDeleteFolderRecursive(Folder $folder)
    {
        foreach ($folder->files()->onlyTrashed()->get() as $file) {
            if ($file->file_path && Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }
            $file->forceDelete();
        }

        foreach ($folder->children()->onlyTrashed()->get() as $child) {
            $this->forceDeleteFolderRecursive($child);
        }

        $folder->forceDelete();
    }
}