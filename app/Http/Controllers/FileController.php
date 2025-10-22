<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\File;
use App\Models\Folder;

class FileController extends Controller
{
    public function create()
    {
        return view('files.upload');
    }

    // Simpan file baru ke dalam folder
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:20480', // max 20MB
            'folder_id' => 'nullable|exists:folders,id',
            'description' => 'nullable|string',
        ]);

        $uploadedFile = $request->file('file'); // HARUS 'file', bukan 'files'
        $path = $uploadedFile->store('uploads/files', 'public');

        File::create([
            'folder_id'   => $request->folder_id,     // cocok dengan kolom folder_id
            'uploaded_by' => Auth::id(),              // cocok dengan uploaded_by
            'file_name'   => $uploadedFile->getClientOriginalName(), // cocok dengan file_name
            'file_path'   => $path,                   // cocok dengan file_path
            'file_type'   => $uploadedFile->getClientMimeType(),     // cocok dengan file_type
            'file_size'   => $uploadedFile->getSize(),               // cocok dengan file_size
            'description' => $request->description,   // cocok dengan description
        ]);

        return redirect()->route('dashboard')->with('success', 'File berhasil diupload.');
    }

    // Hapus file
    public function destroy($id)
    {
        $files = File::where('id', $id)
            ->where('uploaded_by', Auth::id())
            ->firstOrFail();

        Storage::disk('public')->delete($files->file_path); // sesuaikan kolom
        $files->delete();

        return back()->with('success', 'File berhasil dihapus.');
    }

}