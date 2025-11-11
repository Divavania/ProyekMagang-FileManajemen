<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\File;

class TrashController extends Controller
{
    // 🗑️ Tampilkan file yang sudah dihapus (soft delete)
    public function index()
    {
        $trashedFiles = File::onlyTrashed()
            ->where('uploaded_by', Auth::id())
            ->orderBy('deleted_at', 'desc')
            ->get();

        return view('trash.index', compact('trashedFiles'));
    }

    // 🔄 Pulihkan file dari sampah
    public function restore($id)
    {
        $file = File::onlyTrashed()
            ->where('id', $id)
            ->where('uploaded_by', Auth::id())
            ->firstOrFail();

        $file->restore();

        return redirect()->route('trash.index')->with('success', 'File berhasil dipulihkan.');
    }

    // ❌ Hapus permanen file
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

    // 🔄 Pulihkan semua file dari sampah
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

// 🧹 Hapus permanen semua file di sampah
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

}
