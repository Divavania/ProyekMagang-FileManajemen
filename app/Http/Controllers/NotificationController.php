<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->latest()->paginate(10);

        return view('notifications.index', compact('notifications'));
    }

    // ✅ Tandai satu notifikasi sudah dibaca
    public function markAsRead($id) 
    {
    $notif = auth()->user()->notifications()->findOrFail($id);
    $notif->markAsRead();
   
    return back(); 
    }

    // ✅ Tandai semua notifikasi sudah dibaca
    public function markAllRead()
    {
        $user = Auth::user();
        $user->unreadNotifications->markAllRead();

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }

    public function deleteSelected(Request $request)
    {
        $ids = explode(',', $request->selected_ids);

        if (!empty($ids)) {
            Auth::user()->notifications()->whereIn('id', $ids)->delete();
            return back()->with('success', 'Notifikasi terpilih berhasil dihapus.');
        }

        return back()->with('error', 'Tidak ada notifikasi yang dipilih.');
    }   
}