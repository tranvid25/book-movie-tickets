<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SeatController extends Controller
{
    public function index(){
        $seat=Seat::all();
        if($seat){
            return response()->json([
                'status'=>200,
                'content'=>$seat
            ]);
        }
        else{
            return response()->json([
                'status'=>404,
                'message'=>'not found Seat'
            ]);
        }
    }
    public function show($id){
        $ghengoi=Seat::where('maLichChieu',$id)->get();
        if($ghengoi){
            return response()->json([
                'status'=>200,
                'content'=>$ghengoi
            ]);
        }
        else
        {
            return response()->json([
                'status'=>404,
                'message'=>'not found Seat'
            ]);
        }
    }
    public function destroy($id){
        $ghengoi=Seat::findOrFail($id);
        if($ghengoi){
            return response()->json([
                'status'=>200,
                'message'=>'deleted successfully!'
            ]);
        }
        else{
            return response()->json([
                'status'=>404,
                'message'=>'not deleted'
            ]);
        }
    }
}
