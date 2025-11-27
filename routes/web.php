<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\SharedController;
use App\Http\Controllers\FolderShareController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TrashController;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use Illuminate\Http\Request;
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
Route::get('/reset-password/{token}', function ($token) {
    return view('auth.reset-password', [
        'token' => $token,
        'email' => request()->query('email')
    ]);
})->name('password.reset');

Route::post('/reset-password', function (Request $request) {
    $request->validate([
    'token' => 'required',
    'email' => 'required|email',
    'password' => [
        'required',
        'confirmed',
        'min:8',
        'regex:/[A-Z]/',      // Huruf besar
        'regex:/[a-z]/',      // Huruf kecil
        'regex:/[0-9]/',      // Angka
        'regex:/[@$!%*?&#]/', // Simbol
    ],
], [
    'password.min' => 'Kata sandi minimal harus terdiri dari 8 karakter.',
    'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
    'password.regex' => 'Kata sandi harus mengandung huruf besar, huruf kecil, angka, dan simbol.',
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
        ? redirect()->route('login')->with('success',  'Kata sandi berhasil direset. Silakan login.')
        : back()->with(['error' => 'Token tidak valid atau sudah kedaluwarsa.']);
})->name('password.update');

Route::middleware('auth')->group(function () {

    // Dashboard (auth only)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    //Profile routes
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
        Route::post('/profile/update-info', [ProfileController::class, 'updateInfo'])->name('profile.updateInfo');
        Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
        Route::post('/profile/update-photo', [ProfileController::class, 'updatePhoto'])->name('profile.updatePhoto');
        Route::delete('/profile/delete-photo', [ProfileController::class, 'deletePhoto'])->name('profile.deletePhoto');
    });

    //Folders
        Route::get('/folders', [FolderController::class, 'index'])->name('folders.index');
        Route::get('/folders/create', [FolderController::class, 'create'])->name('folders.create');
        Route::put('/folders/{id}', [FolderController::class, 'update'])->name('folders.update');
        Route::get('/folders/{id}/download', [FolderController::class, 'downloadZip'])->name('folders.downloadZip');
        Route::post('/folders/store', [FolderController::class, 'store'])->name('store.folder');
        Route::post('/upload-folder', [FileController::class, 'storeFolder'])->name('store.upload.folder');
        Route::delete('/folders/{id}', [FolderController::class, 'destroy'])->name('folders.destroy');
        Route::post('/folders', [FolderController::class, 'store'])->name('folders.store');
        Route::get('/folders/{id}', [FolderController::class, 'show'])->name('folders.show');
        Route::post('/folders', [FolderController::class, 'store'])->name('folders.store');

        // API endpoint untuk upload folder
        Route::post('/folders/upload', [App\Http\Controllers\FolderController::class, 'uploadFolderAjax'])->name('folders.upload.ajax');
        Route::post('/folders/move', [FolderController::class, 'move'])->name('folders.move');

        Route::get('/upload-file', [FileController::class, 'create'])->name('upload.file');
        Route::post('/upload-file', [FileController::class, 'store'])->name('store.file');
        Route::delete('/delete-file/{id}', [FileController::class, 'destroy'])->name('delete.file');

        // Files
        Route::get('/files', [FileController::class, 'index'])->name('files.index');
        Route::get('/files/download/{id}', [FileController::class, 'download'])->name('files.download');
        Route::put('/files/{id}', [FileController::class, 'update'])->name('files.update');
        Route::delete('/files/{id}', [FileController::class, 'destroy'])->name('files.destroy');

        // Notification
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/notifications/read/{id}', [NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
        Route::post('/notifications/delete-selected', [NotificationController::class, 'deleteSelected'])->name('notifications.deleteSelected');

        // Favorites
        Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
        Route::post('/favorites/file/{id}', [FavoriteController::class, 'toggleFile'])->name('favorites.toggle.file');
        Route::post('/favorites/folder/{id}', [FavoriteController::class, 'toggleFolder'])->name('favorites.toggle.folder');

        // Shared
        Route::get('/shared', [SharedController::class, 'index'])->name('shared.index');
        Route::post('/files/share/{id}', [SharedController::class, 'store'])->name('files.share');
        Route::delete('/share/{id}', [SharedController::class, 'removeShare'])->name('share.remove');
        Route::get('/logs', [LogController::class, 'index'])->name('logs.index');

        // Folder sharing
        Route::post('/folders/{id}/share', [FolderShareController::class, 'store'])->name('folders.share');
        Route::get('/shared/folders', [FolderShareController::class, 'index'])->name('shared.folders');
        Route::delete('/shared/folders/{id}', [FolderShareController::class, 'remove'])->name('shared.folders.remove');

         //Move
        Route::put('/files/{id}/move', [FileController::class, 'move'])->name('files.move');

        // Trash
    //    Route::get('/trash', [App\Http\Controllers\TrashController::class, 'index'])->name('trash.index');
    //     Route::post('/trash/restore/{id}', [App\Http\Controllers\TrashController::class, 'restore'])->name('trash.restore');
    //     Route::delete('/trash/delete/{id}', [App\Http\Controllers\TrashController::class, 'forceDelete'])->name('trash.forceDelete');
    //     Route::post('/trash/restore-all', [App\Http\Controllers\TrashController::class, 'restoreAll'])->name('trash.restoreAll');
    //     Route::delete('/trash/empty', [App\Http\Controllers\TrashController::class, 'empty'])->name('trash.empty');
    //     Route::delete('/files/bulk-delete', [FileController::class, 'bulkDelete'])->name('files.bulkDelete');
    //     });

        //Trash Files
        Route::get('/trash', [App\Http\Controllers\TrashController::class, 'index'])->name('trash.index');
        Route::post('/trash/restore/{id}', [App\Http\Controllers\TrashController::class, 'restore'])->name('trash.restore');
        Route::delete('/trash/delete/{id}', [App\Http\Controllers\TrashController::class, 'forceDelete'])->name('trash.forceDelete');
        Route::post('/trash/restore-all', [App\Http\Controllers\TrashController::class, 'restoreAll'])->name('trash.restoreAll');
        Route::delete('/trash/empty', [App\Http\Controllers\TrashController::class, 'empty'])->name('trash.empty');
        Route::delete('/files/bulk-delete', [FileController::class, 'bulkDelete'])->name('files.bulkDelete');

        // Trash Folders 
        Route::post('/trash/folders/restore-all', [TrashController::class, 'restoreAllFolders'])->name('trash.folders.restoreAll');
        Route::delete('/trash/folders/empty', [TrashController::class, 'emptyFolders'])->name('trash.folders.empty');
        Route::post('/trash/folders/restore/{id}', [TrashController::class, 'restoreFolder'])->name('trash.folders.restore');
        Route::delete('/trash/folders/delete/{id}', [TrashController::class, 'forceDeleteFolder'])->name('trash.folders.forceDelete');
        Route::get('/trash/folders', [TrashController::class, 'index'])->name('trash.folders');


        // Route::get('/files/{id}', [FileController::class, 'show'])->name('files.show');
        // Route::get('/files/{id}/preview', [FileController::class, 'preview'])->name('files.preview');
        Route::post('/files', [FileController::class, 'store'])->name('files.store');

        // 🟦 User Management (hanya admin/superadmin)
            Route::get('/users', [UserController::class, 'index'])->name('users.index');
            Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
            Route::post('/users/store', [UserController::class, 'store'])->name('users.store');
            Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
            Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

         //ACTIVITY LOG 
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])
    ->name('activity.logs')
    ->middleware('auth');

    
        

    
  
