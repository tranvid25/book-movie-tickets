<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use function PHPUnit\Framework\fileExists;

class BannerController extends Controller
{
    public function index(){
        $banner=Banner::all();
        if($banner){
            return response()->json([
                'status'=>200,
                'content'=>$banner
            ]);
        }
        else{
            return response()->json([
                'status'=>404,
                'message'=>'not found Banner'
            ]);
        }
    }
    public function show(string $id) {
    $banner = Banner::find($id);
    if (!$banner) {
        return response()->json([
            'status' => 404,
            'message' => 'Banner không tồn tại'
        ]);
    }

    return response()->json([
        'status' => 200,
        'content' => $banner
    ]);
}

    public function store(Request $request){
        $validator=Validator::make($request->all(),[
         'duongDan'=>'nullable|string',
         'hinhAnh'=>'nullable|file|mimes:jpg,jpeg,png|max:2048',
         'fileName'=>'nullable|string'
        ]);
        if($validator->fails()){
            return response()->json([
                'status'=>'fail',
                'message'=>$validator->errors()->first(),
                'errors'=>$validator->errors()
            ]);
        }
        $bannerUrl=null;
        if($request->hasFile('hinhAnh')){
            $file=$request->file('hinhAnh');
            $imageName=Str::random(12) . '.' . $file->getClientOriginalExtension();
            $imageDirectory='images/banner/';
            $file->move(public_path($imageDirectory),$imageName);
            $bannerUrl=url($imageDirectory . $imageName);

        Banner::create([
            'duongDan'=>$request->duongDan,
            'hinhAnh'=>$bannerUrl,
            'fileName'=>$imageName
        ]);
        return response()->json([
            'message'=>"Banner successfully created"
        ]);
        }else{
            return response()->json([
                'message'=>"something went really wrong"
            ]);
        }
    }
    public function update(Request $request, string $id) {
    $banner = Banner::find($id);
    if (!$banner) {
        return response()->json([
            'status' => 404,
            'message' => 'Banner không tồn tại'
        ], 404);
    }

    $validator = Validator::make($request->all(), [
        'duongDan' => 'nullable|string|max:255',
        'hinhAnh'=>'nullable|file|mimes:jpg,jpeg,png|max:2048',
        'fileName' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'fail',
            'message' => $validator->errors()->first(),
            'errors' => $validator->errors()
        ], 400);
    }

    $bannerUrl = $banner->hinhAnh;

        if ($request->hasFile('hinhAnh')) {
        $file = $request->file('hinhAnh');
        $imageDirectory = 'images/banner/';

        // Xóa ảnh cũ
        if ($bannerUrl) {
            $oldPath = public_path(parse_url($bannerUrl, PHP_URL_PATH));
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }

        $imageName = Str::random(12) . '.' . $file->getClientOriginalExtension();
        $file->move(public_path($imageDirectory), $imageName);
        $bannerUrl = url($imageDirectory . $imageName);
        $banner->fileName = $imageName;
    }

    $banner->duongDan = $request->duongDan;
    $banner->hinhAnh = $bannerUrl;
    $banner->save();

    return response()->json([
        'status' => 200,
        'message' => 'Cập nhật banner thành công',
        'data' => $banner
    ]);
}

    public function destroy(string $id)
{
    $banner = Banner::find($id);
    if (!$banner) {
        return response()->json([
            'status' => 404,
            'message' => 'Banner không tồn tại'
        ]);
    }

    // Xóa ảnh trong thư mục nếu có
    if ($banner->hinhAnh) {
        $oldPath = public_path(parse_url($banner->hinhAnh, PHP_URL_PATH));
        if (file_exists($oldPath)) {
            @unlink($oldPath);
        }
    }

    $banner->delete();
    return response()->json([
        'status' => 200,
        'message' => 'Xóa thành công'
    ]);
}

}
