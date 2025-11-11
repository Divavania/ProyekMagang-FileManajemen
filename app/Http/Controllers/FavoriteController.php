<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Favorite;
use App\Models\File;
use App\Models\Folder;

class FavoriteController extends Controller
{
    // 🔸 Toggle File
    public function toggleFile($id)
    {
        $user = Auth::user();
        $favorite = Favorite::where('user_id', $user->id)
            ->where('file_id', $id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(['status' => 'ok', 'favorited' => false]);
        } else {
            Favorite::create([
                'user_id' => $user->id,
                'file_id' => $id,
            ]);
            return response()->json(['status' => 'ok', 'favorited' => true]);
        }
    }

    // 🔸 Toggle Folder
    public function toggleFolder($id)
    {
        $user = Auth::user();
        $favorite = Favorite::where('user_id', $user->id)
            ->where('folder_id', $id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(['status' => 'ok', 'favorited' => false]);
        } else {
            Favorite::create([
                'user_id' => $user->id,
                'folder_id' => $id,
            ]);
            return response()->json(['status' => 'ok', 'favorited' => true]);
        }
    }

    // 🔸 Halaman Favorit
    public function index()
    {
        $user = Auth::user();

        $favorites = Favorite::with(['file', 'folder'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return view('favorites.index', compact('favorites'));
    }
}
