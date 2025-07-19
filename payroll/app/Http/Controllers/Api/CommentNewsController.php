<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentNewsController extends Controller
{
      public function index(){
        $comment=Comment::with('baiViet')->get();
        if($comment){
            return response()->json([
                'status'=>200,
                'content'=>$comment
            ]);
        }
        else{
            return response()->json([
                'status'=>400,
                'message'=>'not found comment'
            ]);
        }
    }
    public function show(string $id){
        $comment=Comment::where('maBaiViet',$id)->with('baiViet')->get();
        if($comment){
            return response()->json([
                'status'=>200,
                'content'=>$comment
            ]);
        }
        else{
            return response()->json([
                'status'=>404,
                'message'=>'not found comment detail'
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
        'maBaiViet'=>$request->maBaiViet,
        'level'=>0,
        'time'=>now(),
       ];
       //tính level nếu là reply
       if(!is_null($data['parent_id'])){
        $parent=Comment::findOrFail($data['parent_id']);
        $data['level']=$parent->Level+1;
       }
       $comment=Comment::create($data);
       return response()->json([
          'status'=>200,
          'content'=>$comment
       ]);
    }
    public function update(Request $request,string $id){
        $comment=Comment::findOrFail($id);
        $comment->comment=$request->comment;
        $comment->time=now();
        $comment->save();
        return response()->json([
            'status'=>200,
            'mesage'=>'Cập nhật thành công'
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
    
}

