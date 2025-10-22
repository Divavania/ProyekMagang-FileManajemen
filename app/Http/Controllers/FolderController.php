<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Folder;

class FolderController extends Controller
{
    // Menampilkan daftar folder milik user login
    public function index(Request $request)
    {
        $folders = Folder::where(function ($query) {
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
        ]);

        Folder::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id ?? null,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('dashboard')->with('success', 'Folder berhasil dibuat.');
    }

    // Menampilkan isi folder
    public function show($id)
    {
        $folder = Folder::findOrFail($id);
        $subfolders = Folder::where('parent_id', $id)->get();
        $files = \App\Models\File::where('folder_id', $id)->get();

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

        return redirect()->route('folders.index')->with('success', 'Folder berhasil diperbarui.');
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