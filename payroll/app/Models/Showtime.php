<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Showtime extends Model
{
    use HasFactory;
    protected $table = 'showtime';

    protected $primaryKey = 'maLichChieu';

    protected $fillable = [
        'maLichChieu',
        'maPhim',
        'maRap',
        'ngayChieu',
        'gioChieu',
        'giaVeThuong',
        'giaVeVip'
    ];
    public function rapChieu()
    {
        return $this->belongsTo(RapChieu::class, 'maRap', 'maRap');
    }
    public function phim()
    {
        return $this->belongsTo(Movie::class, 'maPhim', 'maPhim');
    }
    public function danhSachGhe()
    {
        return $this->hasMany(Seat::class, 'maLichChieu', 'maLichChieu');
    }

}
