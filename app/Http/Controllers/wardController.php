<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class wardController extends Controller
{
     public function __construct()
     {
          $this->middleware('checkInput');
     }
    public function get_ward($id){
       try {
            $list_ward=DB::table('tbl_ward')->where('_district_id',$id)->get();
            return response()->json(['data'=>$list_ward]);
       } catch (\Exception $e) {
            return response()->json(['error'=>$e->getMessage()]);
       }
    }
}
