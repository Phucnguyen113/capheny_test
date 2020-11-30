<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class select2Controller extends Controller
{
    public function get_list_user(Request $request){
        $user=DB::table('tbl_user')->where('user_email','LIKE','%'.$request->keyword.'%')->get();
        
        $data=[];
        foreach ($user as $key => $value) {
            $data[]=['id'=>$value->user_id,'text'=>$value->user_email];
        }

        return response()->json($data);
      
    }

    public function get_list_product(Request $request){
        $product=DB::table('tbl_product')->where('product_name','LIKE','%'.$request->keyword.'%')->get();
        
        $data=[];
        foreach ($product as $key => $value) {
            $data[]=['id'=>$value->product_id,'text'=>$value->product_name];
        }

        return response()->json($data);
      
    }
    
}
