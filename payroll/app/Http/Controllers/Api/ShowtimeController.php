<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Seat;
use App\Models\Showtime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ShowtimeController extends Controller
{
    public function index(){
        $showtime=Showtime::with(['rapchieu.tinhthanh','phim'])->get();
        if($showtime){
            return response()->json([
                'status'=>200,
                'content'=>$showtime
            ],200);
        }
        else{
            return response()->json([
                'status'=>404,
                'message'=>'not found ShowTime'
            ],404);
        }
    }
    public function showbyMovie($id){
        $showtime=Showtime::where('maPhim', $id)->with(['rapchieu','rapchieu.tinhthanh','phim'])->get();
        if($showtime){
            return response()->json([
                'status'=>200,
                'content'=>$showtime
            ]);
        }
        else{
            return response()->json([
                'status'=>404,
                'message'=>'not found movie'
            ]);
        }
    }
    public function show($id){
        $showtime=Showtime::where('maLichChieu',$id)->with(['rapchieu','phim'])->first();
        if($showtime){
            return response()->json([
                'status'=>200,
                'content'=>$showtime
            ],200);
        }
        else
        {
            return response()->json([
                'status'=>404,
                'message'=>'no such show time'
            ],404);
        }
    }
    public function store(Request $request)
{
    // 1. Validate đầu vào
    $validator = Validator::make($request->all(), [
        'ngayChieu' => 'required|date',
        'gioChieu' => [
            'required',
            Rule::unique('showtime')->where(function ($query) use ($request) {
                return $query->where('gioChieu', $request->gioChieu)
                             ->where('ngayChieu', $request->ngayChieu)
                             ->where('maRap', $request->maRap);
            })
        ],
        'giaVeThuong' => 'required|numeric|min:0',
        'giaVeVip' => 'required|numeric|min:0',
        'maPhim' => 'required|exists:movies,maPhim',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 422,
            'message' => 'Dữ liệu không hợp lệ',
            'errors' => $validator->errors()
        ], 422);
    }

    // 2. Tạo mới suất chiếu
    $showtime = Showtime::create([
        'ngayChieu' => $request->ngayChieu,
        'gioChieu' => $request->gioChieu,
        'giaVeThuong' => $request->giaVeThuong,
        'giaVeVip' => $request->giaVeVip,
        'maPhim' => $request->maPhim,
        'maRap' => $request->maRap,
    ]);

    // 3. Sinh ghế nếu tạo thành công
    if ($showtime) {
    $seats = [];
    $hangs = ['A','B','C','D','E','F','G','H','I','K']; // 10 hàng

    foreach ($hangs as $hang) {
        for ($so = 1; $so <= 16; $so++) {
            $tenGhe = $hang . $so;

            if (in_array($hang, ['I', 'K'])) {
                $loaiGhe = 'vip';
                $giaVe = $request->giaVeVip;
            } else {
                $loaiGhe = 'thuong';
                $giaVe = $request->giaVeThuong;
            }

            $seats[] = [
                'tenGhe' => $tenGhe,
                'loaiGhe' => $loaiGhe,
                'giaVe' => $giaVe,
                'daDat' => false,
                'maLichChieu' => $showtime->maLichChieu,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
    }

    // Insert tất cả ghế 1 lần duy nhất
    Seat::insert($seats);
}


    return response()->json([
        'status' => 200,
        'message' => 'Tạo lịch chiếu và ghế thành công',
        'content' => $showtime
    ]);
}
    public function update(Request $request, string $id)
{
    $showtime = Showtime::findOrFail($id);

    $validator = Validator::make($request->all(), [
        'ngayChieu' => 'required|date',
        'gioChieu' => [
            'required',
            Rule::unique('showtime')
                ->where(function ($query) use ($request) {
                    return $query->where('ngayChieu', $request->ngayChieu)
                                 ->where('maRap', $request->maRap);
                })
                ->ignore($id) // loại trừ bản ghi hiện tại
        ],
        'giaVeThuong' => 'required|numeric|min:0',
        'giaVeVip' => 'required|numeric|min:0',
        'maPhim' => 'required|exists:movies,maPhim',
        'maRap' => 'required|exists:raps,maRap',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 422,
            'message' => 'Dữ liệu không hợp lệ',
            'errors' => $validator->errors()
        ], 422);
    }

    $showtime->update([
        'ngayChieu'    => $request->ngayChieu,
        'gioChieu'     => $request->gioChieu,
        'giaVeThuong'  => $request->giaVeThuong,
        'giaVeVip'     => $request->giaVeVip,
        'maPhim'       => $request->maPhim,
        'maRap'        => $request->maRap
    ]);

    return response()->json([
        'status' => 200,
        'message' => 'Showtime successfully updated'
    ], 200);
}
   public function destroy($id){
    $showtime=Showtime::findOrFail($id);
    if($showtime){
        return response()->json([
            'status'=>200,
            'message'=>'Deleted successfully'
        ],200);
    }
    else{
        return response()->json([
            'status'=>404,
            'message'=>'Deleted fail'
        ],404);
    }
   }

    public function getSeatsByShowtime($maLichChieu) {
        $seats = \App\Models\Seat::where('maLichChieu', $maLichChieu)->get();
        if ($seats) {
            return response()->json([
                'status' => 200,
                'content' => $seats
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No seats found'
            ], 404);
        }
    }
}
