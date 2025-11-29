<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'files';
    protected $primaryKey = 'id';

    protected $fillable = [
        'folder_id',
        'uploaded_by',
        'divisi',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'mime_type',
        'status',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function canAccess($userId)
    {
        if ($this->status === 'Public') return true;
        if ($this->uploaded_by == $userId) return true;

        return $this->shares()->where('shared_with', $userId)->exists();
    }

    public function shares()
    {
        return $this->hasMany(Share::class, 'file_id');
    }

    // Relasi manual ke user
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    //Favorit
    public function favorites()
{
    return $this->hasMany(Favorite::class, 'file_id');
}

public function isFavoritedBy($userId)
{
    return $this->favorites()->where('user_id', $userId)->exists();
}

public function folder() 
{
    return $this->belongsTo(Folder::class, 'folder_id');
}

}