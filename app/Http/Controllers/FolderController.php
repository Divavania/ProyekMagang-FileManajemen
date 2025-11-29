<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Folder;
use App\Models\File;
use ZipArchive;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FolderController extends Controller
{
    // Menampilkan daftar folder induk milik user login atau global
    public function index(Request $request)
    {
        $userId = Auth::id();
        $folders = Folder::whereNull('parent_id')
            ->where('created_by', $userId)
            ->when($request->search, function($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->latest()
            ->get();

       $allFolders = $folders;

        return view('folders.index', compact('folders', 'allFolders')); // <-- kirim ke view
    }

    // Simpan folder baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:folders,id',
        ]);

        $folder = Folder::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'created_by' => auth()->id(),
            'status' => 'Private'
        ]);

       logActivity(
            "Membuat Folder",
            "Membuat folder {$folder->name}"
        );

        return back()->with('success', 'Folder berhasil dibuat.');
    }

    // Menampilkan isi folder
    public function show($id)
    {
        $folder = Folder::with(['children', 'files'])->findOrFail($id);

        if (!$folder->canAccess(Auth::id())) {
            abort(403, 'Akses folder ditolak');
        }
        // Ambil subfolder
        $subfolders = $folder->children()->latest()->get();
        $files = $folder->files()->latest()->get();

        $allFolders = Folder::where('created_by', Auth::id())->get();

        return view('folders.show', compact('folder', 'subfolders', 'files', 'allFolders'));
    }

    // Tambahkan method helper untuk dashboard
    public function dashboardFolders()
    {
        $userId = Auth::id();

        $folders = Folder::whereNull('parent_id')
            ->where(function ($query) use ($userId) {
                $query->where('created_by', $userId) 
                    ->orWhere('status', 'Public')
                    ->orWhereHas('shares', fn($s) =>
                        $s->where('shared_with', $userId)
                    );
            })
            ->latest()
            ->get();

        $allFolders = Folder::all();
        return view('dashboard', compact('folders', 'allFolders'));
    }

    // Update folder
    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //     ]);

    //     $folder = Folder::where('id', $id)
    //         ->where(function ($query) {
    //             $query->where('created_by', Auth::id())
    //                 ->orWhereNull('created_by');
    //         })
    //         ->firstOrFail();

    //     $folder->update([
    //         'name' => $request->name,
    //     ]);

    //     // ✅ Jika folder punya parent, arahkan ke folder induknya
    //     if ($folder->parent_id) {
    //         return redirect()->route('folders.show', $folder->parent_id)
    //             ->with('success', 'Folder berhasil diperbarui.');
    //     }

    //     // ✅ Jika berasal dari dashboard (cek referer URL), balik ke sana
    //     if (str_contains(url()->previous(), route('dashboard'))) {
    //         return redirect()->route('dashboard')
    //             ->with('success', 'Folder berhasil diperbarui.');
    //     }

    //     // ✅ Default: arahkan ke halaman index folder
    //     return redirect()->route('folders.index')
    //         ->with('success', 'Folder berhasil diperbarui.');
    // }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $folder = Folder::findOrFail($id);

        if (!$folder->canAccess(Auth::id())) abort(403);

        $oldName = $folder->name;

         $folder->update(['name' => $request->name]);

       logActivity(
        "Memperbarui Folder",
        "Mengubah nama folder dari {$oldName} menjadi {$folder->name}"
    );

        // Redirect sesuai kondisi
        if ($folder->parent_id) {
            return redirect()->route('folders.show', $folder->parent_id)
                ->with('success', 'Folder berhasil diperbarui.');
        }

        if (str_contains(url()->previous(), route('dashboard'))) {
            return redirect()->route('dashboard')
                ->with('success', 'Folder berhasil diperbarui.');
        }

        return redirect()->route('folders.index')
            ->with('success', 'Folder berhasil diperbarui.');
    }

    // public function destroy($id)
    // {
    //     $folder = Folder::where('id', $id)
    //         ->where('created_by', Auth::id())
    //         ->with(['children', 'files']) // ambil relasi supaya bisa dihapus semua
    //         ->firstOrFail();

    //     // 🔁 Fungsi rekursif untuk hapus subfolder dan file di dalamnya
    //     $this->deleteFolderRecursive($folder);

    //     return back()->with('success', 'Folder dan seluruh isinya berhasil dihapus.');
    // }

    // Hapus folder (soft delete)
    public function destroy($id)
    {
        $folder = Folder::where('id', $id)
            ->where('created_by', Auth::id())
            ->with(['children', 'files'])
            ->firstOrFail();

        $this->deleteFolderRecursive($folder);

        $folderName = $folder->name;

         logActivity(
        "Menghapus Folder",
        "Menghapus folder {$folderName} ke sampah"
    );

        return redirect()->route('trash.folders')
            ->with('success', 'Folder berhasil dipindahkan ke sampah.');
    }

    // private function deleteFolderRecursive($folder)
    // {
    //     // 1️⃣ Hapus semua file di folder ini
    //     foreach ($folder->files as $file) {
    //         // hapus dari storage
    //         if (\Illuminate\Support\Facades\Storage::exists($file->file_path)) {
    //             \Illuminate\Support\Facades\Storage::delete($file->file_path);
    //         }
    //         // hapus dari database
    //         $file->delete();
    //     }

    //     // 2️⃣ Hapus semua subfolder dan isinya
    //     foreach ($folder->children as $childFolder) {
    //         $this->deleteFolderRecursive($childFolder);
    //     }

    //     // 3️⃣ Hapus folder itu sendiri
    //     $folder->delete();
    // }

    private function deleteFolderRecursive($folder)
    {
        foreach ($folder->files as $file) {
            $file->delete();
        }

        foreach ($folder->children as $child) {
            $this->deleteFolderRecursive($child);
        }

        $folder->delete();
    }

    // Move folder
    public function move(Request $request)
    {
        $folder = Folder::where('id', $request->folder_id)
            ->where('created_by', Auth::id())
            ->firstOrFail();

        // Folder tidak bisa pindah ke diri sendiri
        if ($request->parent_id == $folder->id) {
            return back()->with('error', 'Folder tidak bisa dipindah ke dirinya sendiri.');
        }

        // Pastikan folder tujuan milik user login
        if ($request->parent_id) {
            Folder::where('id', $request->parent_id)
                ->where('created_by', Auth::id())
                ->firstOrFail();
        }

        $folder->parent_id = $request->parent_id ?: null; // null = root
        $folder->save();

        logActivity(
            'move_folder',
            "Memindahkan folder {$folder->name} ke parent_id: " . ($request->parent_id ?? 'root')
        );

        return back()->with('success', 'Folder berhasil dipindah.');
    }

    // Upload folder + file (ajax)
    public function uploadFolderAjax(Request $request)
    {
        $request->validate([
            'folders' => 'required|array',
            'folders.*.name' => 'required|string',
            'folders.*.path' => 'nullable|string',
            'folders.*.files' => 'nullable|array',
            'folders.*.files.*' => 'file',
        ]);

        $userId = auth()->id();

        foreach ($request->folders as $folderData) {
            $parentId = null;
            $pathParts = explode('/', $folderData['path'] ?? '');

            foreach ($pathParts as $part) {
                if (!$part) continue;

                $folder = Folder::firstOrCreate([
                    'name' => $part,
                    'parent_id' => $parentId,
                    'created_by' => $userId,
                ]);
                $parentId = $folder->id;
            }

            $currentFolderId = $parentId;

            if (!empty($folderData['files'])) {
                foreach ($folderData['files'] as $file) {
                    $filePath = $file->storeAs('uploads', $file->getClientOriginalName(), 'public');

                    File::create([
                        'file_name' => $file->getClientOriginalName(),
                        'file_type' => $file->extension(),
                        'file_path' => $filePath,
                        'folder_id' => $currentFolderId,
                        'uploaded_by' => $userId,
                    ]);
                }
            }
        }

        logActivity('upload_folder', 'Upload folder menggunakan drag & drop');

        return response()->json(['success' => true, 'message' => 'Folders uploaded successfully!']);
    }

    // public function move(Request $request)
    // {
    //     $folder = Folder::findOrFail($request->folder_id);

    //     // Jangan bisa dipindahkan ke dirinya sendiri
    //     if($request->parent_id == $folder->id){
    //         return redirect()->back()->with('error', 'Folder tidak bisa dipindah ke dirinya sendiri.');
    //     }

    //     $folder->parent_id = $request->parent_id ?: null; // null = root
    //     $folder->save();

    //     return redirect()->back()->with('success', 'Folder berhasil dipindah.');
    // }

    public function downloadZip($id)
    {
        $folder = Folder::with(['children', 'files'])->findOrFail($id);

        if (!$folder->canAccess(Auth::id())) abort(403);

        // Nama zip (tambah timestamp agar unik)
        $zipName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $folder->name) . '_' . time() . '.zip';
        $tmpDir = storage_path('app/tmp');
        if (!file_exists($tmpDir)) mkdir($tmpDir, 0755, true);
        $zipPath = $tmpDir . DIRECTORY_SEPARATOR . $zipName;

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Gagal membuat file zip.');
        }

        // Rekursif tambahkan semua folder & file
        $this->addFolderToZip($folder, $zip);

        $zip->close();

        logActivity('download_folder', 'Download ZIP folder: ' . $folder->name);

        return response()->download($zipPath, $zipName)->deleteFileAfterSend(true);
    }

    /**
     * Tambahkan folder dan semua file/subfolder ke zip secara rekursif
     */
    private function addFolderToZip(Folder $folder, ZipArchive $zip, $parentPath = '')
    {
        $currentPath = $parentPath ? $parentPath . '/' . $folder->name : $folder->name;

        // Tambahkan folder kosong (ZipArchive tidak otomatis menambahkan folder)
        $zip->addEmptyDir($currentPath);

        // Tambahkan file di folder ini
        foreach ($folder->files as $file) {
            if (!$file->canAccess(Auth::id())) continue;
            $zip->addFile(
                storage_path("app/public/{$file->file_path}"),
                "$currentPath/{$file->file_name}"
            );
        }

        // Rekursif ke subfolder
        foreach ($folder->children as $child) {
            $this->addFolderToZip($child, $zip, $currentPath);
        }
        
    }

    public function updateFolderStatus(Request $request, $id)
    {
        $folder = Folder::findOrFail($id);

        if ($folder->created_by != Auth::id()) abort(403);

        $newStatus = $request->status ?? ($folder->status === 'Private' ? 'Public' : 'Private');

        \DB::transaction(function () use ($folder, $newStatus) {
            $folder->update(['status' => $newStatus]);

            $files = $this->collectAllFiles($folder);

            foreach ($files as $file) {
                $file->update(['status' => $newStatus]);

                if ($newStatus === 'Public') {
                    $file->shares()->delete();
                }
            }
        });

        return back()->with('success', "Folder berhasil diperbarui menjadi {$newStatus}");
    }

    private function collectAllFiles(Folder $folder)
    {
        $files = $folder->files()->get();

        foreach ($folder->children as $child) {
            $files = $files->merge($this->collectAllFiles($child));
        }

        return $files;
    }
}