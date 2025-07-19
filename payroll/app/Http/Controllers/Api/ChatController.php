<?php

namespace App\Http\Controllers\Api;

use App\Events\MessageSent;
use App\Events\UserOnline;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;

class ChatController extends Controller
{
    // Lấy lịch sử chat, trả về kèm user
    public function index()
    {
        $messages = Message::with('user')->orderBy('id')->get();
        return response()->json([
            'messages' => $messages
        ]);
    }

    // Gửi tin nhắn, lưu userId, message, broadcast event
    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        $fileUrl = null;
        $fileName = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = Str::random(12) . '.' . $file->getClientOriginalExtension();
            $fileDirectory = 'images/message/';
            $file->move(public_path($fileDirectory), $fileName);
            $fileUrl = url($fileDirectory . $fileName);
        }
        // Cho phép gửi tin nhắn chỉ có file hoặc chỉ có text hoặc cả hai
        if (!$request->filled('message') && !$fileUrl) {
            return response()->json(['error' => 'Message or file is required'], 422);
        }
        $msg = Message::create([
            'userId' => $user->id,
            'message' => $request->message ?? '',
            'file_path' => $fileUrl
        ]);
        // Broadcast event UserOnline (chuẩn BE FE)
        broadcast(new \App\Events\UserOnline($user, $msg->message, $fileUrl))->toOthers();
        return response()->json($msg);
    }
}
