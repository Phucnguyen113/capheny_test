<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
class ApiCommentController extends Controller
{
    public function add(Request $request){
       
        $validated=Validator::make($request->all(),
            [
                'product_id'=>'required|numeric',
                'content' =>'required'
            ],
            [
                'required' => 'Chưa chọn :attribute',
                'content.required'=> 'Chưa nhập nội dung bình luận',
                'numeric' => 'Lỗi'
            ],
            [
               
                'product_id'=> 'sản phẩm',

            ]
        );
        if($validated->fails()) return response()->json(['error'=>$validated->getMessageBag()]);
        $create_at=Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString();
        $user=JWTAuth::parseToken()->authenticate();
        $user=json_decode($user);
        $data=['user_id'=>$user->user_id,'content'=>$request->content,'product_id'=>$request->product_id,'create_at'=>$create_at,'active'=>0];
        DB::table('tbl_comment')->insert( $data );
        return response()->json(['success'=>'success']);
    }
    public function list_comment(Request $request){
        $list_comment=DB::table('tbl_comment')
        ->join('tbl_user','tbl_user.user_id','=','tbl_comment.user_id')
        ->where([
            ['product_id','=',$request->product_id],
            ['active','=',1]
        ])->get(['tbl_comment.*','tbl_user.user_email']);
        return response()->json(['data'=>$list_comment]);
    }
}
