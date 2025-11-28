<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\File;
use App\Models\Folder;

class TrashController extends Controller
{
    // Tampilkan file yang sudah dihapus (soft delete)
    public function index()
    {
        $trashedFiles = File::onlyTrashed()
            ->where('uploaded_by', Auth::id())
            ->orderBy('deleted_at', 'desc')
            ->get();

        $trashedFolders = Folder::onlyTrashed()
            ->where('created_by', Auth::id())
            ->orderBy('deleted_at', 'desc')
            ->get();

        return view('trash.index', compact('trashedFiles', 'trashedFolders'));
    }

    // Pulihkan file dari sampah
    public function restore($id)
    {
        $file = File::onlyTrashed()
            ->where('id', $id)
            ->where('uploaded_by', Auth::id())
            ->firstOrFail();

        $file->restore();

        return redirect()->route('trash.index')->with('success', 'File berhasil dipulihkan.');
    }

    // Hapus permanen file
    public function forceDelete($id)
    {
        $file = File::onlyTrashed()
            ->where('id', $id)
            ->where('uploaded_by', Auth::id())
            ->firstOrFail();

        // hapus file fisiknya dari storage
        if ($file->file_path && Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }

        $file->forceDelete();

        return redirect()->route('trash.index')->with('success', 'File berhasil dihapus permanen.');
    }

    // Pulihkan semua file dari sampah
    public function restoreAll()
    {
        $files = File::onlyTrashed()->where('uploaded_by', Auth::id())->get();

        if ($files->isEmpty()) {
            return redirect()->route('trash.index')->with('error', 'Tidak ada file untuk dipulihkan.');
        }

        foreach ($files as $file) {
            $file->restore();
        }

        return redirect()->route('trash.index')->with('success', 'Semua file berhasil dipulihkan.');
    }

    // Hapus permanen semua file di sampah
    public function empty()
    {
        $files = File::onlyTrashed()->where('uploaded_by', Auth::id())->get();

        if ($files->isEmpty()) {
            return redirect()->route('trash.index')->with('error', 'Tidak ada file di sampah.');
        }

        foreach ($files as $file) {
            if ($file->file_path && Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }
            $file->forceDelete();
        }

        return redirect()->route('trash.index')->with('success', 'Semua file di sampah telah dihapus permanen.');
    }

    // Pulihkan folder
    public function restoreFolder($id)
    {
        $folder = Folder::onlyTrashed()
            ->where('id', $id)
            ->where('created_by', Auth::id())
            ->firstOrFail();

        $this->restoreFolderRecursive($folder);

        return back()->with('success', 'Folder berhasil dipulihkan.');
    }

    // Recursive restore folder + subfolder + files
    private function restoreFolderRecursive(Folder $folder)
    {
        $folder->restore();

        foreach (Folder::onlyTrashed()->where('parent_id', $folder->id)->get() as $child) {
            $this->restoreFolderRecursive($child);
        }

        foreach (File::onlyTrashed()->where('folder_id', $folder->id)->get() as $file) {
            $file->restore();
        }
    }

    // Hapus permanen folder
    public function forceDeleteFolder($id)
    {
        $folder = Folder::onlyTrashed()
            ->where('id', $id)
            ->where('created_by', Auth::id())
            ->with(['children', 'files'])
            ->firstOrFail();

        $this->forceDeleteFolderRecursive($folder);

        return back()->with('success', 'Folder dihapus permanen.');
    }

    // Recursive force delete folder + subfolder + files
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

    // 🔄 Pulihkan semua folder
    public function restoreAllFolders()
    {
        $folders = Folder::onlyTrashed()->where('created_by', Auth::id())->get();

        if ($folders->isEmpty()) {
            return back()->with('error', 'Tidak ada folder untuk dipulihkan.');
        }

        foreach ($folders as $folder) {
            $this->restoreFolderRecursive($folder);
        }

        return back()->with('success', 'Semua folder berhasil dipulihkan.');
    }

    // 🧹 Hapus permanen semua folder
    public function emptyFolders()
    {
        $folders = Folder::onlyTrashed()->where('created_by', Auth::id())->get();

        if ($folders->isEmpty()) {
            return back()->with('error', 'Tidak ada folder di sampah.');
        }

        foreach ($folders as $folder) {
            $this->forceDeleteFolderRecursive($folder);
        }

        return back()->with('success', 'Semua folder di sampah telah dihapus permanen.');
    }

}
