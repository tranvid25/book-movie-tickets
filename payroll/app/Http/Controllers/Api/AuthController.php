<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\SendNewPassword;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class AuthController extends Controller
{
    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed',
            'role' => 'nullable|string',
            'avatar' => 'nullable|string',
            'fileName' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => '401',
                'message' => 'Email này đã được sử dụng',
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()->toArray(),
            ]);
        } else {
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            return response()->json([
                'status' => 200,
            ]);
        }
    }
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember' => 'boolean'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fails',
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()->toArray(),
            ]);
        }
        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'status' => 'fails',
                'message' => 'Sai tên đăng nhập hoặc mật khẩu'
            ], 401);
        }
        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        if ($request->remember) {
            $token->expires_at = Carbon::now()->addWeek(1);
        }
        $token->save();
        return response()->json([
            'status' => 200,
            'content' => [
                'content' => $user,
                'accessToken' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse(
                    $tokenResult->token->expires_at
                )->toDateTimeString()
            ]
        ], 200);
    }
    public function logout(Request $request)
    {
        $accesstoken=$request->user()->token();
        $tokenId=$accesstoken->id;
        //tính ttl còn lại của token
        $expiresAt=$accesstoken->expires_at;
        $ttl=Carbon::parse($expiresAt)->diffInSeconds(now());
        //đưa token vào Redis blackList
        Redis::setex("blacklist:token:$tokenId",$ttl,true);
        //thu hồi token
        $accesstoken->revoke();
        return response()->json([
            'status' => 200,
            'message'=>'Logged'
        ]);
    }
    public function user(Request $request)
    {
        return response()->json($request->user());
    }
    public function passwordRetrieval(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|string|email|exists:users,email',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 422,
            'message' => $validator->errors()->first(),
            'errors' => $validator->errors()->toArray(),
        ]);
    }

    $tkEmail = $request->email;
    $newPwd = Str::random(6);
    $user = User::where('email', $tkEmail)->first();

    if ($user) {
        $user->password = bcrypt($newPwd);
        $user->save();

        // Gửi email bằng queue
        Mail::to($tkEmail)->queue(new SendNewPassword($newPwd));

        return response()->json([
            'status' => 200,
            'message' => 'Mật khẩu mới đã được gửi đến email của bạn',
            'email' => $tkEmail
        ]);
    }

    return response()->json([
        'status' => 404,
        'message' => 'Email này chưa đăng ký'
    ]);
}
}
