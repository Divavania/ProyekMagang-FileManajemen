<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\File;
use App\Models\Folder;
use App\Models\User;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id(); // ambil ID user yang sedang login

        // Query file dengan filter pencarian + user login
        $query = File::with('uploader')->where('uploaded_by', $userId);

        if ($request->keyword) {
            $query->where('file_name', 'like', '%' . $request->keyword . '%');
        }

        if ($request->type) {
            $query->where('file_type', 'like', '%' . $request->type . '%');
        }

        $files = $query->latest()->get();

        // Folder hanya 8 terbaru yang dibuat oleh user ini
        $folders = Folder::where('created_by', $userId)
            ->whereNull('parent_id') // hanya folder induk
            ->latest()
            ->limit(8) // Ambil hanya 8 folder terbaru
            ->get();

        // Ambil semua folder untuk dropdown (pindahkan folder, dll)
        $allFolders = Folder::with('children')
            ->where('created_by', $userId)
            ->get();

        // Uploader list tetap bisa semua user (misal untuk admin)
        $uploaders = User::all();

        // Hitung total berdasarkan ekstensi file milik user ini
        $documentExtensions = ['pdf','doc','docx','txt','xls','xlsx','ppt','pptx'];
        $imageExtensions = ['jpg','jpeg','png','gif','webp','bmp'];
        $videoExtensions = ['mp4','avi','mkv','mov','wmv','flv','webm'];
        $audioExtensions = ['mp3','wav','ogg','m4a','flac'];

        $totalDocuments = File::where('uploaded_by', $userId)
            ->whereIn('file_type', $documentExtensions)
            ->count();

        $totalImages = File::where('uploaded_by', $userId)
            ->whereIn('file_type', $imageExtensions)
            ->count();

        $totalVideos = File::where('uploaded_by', $userId)
            ->whereIn('file_type', $videoExtensions)
            ->count();

        $totalAudios = File::where('uploaded_by', $userId)
            ->whereIn('file_type', $audioExtensions)
            ->count();

        return view('dashboard', compact(
            'files',
            'folders',
            'allFolders', // Tambahkan ini
            'uploaders',
            'totalDocuments',
            'totalImages',
            'totalVideos',
            'totalAudios'
        ));
    }
}