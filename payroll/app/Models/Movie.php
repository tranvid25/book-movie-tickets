<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;
    protected $table = 'movies';

    protected $primaryKey = 'maPhim';

    protected $fillable = [
        'maPhim',
        'tenPhim',
        'trailer',
        'hinhAnh',
        'fileName',
        'moTa',
        'ngayKhoiChieu',
        'danhGia',
        'hot',
        'dangChieu',
        'sapChieu',
    ];
    public function lichchieu(){
        return $this->hasMany(Showtime::class,'maPhim','maPhim');
    }
}
