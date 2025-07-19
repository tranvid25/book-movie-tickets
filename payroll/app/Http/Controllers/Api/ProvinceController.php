<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Province;
use Illuminate\Http\Request;

class ProvinceController extends Controller
{
    public function index(){
        $province=Province::all();
        if($province){
            return response()->json([
                'status'=>200,
                'content'=>$province
            ]);
        }
        else{
            return response()->json([
                'status'=>404,
                'message'=>'not found province'
            ]);
        }
    }
    public function store(Request $request){
        $data=$request->all();
        $province=Province::create($data);
        if($province){
            return response()->json([
                'status'=>200,
                'message'=>'Created Province Successfully!'
            ],200);
        }
        else{
            return response()->json([
                'status'=>404,
                'message'=>'Created Province fail'
            ],404);
        }
    }
}
