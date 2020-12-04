<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiColorController extends Controller
{
    public function list_color_api(){
        $list_color=DB::table('tbl_color')->orderByDesc('color_id')->get();
        return response()->json(['data'=>$list_color]);
    }
    
}
