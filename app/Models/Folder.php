<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Folder extends Model
{
    use HasFactory;

    protected $table = 'folders';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'parent_id',
        'created_by',
    ];

    // Relasi ke FileItem
    public function files()
    {
        return $this->hasMany(FileItem::class, 'folder_id');
    }

    // Relasi ke User pembuat folder
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}