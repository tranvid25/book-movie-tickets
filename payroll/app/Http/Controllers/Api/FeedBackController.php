<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FeedBack;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;

class FeedBackController extends Controller
{
    public function index(){
        $feedback=FeedBack::all();
        if($feedback){
            return response()->json([
                'status'=>200,
                'content'=>$feedback
            ]);
        }else{
            return response()->json([
                'status'=>404,
                'message'=>'not found feedback'
            ]);
        }
    }
    public function show($id){
        $feedback=FeedBack::findOrFail($id);
        if($feedback){
            return response()->json([
                'status'=>200,
                'content'=>$feedback
            ]);
        }
        else
        {
            return response()->json([
                'status'=>404,
                'message'=>'not found feedback'
            ]);
        }
    }
    public function store(Request $request){
        $data=$request->all();
        $feedback=FeedBack::create($data);
        if($feedback){
            return response()->json([
                'status'=>200,
                'message'=>'Send Feedback success'
            ]);
        }
        else{
            return response()->json([
                'status'=>404,
                'message'=>'Send Feedback failed'
            ]);
        }
    }
    public function update(Request $request,string $id){
        $feedback=FeedBack::findOrFail($id);
        $data=$request->all();
        $Sendfeedback=$feedback->update($data);
        $tkEmail=$request->email;

        if($Sendfeedback){
            Mail::send('mail.sendEmailFeedback',array(
               'tieuDe'=>$request->tieuDe,
               'noiDung'=>$request->noiDung,
               'ngayXuLy'=>$request->ngayXuLy,
               'noiDungXuLy'=>$request->noiDungXuLy,
            ),function($message) use ($tkEmail){
                $message->to($tkEmail,'name')->subject('PHTV-thông tin feedback');
            });
        }
        return response()->json([
            'message'=>"Feedback successfully updated"
        ]);
    }
    public function destroy(string $id){
        $feedback=FeedBack::findOrFail($id);
        if($feedback){
            $feedback->delete();
            return response()->json([
                'status'=>200,
                'message'=>'deleted successfully'
            ]);
        }
        else{
            return response()->json([
                'status'=>404,
                'message'=>'deleted fails'
            ]);
        }
    }
}
