<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $fillable=[
        'userId',
        'message',
        'file_path'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'userId');
    }
}
