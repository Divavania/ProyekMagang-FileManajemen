<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\File;
use App\Models\User;
use App\Models\Share;
use App\Notifications\SharedNotification;

class SharedController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // File yang dibagikan oleh user login
        $shared_by = Share::with(['file', 'receiver'])
            ->where('shared_by', $userId)
            ->latest()
            ->get();

        // File yang diterima oleh user login
        $shared_with = Share::with(['file', 'sender'])
            ->where('shared_with', $userId)
            ->latest()
            ->get();

        return view('shared.index', compact('shared_by', 'shared_with'));
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

            if (!$receiver) {
                continue;
            }

            // Simpan data share
            Share::create([
                'file_id' => $file->id,
                'shared_by' => Auth::id(),
                'shared_with' => $receiver->id,
                'permission' => $request->permission,
                'message' => $request->message, // boleh null sekarang
            ]);

            // Kirim notifikasi (via database dan/atau email)
            $receiver->notify(new SharedNotification($file, Auth::user(), $request->message));

            $sharedCount++;
        }

        return back()->with('success', "File berhasil dibagikan ke {$sharedCount} pengguna!");
    }
}