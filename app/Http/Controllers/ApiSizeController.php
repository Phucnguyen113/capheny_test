<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiSizeController extends Controller
{
    public function list_size_api(){
        $list_size=DB::table('tbl_size')->orderByDesc('size_id')->get();
        return response()->json(['data'=>$list_size]);
    }
}
