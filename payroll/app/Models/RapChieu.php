<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RapChieu extends Model
{
    use HasFactory;
    protected $table = 'rapchieu';

    protected $primaryKey = 'maRap';

    protected $fillable = [
        'maRap',
        'tenRap',
        'diaChi',
        'maTinh_id',
    ];
    public function tinhThanh()
    {
        return $this->belongsTo(Province::class,'maTinh_id','maTinh');
    }
    //1 rap chiếu thì chỉ thuộc về 1 tỉnh thành
    public function lichchieu(){
        return $this->hasMany(Showtime::class,'maRap','maRap');
    }
}
