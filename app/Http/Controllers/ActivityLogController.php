<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityLog;
use App\Models\User;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        // Batasi akses untuk SUPERADMIN saja
        if (!auth()->check() || auth()->user()->role !== 'superadmin') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        // Ambil list user untuk filter
        $userList = User::orderBy('name')->get(['id', 'name']);

        // Query log
        $query = ActivityLog::with('user')->orderBy('created_at', 'desc');

        // Filter search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('action', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function ($userQuery) use ($request) {
                      $userQuery->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        // Filter user
        if ($request->filled('user')) {
            $query->where('user_id', $request->user);
        }

        // Filter tanggal
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Pagination
        $logs = $query->paginate(15);

        return view('activity_log.index', compact('logs', 'userList'));
    }
}
