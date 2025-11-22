<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'photo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * 🔐 Hash password otomatis ketika password di-set
     */
    public function setPasswordAttribute($value)
    {
        // Jika password sudah ter-hash (bcrypt), jangan hash lagi
        if (!empty($value) && !str_starts_with($value, '$2y$')) {
            $this->attributes['password'] = Hash::make($value);
        } else {
            $this->attributes['password'] = $value;
        }
    }

    /**
     * ❤️ Relasi favorite files
     */
    public function favoriteFiles()
    {
        return $this->belongsToMany(File::class, 'favorites', 'user_id', 'file_id')
                    ->withTimestamps()
                    ->whereNotNull('file_id');
    }

    /**
     * ❤️ Relasi favorite folders
     */
    public function favoriteFolders()
    {
        return $this->belongsToMany(Folder::class, 'favorites', 'user_id', 'folder_id')
                    ->withTimestamps()
                    ->whereNotNull('folder_id');
    }

    /**
     * 🔧 Default otomatis saat membuat user baru
     */
    protected static function booted()
    {
        static::creating(function ($user) {
            if (!$user->role) {
                $user->role = 'user';
            }

            if (!$user->status) {
                $user->status = 'aktif';
            }

            if (!$user->photo) {
                $user->photo = null;
            }
        });
    }
}