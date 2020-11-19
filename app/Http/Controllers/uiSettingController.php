<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class uiSettingController extends Controller
{  
    public function set_ui(Request $request){
        try {
            $data=json_encode($request->except(['table','user_id']));
            DB::table('tbl_system_ui')->where([
                ['user_id','=',$request->user_id],
                ['name','=',$request->table]
            ])->update(['value'=>$data]);
            return response()->json(['success'=>'success']);
        } catch (\Throwable $th) {
            return response()->json(['error'=>'error']);
        }
            
      
    }
}
