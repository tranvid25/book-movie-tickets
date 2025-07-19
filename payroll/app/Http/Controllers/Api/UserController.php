<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $user = User::all();
        if ($user) {
            return response()->json([
                'status' => 200,
                'content' => $user
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'no user found'
            ], 404);
        }
    }
    public function show(string $id){
        $user=User::findOrFail($id);
        if($user){
            return response()->json([
                'status'=>200,
                'content'=>$user
            ]);
        }
        else{
            return response()->json([
                'status'=>404,
                'message'=>'no user found'
            ]);
        }
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required',
            'avatar' => 'nullable|file|mimes:jpg,jpeg,png|max:2048'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ]);
        }
        //xử lý avatar
        $avatarUrl = null;
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $imageName = Str::random(12) . '.' . $file->getClientOriginalExtension();
            $imageDirectory = 'images/avatar/';
            $file->move(public_path($imageDirectory), $imageName);
            $avatarUrl = url($imageDirectory . $imageName);
        }
        $user=User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
            'role'=>$request->role,
            'avatar'=>$avatarUrl
        ]);
        return response()->json([
            'status'=>200,
            'message'=>'Người dùng đã được tạo thành công',
            'user'=>$user
        ],200);
    }
    public function update(Request $request, string $id)
{
    $user = User::find($id);
    if (!$user) {
        return response()->json([
            'status' => 404,
            'message' => 'Người dùng không tồn tại'
        ], 404);
    }

    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|unique:users,email,' . $id,
        'password' => 'nullable|string|min:6',
        'role' => 'required|string',
        'avatar' => 'nullable|file|mimes:jpg,jpeg,png|max:2048'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'fail',
            'message' => $validator->errors()->first(),
            'errors' => $validator->errors()
        ], 400);
    }

    // Xử lý avatar
    $avatarUrl = $user->avatar;
    if ($request->hasFile('avatar')) {
        $file = $request->file('avatar');
        $imageDirectory = 'images/avatar/';

        // Xóa ảnh cũ nếu có
        if ($user->avatar) {
            $oldPath = public_path(parse_url($user->avatar, PHP_URL_PATH));
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        $imageName = Str::random(12) . '.' . $file->getClientOriginalExtension();
        $file->move(public_path($imageDirectory), $imageName);
        $avatarUrl = url($imageDirectory . $imageName);
    }

    // Cập nhật dữ liệu user
    $user->name = $request->name;
    $user->email = $request->email;
    $user->role = $request->role;
    $user->avatar = $avatarUrl;

    // Chỉ cập nhật password nếu có nhập
    if ($request->filled('password')) {
        $user->password = Hash::make($request->password);
    }

    $user->save();

    return response()->json([
        'status' => 200,
        'message' => 'Người dùng đã được cập nhật thành công',
        'user' => $user
    ], 200);
}

    public function destroy(string $id){
        $user=User::findOrFail($id);
        if($user){
            $user->delete();
            return response()->json([
                'status'=>200,
                'message'=>'User deleted successfully'
            ],200);
        }else{
            return response()->json([
                'status'=>404,
                'message'=>'No such user found'
            ],404);
        }
    }


}
