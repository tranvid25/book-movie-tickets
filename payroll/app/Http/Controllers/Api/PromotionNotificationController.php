<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PromotionNotification;
use App\Events\NewPromotionNotification;
use Illuminate\Http\Request;

class PromotionNotificationController extends Controller
{
    public function store(Request $request){
       $request->validate([
           'title' => 'required|string|max:255',
           'description' => 'nullable|string'
       ]);

       $data = $request->all();
       $notification = PromotionNotification::create($data);

       //Phát event realtime
       event(new NewPromotionNotification($notification));

       return response()->json([
         'status' => 200,
         'data' => $notification
       ]);
    }

    public function unread(){
        $notifications = PromotionNotification::where('read', false)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($notifications);
    }

    public function markAsRead($id){
        $notification = PromotionNotification::findOrFail($id);
        $notification->update(['read' => true]);

        return response()->json([
            'status' => 200,
            'message' => 'Notification marked as read'
        ]);
    }
}
