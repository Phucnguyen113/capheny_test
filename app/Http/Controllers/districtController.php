<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class districtController extends Controller
{
    public function __construct()
    {
         $this->middleware('checkInput');
    }
    function get_district($id){
        try{
            $list_district=DB::table('tbl_district')->where('_province_id',$id)->get();
            return response()->json(['data'=>$list_district]);
        }catch(\Exception $e){
            return response()->json(['error'=>$e->getMessage()]);
        }   
    }
}
