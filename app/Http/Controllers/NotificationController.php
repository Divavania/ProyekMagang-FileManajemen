<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Ambil notifikasi terbaru, paginate
        $notifications = $user->notifications()->latest()->paginate(10);

        // Bisa diolah agar item_type dan item_id tersedia di view
        $notifications->transform(function($notif) {
            $data = $notif->data;
            // item_type: 'file' atau 'folder'
            $data['item_type'] = $data['item_type'] ?? 'file';
            $data['item_id'] = $data['item_id'] ?? null;
            return $notif->setAttribute('item_type', $data['item_type'])
                         ->setAttribute('item_id', $data['item_id']);
        });
        
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
        $user->unreadNotifications->markAsRead();

        return redirect()->back()->with('success', 'Semua notifikasi telah dibaca');
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