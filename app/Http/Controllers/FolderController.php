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

        return view('folders.index', compact('folders'));
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

        return view('folders.show', compact('folder', 'subfolders', 'files'));
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

    // Hapus folder
    public function destroy($id)
    {
        $folder = Folder::where('id', $id)
            ->where('created_by', Auth::id())
            ->firstOrFail();

        $folder->delete();

        return back()->with('success', 'Folder berhasil dihapus.');
    }
}