<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    private function checkRole(array $roles)
    {
        $user = auth()->user();
        if (!$user || !in_array($user->role, $roles)) {
            abort(403, 'Unauthorized');
        }
    }

    // Superadmin boleh CRUD semua, admin hanya CRUD user
    private function checkAccess($roleTarget)
    {
        $authRole = auth()->user()->role;

        if ($authRole === 'superadmin') return;

        if ($authRole === 'admin' && $roleTarget === 'user') return;

        abort(403, 'Unauthorized');
    }

    // Tampilkan daftar user sesuai hak akses
    public function index()
    {
        $this->checkRole(['admin', 'superadmin']);

        $users = auth()->user()->role === 'superadmin'
            ? User::orderBy('id', 'desc')->get()
            : User::where('role', 'user')->orderBy('id', 'desc')->get();

        return view('users.index', compact('users'));
    }

    // Form tambah user/admin
    public function create()
    {
        $this->checkRole(['admin', 'superadmin']);
        return view('users.create');
    }

    // Simpan user baru
    public function store(Request $request)
    {
        $this->checkRole(['admin', 'superadmin']);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:5',
            'role' => 'required|in:user,admin,superadmin',
            'status' => 'required|in:aktif,nonaktif',
            'photo' => 'nullable|image|max:2048',
        ]);

        // Batasi admin hanya bisa buat user
        if (auth()->user()->role === 'admin' && $request->role !== 'user') {
            return back()->with('error', 'Admin hanya bisa membuat user biasa.');
        }

        // Simpan user dengan password di-hash dan photo default
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'status' => $request->status,
            'photo' => 'default.png', // default supaya tidak null
        ]);

        // Upload foto jika ada
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('profile_photos', $filename, 'public');
            $user->photo = $path;
            $user->save();
        }

        // LOG ACTIVITY
          logActivity(
            "Tambah User",
            "Admin menambah user baru: {$user->name} ({$user->email}) dengan role {$user->role}"
        );

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan!');
    }

    // Update user
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $this->checkAccess($user->role);
        $this->checkAccess($request->role);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:5',
            'role' => 'required|in:superadmin,admin,user',
            'status' => 'required|in:aktif,nonaktif',
            'photo' => 'nullable|image|max:2048',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->status = $request->status;

        if ($request->password) {
            $user->password = bcrypt($request->password);
        }

        // Upload foto baru jika ada
        if ($request->hasFile('photo')) {
            if ($user->photo && Storage::disk('public')->exists($user->photo) && $user->photo !== 'default.png') {
                Storage::disk('public')->delete($user->photo);
            }

            $file = $request->file('photo');
            $filename = $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('profile_photos', $filename, 'public');
            $user->photo = $path;
        }

        $user->save();

        // LOG ACTIVITY
        $oldName = $user->getOriginal('name');
        $oldEmail = $user->getOriginal('email');
        logActivity(
            "Update User",
            "Mengubah data user dari {$oldName} ({$oldEmail}) menjadi {$user->name} ({$user->email})"
        );

        return redirect()->route('users.index')->with('success', 'User berhasil diupdate!');
    }

    // Hapus user
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        $this->checkAccess($user->role);

        if ($user->photo && Storage::disk('public')->exists($user->photo) && $user->photo !== 'default.png') {
            Storage::disk('public')->delete($user->photo);
        }

        $user->delete();

        // LOG ACTIVITY
          logActivity(
            "Hapus User",
            "Menghapus user: {$name} ({$email})"
        );

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus!');
    }
}