<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Folder;

class FolderController extends Controller
{
    // Menampilkan daftar folder induk milik user login atau global
    public function index(Request $request)
    {
        $folders = Folder::whereNull('parent_id') // hanya folder induk
            ->where(function ($query) {
                $query->where('created_by', Auth::id())
                    ->orWhereNull('created_by'); 
            })
            ->when($request->search, function($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->latest()
            ->get();

        $allFolders = Folder::all(); // <-- Tambahkan ini

        return view('folders.index', compact('folders', 'allFolders')); // <-- kirim ke view
    }

    // Simpan folder baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:folders,id',
        ]);

        Folder::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Folder created successfully.');
    }

    // Menampilkan isi folder
    public function show($id)
    {
        $folder = Folder::with(['children', 'files'])->findOrFail($id);

        // Ambil subfolder
        $subfolders = $folder->children()->latest()->get();
        $files = $folder->files()->latest()->get();

        $allFolders = Folder::all(); // <-- Tambahkan ini juga

        return view('folders.show', compact('folder', 'subfolders', 'files', 'allFolders'));
    }

    // Tambahkan method helper untuk dashboard
    public function dashboardFolders()
    {
        $folders = Folder::whereNull('parent_id')
            ->where(function($query){
                $query->where('created_by', Auth::id())
                      ->orWhereNull('created_by');
            })
            ->latest()
            ->get();

        $allFolders = Folder::all(); // Supaya dropdown Move bisa muncul

        return view('dashboard', compact('folders', 'allFolders'));
    }

    // Update folder
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $folder = Folder::where('id', $id)
            ->where(function ($query) {
                $query->where('created_by', Auth::id())
                    ->orWhereNull('created_by');
            })
            ->firstOrFail();

        $folder->update([
            'name' => $request->name,
        ]);

        // ✅ Jika folder punya parent, arahkan ke folder induknya
        if ($folder->parent_id) {
            return redirect()->route('folders.show', $folder->parent_id)
                ->with('success', 'Folder berhasil diperbarui.');
        }

        // ✅ Jika berasal dari dashboard (cek referer URL), balik ke sana
        if (str_contains(url()->previous(), route('dashboard'))) {
            return redirect()->route('dashboard')
                ->with('success', 'Folder berhasil diperbarui.');
        }

        // ✅ Default: arahkan ke halaman index folder
        return redirect()->route('folders.index')
            ->with('success', 'Folder berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $folder = Folder::where('id', $id)
            ->where('created_by', Auth::id())
            ->with(['children', 'files']) // ambil relasi supaya bisa dihapus semua
            ->firstOrFail();

        // 🔁 Fungsi rekursif untuk hapus subfolder dan file di dalamnya
        $this->deleteFolderRecursive($folder);

        return back()->with('success', 'Folder dan seluruh isinya berhasil dihapus.');
    }

    private function deleteFolderRecursive($folder)
    {
        // 1️⃣ Hapus semua file di folder ini
        foreach ($folder->files as $file) {
            // hapus dari storage
            if (\Illuminate\Support\Facades\Storage::exists($file->file_path)) {
                \Illuminate\Support\Facades\Storage::delete($file->file_path);
            }
            // hapus dari database
            $file->delete();
        }

        // 2️⃣ Hapus semua subfolder dan isinya
        foreach ($folder->children as $childFolder) {
            $this->deleteFolderRecursive($childFolder);
        }

        // 3️⃣ Hapus folder itu sendiri
        $folder->delete();
    }

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
            // Buat folder di DB
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

            // Folder terakhir di path
            $currentFolderId = $parentId;

            // Simpan file jika ada
            if (!empty($folderData['files'])) {
                foreach ($folderData['files'] as $file) {
                    $filePath = $file->storeAs('uploads', $file->getClientOriginalName(), 'public');

                    \App\Models\File::create([
                        'file_name' => $file->getClientOriginalName(),
                        'file_type' => $file->extension(),
                        'file_path' => $filePath,
                        'folder_id' => $currentFolderId,
                        'uploaded_by' => $userId,
                    ]);
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Folders uploaded successfully!']);
    }

    public function move(Request $request)
    {
        $folder = Folder::findOrFail($request->folder_id);

        // Jangan bisa dipindahkan ke dirinya sendiri
        if($request->parent_id == $folder->id){
            return redirect()->back()->with('error', 'Folder tidak bisa dipindah ke dirinya sendiri.');
        }

        $folder->parent_id = $request->parent_id ?: null; // null = root
        $folder->save();

        return redirect()->back()->with('success', 'Folder berhasil dipindah.');
    }
}