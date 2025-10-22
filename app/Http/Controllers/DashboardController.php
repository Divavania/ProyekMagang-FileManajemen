<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use App\Models\Folder;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $query = File::query();

        // Filter pencarian
        if ($request->keyword) {
            $query->where('file_name', 'like', '%' . $request->keyword . '%');
        }
        if ($request->type) {
            $query->where('file_type', 'like', '%' . $request->type . '%');
        }
        if ($request->uploader) {
            $query->where('uploaded_by', $request->uploader);
        }

        $files = $query->latest()->get();
        $folders = Folder::latest()->get();
        $uploaders = User::all();

        // Hitung total file berdasarkan jenis
        $totalDocuments = File::where(function ($q) {
            $q->where('file_type', 'like', 'application/%')
              ->orWhere('file_type', 'like', 'text/%');
        })->count();

        $totalImages = File::where('file_type', 'like', 'image/%')->count();
        $totalVideos = File::where('file_type', 'like', 'video/%')->count();

        return view('dashboard', compact(
            'files',
            'folders',
            'uploaders',
            'totalDocuments',
            'totalImages',
            'totalVideos'
        ));
    }
}
