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
        'share_type',
        'shared_with',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

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

}