<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    protected $table='comment';
    protected $primaryKey='maComment';
    protected $fillable=[
        'maComment',
        'userId',
        'parent_id',
        'comment',
        'userName',
        'userAvatar',
        'maBaiViet',
        'maPhim',
        'level',
        'time'
    ];
    public function baiViet()
    {
        return $this->belongsTo(TinTuc::class,'maBaiViet','maBaiViet');
    }
    public function tinphim(){
        return $this->hasMany(Movie::class,'maPhim','maPhim');
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function replies(){
        return $this->hasMany(Comment::class,'parent_id');
    }
    public function parent(){
        return $this->belongsTo(Comment::class,'parent_id');
    }
    //tự đọng settimeout khi tạo comment
    protected static function boot(){
        parent::boot();
        static::creating(function($comment){
            $comment->time=Carbon::now();
        });
    }

}
