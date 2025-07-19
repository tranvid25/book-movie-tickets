<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\OrderDetail;
use App\Models\Seat;
use App\Models\Showtime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class OrderDetailController extends Controller
{
    public function index(){
     $Order=OrderDetail::all();
     if($Order){
        return response()->json([
            'status'=>200,
            'content'=>$Order
        ]);
     }
     else{
        return response()->json([
            'status'=>404,
            'message'=>'not found Order'
        ]);
     }
    }
    public function show($id){
        $Order=OrderDetail::findOrFail($id);
        if($Order){
            return response()->json([
                'status'=>200,
                'message'=>$Order
            ]);
        }
        else{
            return response()->json([
                'status'=>404,
                'message'=>'not found Order'
            ]);
        }
    }
        public function store(Request $request)
{
    // 1. Validate dữ liệu đầu vào
    $validator = Validator::make($request->all(), [
        'maLichChieu' => 'required|exists:showtime,maLichChieu',
        'danhSachGhe' => 'required|string', // chuỗi dạng: "101,102,103"
        'userId' => 'nullable|exists:users,id',
        'name' => 'required|string|max:255',
        'email' => 'required|email',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 422,
            'message' => 'Dữ liệu không hợp lệ',
            'errors' => $validator->errors()
        ], 422);
    }

    // 2. Lấy thông tin lịch chiếu kèm phim và rạp
    $showtime = Showtime::with(['rapChieu', 'phim'])->where('maLichChieu', $request->maLichChieu)->first();
    if (!$showtime || !$showtime->rapChieu || !$showtime->phim) {
        return response()->json([
            'status' => 404,
            'message' => 'Không tìm thấy thông tin suất chiếu, rạp hoặc phim'
        ], 404);
    }

    // 3. Xử lý danh sách mã ghế
    $arrMaGhe = array_map('intval', explode(',', $request->danhSachGhe));

    // 4. Lấy danh sách ghế và kiểm tra ghế đã được đặt
    $seats = Seat::whereIn('maGhe', $arrMaGhe)
                ->where('maLichChieu', $request->maLichChieu)
                ->get();

    $gheDaDat = $seats->filter(fn($ghe) => $ghe->daDat);

    if ($gheDaDat->count() > 0) {
        return response()->json([
            'status' => 409,
            'message' => 'Một số ghế đã được đặt: ' . $gheDaDat->pluck('tenGhe')->implode(', ')
        ], 409);
    }

    // 5. Tính tổng tiền từ ghế
    $tongTien = $seats->sum('giaVe');
    $danhSachTenGhe = $seats->pluck('tenGhe')->implode(', ');

    // 6. Lưu đơn hàng
    $order = OrderDetail::create([
        'maLichChieu' => $request->maLichChieu,
        'maPhim' => $showtime->maPhim,
        'phim' => $showtime->phim->tenPhim,
        'rapChieu' => $showtime->rapChieu->tenRap,
        'gioChieu' => $showtime->gioChieu,
        'ngayChieu' => $showtime->ngayChieu,
        'danhSachGhe' => $danhSachTenGhe,
        'tongTien' => $tongTien,
        'userId' => $request->userId,
        'name' => $request->name,
        'email' => $request->email,
    ]);

    // 7. Đánh dấu ghế đã đặt
    Seat::whereIn('maGhe', $arrMaGhe)->update([
        'daDat' => 1,
    ]);

    // 8. Gửi email xác nhận
    Mail::send('mail.sendEmail', [
        'rapChieu' => $showtime->rapChieu->tenRap,
        'phim' => $showtime->phim->tenPhim,
        'gioChieu' => $showtime->gioChieu,
        'ngayChieu' => $showtime->ngayChieu,
        'danhSachGhe' => $danhSachTenGhe,
        'tongTien' => $tongTien,
        'name' => $request->name,
        'email' => $request->email,
    ], function ($message) use ($request) {
        $message->to($request->email, $request->name)
                ->subject('PHTV - Thông tin đặt vé');
    });

    // 9. Trả kết quả
    return response()->json([
        'status' => 200,
        'message' => 'Đặt vé thành công!',
        'content' => $order
    ]);
}


    public function showByUser($id)
    {
        $order = OrderDetail::where('userId', $id)->get();
        if ($order) {
            return response()->json([
                'status' => 200,
                'content' => $order
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'no such order found'
            ], 404);
        }
    }


    public function doanhthu($year)
    {
        $order = OrderDetail::whereYear('created_at', $year)->get();
        if ($order->count() >= 0) {
            return response()->json([
                'status' => 200,
                'content' => $order
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'no oder found'
            ], 404);
        }
    }


}
