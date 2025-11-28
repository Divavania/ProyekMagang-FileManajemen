<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Folder;
use App\Models\User;
use App\Models\FolderShare;
use App\Notifications\SharedNotification;

class FolderShareController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $sort = $request->query('sort', 'latest');

        $shared_by = FolderShare::with(['folder', 'receiver'])
            ->where('shared_by', $userId)
            ->get();

        $shared_with = FolderShare::with(['folder', 'sender'])
            ->where('shared_with', $userId)
            ->get();

        $allShared = $shared_by->merge($shared_with);

        $allShared = $sort === 'oldest'
            ? $allShared->sortBy('created_at')
            : $allShared->sortByDesc('created_at');

        $allShared = $allShared->values();

         // LOG ACTIVITY
        logActivity("Melihat Folder Dibagikan", "User melihat daftar folder yang dibagikan.");

        return view('shared.folders', compact('shared_by', 'shared_with', 'allShared', 'sort'));
    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'email' => 'required|string',
            'permission' => 'required|in:view,edit,download',
            'message' => 'nullable|string'
        ]);

        $folder = Folder::findOrFail($id);
        $emails = array_map('trim', explode(',', $request->email));
        $count = 0;

        foreach ($emails as $email) {
            $receiver = User::where('email', $email)->first();
            if (!$receiver) continue;

            FolderShare::create([
                'folder_id' => $folder->id,
                'shared_by' => Auth::id(),
                'shared_with' => $receiver->id,
                'permission' => $request->permission,
                'message' => $request->message
            ]);
            
            $receiver->notify(new SharedNotification($folder, Auth::user(), 'folder', $request->message));
            $count++;
        }

        // LOG ACTIVITY 
        logActivity(
            "Share Folder",
            "Membagikan folder {$folder->name} ke {$count} pengguna"
        );

        return back()->with('success', "Folder berhasil dibagikan ke {$count} pengguna.");
    }

    public function remove($id)
    {
        $share = FolderShare::findOrFail($id);

        if ($share->shared_by !== Auth::id() && $share->shared_with !== Auth::id()) {
            abort(403);
        }

        $share->delete();

        // LOG ACTIVITY 
        logActivity(
            "Hapus Share Folder",
            "Menghapus akses folder yang dibagikan: {$folderName}"
        );

        return back()->with('success', 'Akses folder dibagikan berhasil dihapus.');
    }
}