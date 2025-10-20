<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $totalUsers = User::count();

                // Panggil file resources/views/dashboard.blade.php
                return view('dashboard', compact('user', 'totalUsers'));
    }
}
