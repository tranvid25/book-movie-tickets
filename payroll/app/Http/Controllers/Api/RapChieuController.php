<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RapChieu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RapChieuController extends Controller
{
    public function index(){
        $rapchieu=RapChieu::with('tinhThanh')->get();
        if($rapchieu){
            return response()->json([
                'status'=>200,
                'content'=>$rapchieu
            ]);
        }
        else{
            return response()->json([
                'status'=>400,
                'message'=>'Not found'
            ]);
        }
    }
    public function store(Request $request){
        $data=$request->all();
        $rapchieu=RapChieu::create($data);
        if($rapchieu){
            return response()->json([
                'status'=>200,
                'message'=>'create successfully!'
            ]);
        }
        else
        {
            return response()->json([
                'status'=>500,
                'message'=>'Something went wreong'
            ]);
        }
    }
    public function show($id){
        $rapchieu=RapChieu::findOrFail($id);
        if($rapchieu){
            return response()->json([
                'status'=>200,
                'content'=>$rapchieu
            ]);
        }
        else
        {
            return response()->json([
                'status'=>404,
                'message'=>'not founđ rap'
            ]);
        }
    }
    public function update(Request $request,string $id){
        $validator=Validator::make($request->all(),[
            'tenRap'=>'required|string|max:100',
            'diaChi'=>'required|string|max:200',
            'maTinh_id'=>'nullable'
        ]);
        if($validator->fails()){
           return response()->json([
            'status'=>422,
            'message'=>$validator->errors()
           ]);
        }
        $rapchieu=RapChieu::findOrFail($id);
        if($rapchieu){
           $rapchieu->update([
            'tenRap'=>$request->tenRap,
            'diaChi'=>$request->diaChi,
            'maTinh_id'=>$request->maTinh_id
           ]);
           return response()->json([
            'status'=>200,
            'message'=>'update successfully'
           ]);
        }
        else{
            return response()->json([
                'status'=>404,
                'message'=>'no such thetre found',
            ]);
        }
    }
    public function destroy($id){
        $rapchieu=RapChieu::findOrFail($id);
        if($rapchieu){
            $rapchieu->delete();
            return response()->json([
                'status'=>200,
                'message'=>'delete successfully!'
            ]);
        }
        else
        {
            return response()->json([
                'status'=>404,
                'message'=>'delete fail'
            ]);
        }
    }
}
