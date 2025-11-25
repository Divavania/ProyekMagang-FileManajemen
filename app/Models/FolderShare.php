<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FolderShare extends Model
{
    protected $table = 'folder_shares'; 
    public $timestamps = false; 

    protected $fillable = [
        'folder_id',
        'shared_by',
        'shared_with',
        'permission',
        'message',
    ];

    public function folder()
    {
        return $this->belongsTo(Folder::class, 'folder_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'shared_by');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'shared_with');
    }
}