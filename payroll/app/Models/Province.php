<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    use HasFactory;
    protected $table = 'province';

    protected $primaryKey = 'maTinh';

    protected $fillable = [
        'maTinh',
        'tenTinh',
    ];
    public function province(){
        return $this->hasMany(RapChieu::class,'maTinh','maTinh_id');
    }
    //1 tỉnh có nhiều rạp 
}
