<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\File;

class FileController extends Controller
{
    // 🔹 Tampilkan halaman daftar file (hanya milik user login)
    public function index(Request $request)
    {
        $query = File::with('uploader')
            ->where('uploaded_by', Auth::id()); // hanya file milik user login

        if ($request->keyword) {
            $query->where('file_name', 'like', '%' . $request->keyword . '%');
        }

        if ($request->type) {
            $query->where('file_type', $request->type);
        }

        $files = $query->orderBy('created_at', 'desc')->get();
        return view('files.index', compact('files'));
    }

    // 🔹 Halaman upload file
    public function create()
    {
        return view('files.upload');
    }

    // 🔹 Simpan file baru
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:20480', // max 20 MB
            'folder_id' => 'nullable|exists:folders,id',
        ]);

        $file = $request->file('file');
        $path = $file->store('uploads/files', 'public');

        File::create([
            'folder_id' => $request->folder_id,
            'uploaded_by' => Auth::id(),
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => strtolower($file->getClientOriginalExtension()),
            'file_size' => $file->getSize(),
            'description' => $request->description,
        ]);

        return back()->with('success', 'File uploaded successfully.');
    }

    // 🔹 Hapus file (hanya jika milik user sendiri)
    public function destroy($id)
    {
        $file = File::where('id', $id)
            ->where('uploaded_by', Auth::id())
            ->firstOrFail();

        if (Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }

        $file->delete();
        return back()->with('success', 'File berhasil dihapus.');
    }

    // 🔹 Rename file (hanya file milik user sendiri)
    public function update(Request $request, $id)
    {
        $request->validate([
            'file_name' => 'required|string|max:255',
        ]);

        $file = File::where('id', $id)
            ->where('uploaded_by', Auth::id())
            ->firstOrFail();

        $file->file_name = $request->file_name;
        $file->save();

        return redirect()->back()->with('success', 'File renamed successfully.');
    }
}