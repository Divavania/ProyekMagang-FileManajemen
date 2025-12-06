<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Notifications\SharedNotification;
use App\Models\User;
use App\Models\File;
use App\Models\Folder;
use function logActivity;

class FileController extends Controller
{
    // Halaman daftar file milik user login
    public function index(Request $request)
    {
        $userId = Auth::id();

        $query = File::with('uploader')
            ->where('uploaded_by', $userId); 

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
        $folders = Folder::where('created_by', $userId)->get(); 

         $viewMode = $request->input('view', 'grid');

        return view('files.index', compact('files', 'users', 'folders', 'viewMode'));
    }

    // Halaman upload file
    public function create()
    {
        $folders = Folder::where('created_by', Auth::id())->get();
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
            'mime_type' => $file->getClientMimeType(),
            'status' => 'Private',
            'description' => $request->description,
        ]);

        return back()->with('success', 'File berhasil diunggah.');
    }

    // Hapus single file
    public function destroy($id)
    {
        $file = File::where('id', $id)->where('uploaded_by', Auth::id())->firstOrFail();

        logActivity(
            "Memindahkan ke sampah",
            "Memindahkan file {$file->file_name} ke sampah"
        );

        $file->delete(); 

        return redirect()->route('files.index')->with('success', 'File berhasil dipindahkan ke sampah.');
    }

    // Bulk delete file
    public function bulkDelete(Request $request)
{
    // Ambil ID yang dikirim dari form
    $ids = $request->input('selected_files', []);

    if (empty($ids)) {
        return redirect()->route('files.index')
            ->with('error', 'Tidak ada file yang dipilih.');
    }

    // Ambil file yang sesuai milik user
    $files = File::whereIn('id', $ids)
        ->where('uploaded_by', Auth::id())
        ->get();

    if ($files->isEmpty()) {
        return redirect()->route('files.index')
            ->with('error', 'Tidak ada file valid untuk dihapus.');
    }

    foreach ($files as $file) {
        $file->delete(); // soft delete
    }

    logActivity(
        "Bulk Delete",
        "Memindahkan ke sampah beberapa file: " . implode(', ', $files->pluck('file_name')->toArray())
    );

    return redirect()->route('files.index')
        ->with('success', 'File terpilih berhasil dipindahkan ke sampah.');
}


    // Update nama file
    public function update(Request $request, $id)
    {
        $request->validate([
            'file_name' => 'required|string|max:255',
        ]);

        $file = File::where('id', $id)
            ->where('uploaded_by', Auth::id())
            ->firstOrFail();

        $extension = pathinfo($file->file_name, PATHINFO_EXTENSION);
        $newName = $request->file_name . '.' . $extension;

        logActivity("Rename File", "Rename dari {$file->file_name} menjadi {$newName}");

        $oldPath = $file->file_path;
        $newPath = dirname($oldPath) . '/' . $newName;

        if (Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->move($oldPath, $newPath);
        }

        $file->update([
            'file_name' => $newName,
            'file_path' => $newPath,
        ]);

        return redirect()->back()->with('success', 'Nama file berhasil diperbarui.');
    }

    // Download file
    public function download($id)
    {
        $file = File::findOrFail($id);
        if (!$file->canAccess(Auth::id())) abort(403);
        $filePath = storage_path('app/public/' . $file->file_path);

       logActivity("Download File", "Mengunduh file {$file->file_name}");


        if (!file_exists($filePath)) {
            return back()->with('error', 'File tidak ditemukan di server.');
        }

        return response()->download($filePath, $file->file_name);
    }

    // Update share settings
    public function updateShare(Request $request, $id)
    {
        $file = File::where('id', $id)->where('uploaded_by', Auth::id())->firstOrFail();
        
        logActivity("Mengubah pengaturan sharing untuk file: {$file->file_name}");

        $type = $request->share_type;
        $file->share_type = $type;
        $file->shared_with = $type === 'selective' ? json_encode($request->shared_with ?: []) : null;
        $file->save();

        return back()->with('success', 'Pengaturan berbagi berhasil diperbarui.');
    }

    // Pindahkan file 
   public function move(Request $request, $id)
    {
        $request->validate([
            'folder_id' => 'required|exists:folders,id'
        ]);

        $file = File::where('id', $id)
                    ->where('uploaded_by', Auth::id())
                    ->firstOrFail();

        // Pastikan folder milik user
        $folder = Folder::where('id', $request->folder_id)
                        ->where('created_by', Auth::id())
                        ->firstOrFail();

        logActivity("Memindahkan File", "Memindahkan file {$file->file_name} ke folder {$folder->folder_name}");

        $file->folder_id = $folder->id;
        $file->save();

        return back()->with('success', 'File berhasil dipindahkan.');
    }

//Share Link File
    public function shareLink($id)
    {
        $file = File::findOrFail($id);

        logActivity("Mengambil share link untuk file: {$file->file_name}");

        $link = asset('storage/' . $file->file_path);

        return response()->json(['link' => $link]);
    }

    // Pindahkan folder via form
    public function moveToFolder(Request $request, File $file)
    {
        $request->validate([
            'folder_id' => 'required|exists:folders,id',
        ]);

        logActivity("File {$file->file_name} dipindahkan ke folder ID {$request->folder_id}");

        $file->folder_id = $request->folder_id;
        $file->save();

        return redirect()->back()->with('success', 'File berhasil dipindahkan.');
    }

    //Notif File Shared
   public function shareFile(Request $request)
    {
        $file = File::findOrFail($request->file_id);
        $receiver = User::findOrFail($request->receiver_id);

        logActivity("Membagikan file {$file->file_name} kepada {$receiver->name}");

        // kirim notifikasi ke user penerima
        $receiver->notify(new FileSharedNotification($file, 'file'));

        return back()->with('success', 'File berhasil dibagikan dan notifikasi dikirim.');
    }
    
    public function updateStatus(Request $request, $id)
    {
        $file = File::findOrFail($id);

        // Cek user non-aktif → tolak
        if(auth()->user()->status === 'Nonaktif'){
            return response()->json([
                'success' => false,
                'message' => 'Akun anda nonaktif. Tidak dapat mengubah status file atau menerima share.'
            ], 403);
        }

        // Jika file berada di folder → tidak boleh ubah
        if($file->folder_id !== null){
            return response()->json([
                'success' => false,
                'message' => 'File ini berasal dari folder. Ubah status folder untuk mempengaruhi file.'
            ], 403);
        }

        // Update status
        $file->status = $request->status;
        $file->save();

        return response()->json([
            'success' => true,
            'new_status' => $file->status
        ]);
    }
}