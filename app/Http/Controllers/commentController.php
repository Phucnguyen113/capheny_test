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
        $title='Capheny - Danh sách bình luận';
        return view('admin.comment.index',compact('list_comment','title'));
    }
    public function add_form(){
        if(!p_author('add','tbl_comment')){
            return view('error.403');
        }
        $list_product=DB::table('tbl_product')->orderByDesc('product_id')->get();
        $list_user=DB::table('tbl_user')->orderByDesc('user_id')->get();
        $title='Capheny - Thêm bình luận';
        return view('admin.comment.add',compact('list_user','list_product','title'));
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
        $comment_id=DB::table('tbl_comment')->insertGetId( array_merge($request->except(['_token']),['create_at'=>$create_at]) );
        p_history(0,'đã thêm 1 comment mới #'.$comment_id,p_user()['user_id']);
        event(new \App\Events\pusherComment(['comment_id'=>$comment_id,'user_email'=>p_user()['user_email']]));
        return response()->json(['success'=>'success']);
    }
    public function edit_form($id){
        if(!p_author('edit','tbl_comment')){
            return view('error.403');
        }
        $comment=DB::table('tbl_comment')->where('comment_id',$id)->first();
        if(empty($comment)){
            return view('error.notfound');
        }
        $user=DB::table('tbl_user')->where('user_id',$comment->user_id)->first();
        $product=DB::table('tbl_product')->where('product_id',$comment->product_id)->first();
        $title='Capheny - Cập nhật bình luận';
        return view('admin.comment.edit',compact('comment','user','product','title'));
    }
    public function edit($id,Request $request){
        $validated=Validator::make($request->all(),
            [
                'user_id'=>'bail|required|numeric',
                'product_id'=> 'bail|required|numeric',
                'content' => 'bail|required'
            ],
            [
                'required' =>'Chưa chọn :attribute',
                'content.required' => 'Chưa nhập nội dung',
                'numeric'=>' Sai kiểu dữ liệu của :attribute'
            ],
            [
                'user_id'=>'người dùng',
                'product_id' => 'sản phẩm'
            ]
        );
        if($validated->fails()) return response()->json(['error'=>$validated->getMessageBag()]);
        $active=0;
        if($request->has('active') && $request->active!==null){
            $active=1;
        }
        $now=Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString();
        $comment_old=DB::table('tbl_comment')->where('comment_id',$id)->first(['content']);
        DB::table('tbl_comment')->where('comment_id',$id)->update([
            'active'=>$active,
            'product_id'=>$request->product_id,
            'user_id'=>$request->user_id,
            'content'=>$request->content,
            'update_at'=>$now
        ]);

        p_history(1,'đã cập nhật bình luận #'.$id,p_user()['user_id']);
        event(new \App\Events\pusherCommentEdit(['comment_id'=>$id,'user_email'=>p_user()['user_email']]));
        return response()->json(['success'=>'success']);
    }
    public function delete_comment($id){
        DB::table("tbl_comment")->where('comment_id',$id)->delete();
        p_history(2,'đã xóa bình luận #'.$id,p_user()['user_id']);
        event(new \App\Events\pusherCommentDelete(['comment_id'=>$id,'user_email'=>p_user()['user_email']]));
        return response()->json(['success'=>['success']]);
    }
}
