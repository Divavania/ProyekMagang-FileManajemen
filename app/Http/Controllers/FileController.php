<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Notifications\FileSharedNotification;
use App\Models\User;
use App\Models\File;
use App\Models\Folder;

class FileController extends Controller
{
    // Halaman daftar file milik user login
    public function index(Request $request)
    {
        $query = File::with('uploader')->where('uploaded_by', Auth::id());

        // Search keyword
        if ($request->keyword) {
            $query->where('file_name', 'like', '%' . $request->keyword . '%');
        }

        // Filter berdasarkan kategori
        if ($request->type && $request->type !== 'semua') {
            $typeMap = [
                'dokumen' => ['pdf','doc','docx','xls','xlsx','txt','ppt','pptx'],
                'gambar'  => ['jpg','jpeg','png','gif','webp','bmp'],
                'video'   => ['mp4','mov','avi','mkv','webm'],
                'audio'   => ['mp3','wav','ogg','flac'],
            ];
            if (isset($typeMap[$request->type])) {
                $query->whereIn('file_type', $typeMap[$request->type]);
            } else {
                $query->where('file_type', $request->type);
            }
        }

        // Sorting
        switch ($request->sort) {
            case 'created_at_asc': $query->orderBy('created_at', 'asc'); break;
            case 'created_at_desc': $query->orderBy('created_at', 'desc'); break;
            case 'name_asc': $query->orderBy('file_name', 'asc'); break;
            case 'name_desc': $query->orderBy('file_name', 'desc'); break;
            case 'size_asc': $query->orderBy('file_size', 'asc'); break;
            case 'size_desc': $query->orderBy('file_size', 'desc'); break;
            default: $query->orderBy('created_at', 'desc');
        }

        $files = $query->get();
        $users = User::all();
        $folders = Folder::all(); // ✨ pastikan tersedia di view

        return view('files.index', compact('files', 'users', 'folders'));
    }

    // Halaman upload file
    public function create()
    {
        $folders = Folder::all();
        return view('files.upload', compact('folders'));
    }

    // Simpan file baru
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:20480',
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

        return back()->with('success', 'File berhasil diunggah.');
    }

    // Hapus single file
    public function destroy($id)
    {
        $file = File::where('id', $id)->where('uploaded_by', Auth::id())->firstOrFail();

        if ($file->file_path && Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }

        $file->delete();
        return redirect()->route('files.index')->with('success', 'File berhasil dihapus.');
    }

    // Bulk delete
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('selected_files', []);
        if (!empty($ids)) {
            $files = File::whereIn('id', $ids)->where('uploaded_by', Auth::id())->get();
            foreach ($files as $f) {
                if ($f->file_path && Storage::disk('public')->exists($f->file_path)) {
                    Storage::disk('public')->delete($f->file_path);
                }
            }
            File::whereIn('id', $ids)->where('uploaded_by', Auth::id())->delete();
            return redirect()->route('files.index')->with('success', 'File terpilih berhasil dihapus.');
        }
        return redirect()->route('files.index')->with('error', 'Tidak ada file yang dipilih.');
    }

    // Update nama file
    public function update(Request $request, $id)
    {
        $request->validate([
            'file_name' => 'required|string|max:255',
        ]);

        $file = File::where('id', $id)->where('uploaded_by', Auth::id())->firstOrFail();
        $file->file_name = $request->file_name;
        $file->save();

        return redirect()->back()->with('success', 'Nama file berhasil diperbarui.');
    }

    // Download file
    public function download($id)
    {
        $file = File::where('id', $id)->where('uploaded_by', Auth::id())->firstOrFail();
        $filePath = storage_path('app/public/' . $file->file_path);

        if (!file_exists($filePath)) {
            return back()->with('error', 'File tidak ditemukan di server.');
        }

        return response()->download($filePath, $file->file_name);
    }

    // Update share settings
    public function updateShare(Request $request, $id)
    {
        $file = File::where('id', $id)->where('uploaded_by', Auth::id())->firstOrFail();
        $type = $request->share_type;
        $file->share_type = $type;
        $file->shared_with = $type === 'selective' ? json_encode($request->shared_with ?: []) : null;
        $file->save();

        return back()->with('success', 'Pengaturan berbagi berhasil diperbarui.');
    }

    // Pindahkan file (AJAX/fetch)
    public function move(Request $request)
    {
        $request->validate([
            'file_id' => 'required|exists:files,id',
            'folder_id' => 'required|exists:folders,id',
        ]);

        $file = File::findOrFail($request->file_id);
        $file->folder_id = $request->folder_id;
        $file->save();

        return response()->json([
            'status' => 'ok',
            'message' => 'File berhasil dipindahkan ke folder baru.'
        ]);
    }

    public function shareLink($id)
{
    $file = File::findOrFail($id);

    // Bisa langsung return URL file yang bisa diakses publik
    // Misal pakai storage link
    $link = asset('storage/' . $file->file_path);

    // Kalau mau ditampilkan di view modal, kita return JSON
    return response()->json(['link' => $link]);
}


    // Pindahkan file (form biasa)
    public function moveToFolder(Request $request, File $file)
    {
        $request->validate([
            'folder_id' => 'required|exists:folders,id',
        ]);

        $file->folder_id = $request->folder_id;
        $file->save();

        return redirect()->back()->with('success', 'File berhasil dipindahkan.');
    }

    //Notif File Shared
   public function shareFile(Request $request)
{
    $file = File::findOrFail($request->file_id);
    $receiver = User::findOrFail($request->receiver_id);

    // kirim notifikasi ke user penerima
    $receiver->notify(new FileSharedNotification($file, 'file'));

    return back()->with('success', 'File berhasil dibagikan dan notifikasi dikirim.');
}
    
}
