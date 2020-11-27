<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class commentController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkInput');
       
    }
    public function index(Request $request){
        if(!p_author('view','tbl_comment')){
            return view('error.403');
        }
        $list_comment=DB::table('tbl_comment')
        ->join('tbl_product','tbl_product.product_id','=','tbl_comment.product_id')
        ->join('tbl_user','tbl_user.user_id','=','tbl_comment.user_id')
        ->orderByDesc('comment_id')->select(['tbl_product.product_name','tbl_user.user_email','tbl_comment.*'])->paginate(20);
        return view('admin.comment.index',compact('list_comment'));
    }
    public function add_form(){
        if(!p_author('add','tbl_comment')){
            return view('error.403');
        }
        $list_product=DB::table('tbl_product')->orderByDesc('product_id')->get();
        $list_user=DB::table('tbl_user')->orderByDesc('user_id')->get();
        return view('admin.comment.add',compact('list_user','list_product'));
    }
    public function add(Request $request){
        $validated=Validator::make($request->all(),
            [
                'user_id'=>'required|numeric',
                'product_id'=>'required|numeric',
                'content' =>'required'
            ],
            [
                'required' => 'Chưa chọn :attribute',
                'content.required'=> 'Chưa nhập nội dung bình luận',
                'numeric' => 'Lỗi'
            ],
            [
                'user_id' => 'người dùng',
                'product_id'=> 'sản phẩm',

            ]
        );
        if($validated->fails()) return response()->json(['error'=>$validated->getMessageBag()]);
        $create_at=Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString();
        DB::table('tbl_comment')->insert( array_merge($request->except(['_token']),['create_at'=>$create_at]) );
        return response()->json(['success'=>'success']);
    }
}
