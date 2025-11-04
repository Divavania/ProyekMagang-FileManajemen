<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

// Root redirect ke login
Route::get('/', fn() => redirect()->route('login'));

// 🔐 Auth routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 🔄 Forgot password
Route::get('/forgot-password', fn() => view('auth.forgot-password'))->name('password.request');
Route::post('/forgot-password', function (Request $request) {
    $request->validate(['email' => 'required|email']);
    $status = Password::sendResetLink($request->only('email'));

    return $status === Password::RESET_LINK_SENT
        ? back()->with(['success' => __($status)])
        : back()->with(['error' => __($status)]);
})->name('password.email');

// 🔁 Reset password
Route::get('/reset-password/{token}', fn($token) => view('auth.reset-password', ['token' => $token]))->name('password.reset');
Route::post('/reset-password', function (Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function (User $user, string $password) {
            $user->forceFill([
                'password' => Hash::make($password),
                'remember_token' => Str::random(60),
            ])->save();
        }
    );

    return $status === Password::PASSWORD_RESET
        ? redirect()->route('login')->with('success', __($status))
        : back()->with(['error' => __($status)]);
})->name('password.update');

// 🧭 Dashboard (auth only)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
        Route::post('/profile/update-info', [ProfileController::class, 'updateInfo'])->name('profile.updateInfo');
        Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
        Route::post('/profile/update-photo', [ProfileController::class, 'updatePhoto'])->name('profile.updatePhoto');
        Route::delete('/profile/delete-photo', [ProfileController::class, 'deletePhoto'])->name('profile.deletePhoto');
    });

    // Pastikan ini ada di dalam middleware auth, ya
    Route::middleware(['auth'])->group(function () {
        Route::get('/folders', [FolderController::class, 'index'])->name('folders.index');
        Route::get('/folders/create', [FolderController::class, 'create'])->name('folders.create');
        Route::put('/folders/{id}', [FolderController::class, 'update'])->name('folders.update');
        Route::post('/folders/store', [FolderController::class, 'store'])->name('store.folder');
        Route::post('/upload-folder', [FileController::class, 'storeFolder'])->name('store.upload.folder');
        Route::delete('/folders/{id}', [FolderController::class, 'destroy'])->name('folders.destroy');
        Route::get('/folders/{id}', [FolderController::class, 'show'])->name('folders.show');
        Route::post('/folders', [FolderController::class, 'store'])->name('folders.store');

        Route::get('/upload-file', [FileController::class, 'create'])->name('upload.file');
        Route::post('/upload-file', [FileController::class, 'store'])->name('store.file');
        Route::delete('/delete-file/{id}', [FileController::class, 'destroy'])->name('delete.file');

        Route::get('/files', [FileController::class, 'index'])->name('files.index');
        Route::get('/files/download/{id}', [FileController::class, 'download'])->name('files.download');
        Route::put('/files/{id}', [FileController::class, 'update'])->name('files.update');
        Route::delete('/files/{id}', [FileController::class, 'destroy'])->name('files.destroy');
        // Route::get('/files/{id}', [FileController::class, 'show'])->name('files.show');
        // Route::get('/files/{id}/preview', [FileController::class, 'preview'])->name('files.preview');
        Route::post('/files', [FileController::class, 'store'])->name('files.store');
    });
});