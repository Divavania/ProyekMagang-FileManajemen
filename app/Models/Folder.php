<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class Folder extends Model
{
    use HasFactory, SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'folders';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'parent_id',
        'divisi',
        'status',
        'created_by',
    ];

    public function canAccess($userId)
    {
        if ($this->status === 'Public') return true;
        if ($this->created_by == $userId) return true;

        return $this->shares()->where('shared_with', $userId)->exists();
    }

    public function shares()
    {
        return $this->hasMany(FolderShare::class, 'folder_id');
    }
    // Relasi ke FileItem
    public function files()
    {
        return $this->hasMany(File::class, 'folder_id');
    }

    // Relasi ke User pembuat folder
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function parent()
    {
        return $this->belongsTo(Folder::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Folder::class, 'parent_id');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'folder_id');
    }

    public function isFavoritedBy($userId)
    {
        return $this->favorites()->where('user_id', $userId)->exists();
    }
}