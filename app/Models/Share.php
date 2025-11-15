<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Share extends Model
{
    use HasFactory;

    protected $table = 'file_shares';
    public $timestamps = false;

    protected $fillable = [
        'file_id', 'shared_with', 'shared_by', 'permission', 'message'
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function file()
    {
        return $this->belongsTo(File::class, 'file_id');
    }

    public function sender() {
        return $this->belongsTo(User::class, 'shared_by');
    }

    public function receiver() {
        return $this->belongsTo(User::class, 'shared_with');
    }
}