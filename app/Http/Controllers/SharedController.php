<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\File;
use App\Models\User;
use App\Models\Share;
use App\Models\FolderShare;
use App\Models\Folder;
use App\Notifications\SharedNotification;

class SharedController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $sort = $request->query('sort', 'latest');

        // FILE
        $shared_file_by = Share::with(['file', 'receiver'])
            ->where('shared_by', $userId)->get()
            ->filter(fn($s) => $s->file);

        $shared_file_with = Share::with(['file', 'sender'])
            ->where('shared_with', $userId)->get()
            ->filter(fn($s) => $s->file);

        // FOLDER
        $shared_folder_by = FolderShare::with(['folder', 'receiver'])
            ->where('shared_by', $userId)->get()
            ->filter(fn($s) => $s->folder);

        $shared_folder_with = FolderShare::with(['folder', 'sender'])
            ->where('shared_with', $userId)->get()
            ->filter(fn($s) => $s->folder);

        // 🔥 Gabungkan semua share (files + folders)
        $allShared = $shared_file_by
            ->merge($shared_file_with)
            ->merge($shared_folder_by)
            ->merge($shared_folder_with);

        // Sortir
        $allShared = $sort === 'oldest'
            ? $allShared->sortBy('created_at')
            : $allShared->sortByDesc('created_at');

        $allShared = $allShared->values(); // reset index

        // 🔥 Ambil user untuk dropdown share/edit share
        $users = User::all();

        logActivity('view_shared_page', 'Melihat halaman file & folder yang dibagikan');

        return view('shared.index', compact(
            'shared_file_by',
            'shared_file_with',
            'shared_folder_by',
            'shared_folder_with',
            'allShared',
            'users',
            'sort'
        ));
    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'email' => 'required|string', // bisa isi lebih dari 1 email dipisah koma
            'permission' => 'required|in:view,edit,download',
            'message' => 'nullable|string'
        ]);

        $file = File::findOrFail($id);
        $emails = array_map('trim', explode(',', $request->email)); // pisahkan dengan koma
        $sharedCount = 0;

        foreach ($emails as $email) {
            $receiver = User::where('email', $email)->first();
            if (!$receiver) continue;

            // Simpan data share
            Share::create([
                'file_id' => $file->id,
                'shared_by' => Auth::id(),
                'shared_with' => $receiver->id,
                'permission' => $request->permission,
                'message' => $request->message, // boleh null sekarang
            ]);

            // Kirim notifikasi (via database dan/atau email)
            $receiver->notify(new SharedNotification( $file, Auth::user(),'file', $request->message));
            $sharedCount++;
        }

        // LOG ACTIVITY
          logActivity(
            "Membagikan File",
            "Membagikan file {$file->file_name} ke {$sharedCount} pengguna dengan permission '{$request->permission}'"
        );

        return back()->with('success', "File berhasil dibagikan ke {$sharedCount} pengguna!");
    }

    public function removeShare($id)
    {
        $share = Share::findOrFail($id);

        // Hanya pengirim atau penerima boleh menghapus
        if ($share->shared_by !== Auth::id() && $share->shared_with !== Auth::id()) {
            abort(403);
        }

        $share->delete(); // hanya delete di DB, file aman

        // LOG ACTIVITY
        
        $fileName = $share->file ? $share->file->file_name : 'Unknown File';
        logActivity("Menghapus Share File", "Menghapus akses share file: {$fileName}");

        return back()->with('success', 'Akses file dibagikan berhasil dihapus.');
    }
}