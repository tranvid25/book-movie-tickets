<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentMovieController extends Controller
{
    public function index(){
        $comment=Comment::with('tinPhim')->get();
        if($comment){
            return response()->json([
                'status'=>200,
                'content'=>$comment
            ]);
        }
        else
        {
            return response()->json([
                'status'=>404,
                'message'=>'not found comment'
            ]);
        }
    }
    public function show($id){
        $comment=Comment::where('maPhim',$id)->get();
        if($comment){
            return response()->json([
                'status'=>200,
                'content'=>$comment
            ]);
        }
        else
        {
            return response()->json([
                'status'=>404,
                'message'=>'not found comment'
            ]);
        }
    }
    public function store(Request $request){
        $data=[
            'userId'=>Auth::id(),
            'parent_id'=>$request->parent_id,
            'comment'=>$request->comment,
            'userName'=>Auth::user()->name,
            'userAvatar'=>Auth::user()->avatar,
            'maPhim'=>$request->maPhim,
            'level'=>0,
            'time'=>now()
        ];
        $comment=Comment::create($data);
        if($comment){
            return response()->json([
                'status'=>200,
                'message'=>'create successfully!'
            ]);
        }
        else
        {
            return response()->json([
                'status'=>404,
                'message'=>'create fail'
            ]);
        }
    }
    public function update(Request $request,string $id){
        $comment=Comment::findOrFail($id);
        $comment->comment=$request->comment;
        $comment->time=now();
        $comment->save();
        return response()->json([
            'status'=>200,
            'message'=>'Cập nhật thành công'
        ]);
    }
    public function destroy(string $id){
        $comment=Comment::findOrFail($id);
        if($comment){
            $comment->delete();
            return response()->json([
                'status'=>200,
                'message'=>'delete successfully'
            ]);
        }
        else{
            return response()->json([
                'status'=>404,
                'message'=>'not found comment'
            ]);
        }
    }
    public function edit(string $id){
        $comment=Comment::findOrFail($id);
        if($comment){
            return response()->json([
                'status'=>200,
                'message'=>'found successfully'
            ]);
        }
        else{
            return response()->json([
                'status'=>404,
                'message'=>'not found successfully'
            ]);
        }
    }
}
