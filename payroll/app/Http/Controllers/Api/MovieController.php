<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
class MovieController extends Controller
{
    public function search(Request $request){
        $keyword = $request->query('q');
        $movies = Movie::where('tenPhim','like',"%$keyword%")
            ->orWhere('moTa','like',"%$keyword%")
            ->get();
        return response()->json([
            'status' => 200,
            'content' => $movies
        ]);
    }
    public function index(){
        $movie=Movie::all();
        if($movie){
            return response()->json([
                'status'=>200,
                'content'=>$movie
            ]);
        }
        else
        {
            return response()->json([
                'status'=>404,
                'message'=>'not found movie fail'
            ]);
        }
    }
    // lấy chi tiết phim kèm với lịch của nó ở các rạp mọi nơi
    public function show($maPhim)
    {
        $movie = Movie::with(['lichchieu.rapChieu'])->where('maPhim', $maPhim)->first();

        if (!$movie) {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy phim'
            ]);
        }

        return response()->json([
            'status' => 200,
            'content' => $movie
        ]);
    }
    //lấy  chi tiết phim kèm với lịch nó ở các rạp theo thành phố
    public function showCity($maPhim)
{
    $movie = Movie::with(['lichchieu.rapChieu.tinhthanh'])->where('maPhim', $maPhim)->first();

    if (!$movie) {
        return response()->json([
            'status' => 404,
            'message' => 'Không tìm thấy phim'
        ]);
    }

    // Gom theo tỉnh và rạp
    $grouped = [];

    foreach ($movie->lichchieu as $lich) {
        $tenTinh = $lich->rapChieu->tinhthanh->tenTinh ?? 'Không rõ thành phố';
        $tenRap = $lich->rapChieu->tenRap;

        $grouped[$tenTinh][$tenRap][] = [
            'ngayChieu' => $lich->ngayChieu,
            'gioChieu' => $lich->gioChieu,
            'maLichChieu' => $lich->maLichChieu,
        ];
    }

    return response()->json([
        'status' => 200,
        'phim' => [
            'maPhim' => $movie->maPhim,
            'tenPhim' => $movie->tenPhim,
            'moTa' => $movie->moTa,
        ],
        'raps' => $grouped,
    ]);
}


    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'tenPhim' => 'required|string|max:255',
        'trailer' => 'required|string|max:255',
        'hinhAnh' => 'required|file|mimes:jpg,jpeg,png|max:2048',
        'fileName' => 'nullable|string|max:255',
        'moTa' => 'required|string|max:255',
        'ngayKhoiChieu' => 'required|date',
        'danhGia' => 'required|integer|min:0|max:10',
        'hot' => 'required|boolean',
        'dangChieu' => 'required|boolean',
        'sapChieu' => 'required|boolean',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 422,
            'errors' => $validator->errors()
        ]);
    }

    // Xử lý ảnh phim
    $hinhanhUrl = null;
    $imagesName = null;

    if ($request->hasFile('hinhAnh')) {
        $file = $request->file('hinhAnh');
        $imagesName = Str::random(12) . '.' . $file->getClientOriginalExtension();
        $imageDirectory = 'images/movie/';
        $file->move(public_path($imageDirectory), $imagesName);
        $hinhanhUrl = url($imageDirectory . $imagesName);
    }

    Movie::create([
        'tenPhim' => $request->tenPhim,
        'trailer' => $request->trailer,
        'hinhAnh' => $hinhanhUrl,
        'fileName' => $imagesName,
        'moTa' => $request->moTa,
        'ngayKhoiChieu' => $request->ngayKhoiChieu,
        'danhGia' => $request->danhGia,
        'hot' => $request->boolean('hot'),
        'dangChieu' => $request->boolean('dangChieu'),
        'sapChieu' => $request->boolean('sapChieu'),
    ]);

    return response()->json([
        'status' => 200,
        'message' => 'Movie successfully created'
    ]);
}
    public function update(Request $request, string $id)
{
    $movie = Movie::findOrFail($id); // Sẽ tự throw 404 nếu không tồn tại

    $validator = Validator::make($request->all(), [
        'tenPhim' => 'required|string|max:255',
        'trailer' => 'required|string|max:255',
        'hinhAnh' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        'fileName' => 'nullable|string|max:255',
        'moTa' => 'required|string|max:255',
        'ngayKhoiChieu' => 'required|date',
        'danhGia' => 'required|integer|min:0|max:10',
        'hot' => 'required|boolean',
        'dangChieu' => 'required|boolean',
        'sapChieu' => 'required|boolean',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 422,
            'errors' => $validator->errors()
        ]);
    }

    $hinhanhUrl = $movie->hinhAnh;
    $fileName = $movie->fileName;

    if ($request->hasFile('hinhAnh')) {
        $file = $request->file('hinhAnh');
        $imageDirectory = 'images/movie/';

        // Xóa ảnh cũ nếu có
        if ($movie->hinhAnh) {
            $oldPath = public_path(parse_url($movie->hinhAnh, PHP_URL_PATH));
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        $fileName = Str::random(12) . '.' . $file->getClientOriginalExtension();
        $file->move(public_path($imageDirectory), $fileName);
        $hinhanhUrl = url($imageDirectory . $fileName);
    }

    $movie->tenPhim = $request->tenPhim;
    $movie->trailer = $request->trailer;
    $movie->hinhAnh = $hinhanhUrl;
    $movie->fileName = $fileName;
    $movie->moTa = $request->moTa;
    $movie->ngayKhoiChieu = $request->ngayKhoiChieu;
    $movie->danhGia = $request->danhGia;
    $movie->hot = $request->boolean('hot');
    $movie->dangChieu = $request->boolean('dangChieu');
    $movie->sapChieu = $request->boolean('sapChieu');

    $movie->save();

    return response()->json([
        'status' => 200,
        'message' => 'Update movie successfully!',
        'movie' => $movie
    ]);
}

    public function destroy($id){
        $movie=Movie::findOrFail($id);
        if($movie){
            $movie->delete();
            return response()->json([
                'status'=>200,
                'message'=>'deleted successfully'
            ]);
        }
        else{
            return response()->json([
                'status'=>404,
                'message'=>'deleted successfully fails'
            ]);
        }
    }


}
