<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class sizeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!p_author('view','tbl_size')){
            return view('error.403');
        }
        $list_size=DB::table('tbl_size');
        if($request->size!==null ){
            $list_size=$list_size->where('size','LIKE','%'.$request->size.'%');
        }
        
        // create_at product process
        if($request->create_at_from!==null && $request->create_at_to!==null){
            $list_size=$list_size->whereBetween('create_at',[$request->create_at_from,$request->create_at_to]);
        }else if($request->create_at_from==null && $request->create_at_to!==null ){
            $list_size=$list_size->whereBetween('create_at',['',$request->create_at_to]);
        }else if($request->create_at_from!==null && $request->create_at_to==null){
            $list_size=$list_size->whereBetween('create_at',[$request->create_at_from,Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString()]);
        }
       
        // update_at product process
        if($request->update_at_from!==null && $request->update_at_to!==null){
            $list_size=$list_size->whereBetween('update_at',[$request->update_at_from,$request->update_at_to]);
        }else if($request->update_at_from==null && $request->update_at_to!==null){
            $list_size=$list_size->whereBetween('update_at',['',$request->update_at_to]);
        }else if($request->update_at_from!==null && $request->update_at_to==null){
            $list_size=$list_size->whereBetween('update_at',[$request->update_at_from,Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString()]);
        }
        
        $list_size=$list_size->orderByDesc('size_id')->paginate(20);
        $title='Capheny - Danh sách kích thước';
        return view('admin.size.index',['list_size'=>$list_size,'title'=>$title]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!p_author('add','tbl_size')){
            return view('error.403');
        }
        $title='Capheny - Thêm kích thước';
        return view('admin.size.add',compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated=Validator::make($request->all(),
            [
                'size'=>'required|regex:/^[A-z0-9]*$/|unique:tbl_size,size'
            ],
            [
                'size.required'=>'Size không được rỗng',
                'size.regex'=>'Size chỉ chứa chữ và số',
                'size.unique'=>'Size đã tồn tại'
            ],
            [

            ]
        );
        if($validated->fails()) return response()->json(['error'=>$validated->getMessageBag()]);
        $id=DB::table('tbl_size')->insertGetId(
            [   
                'size'=>$request->size,
                'create_at'=>Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString()
            ]
        );
        p_history(0,'đã thêm kích thước mới #'.$id,p_user()['user_id']);
        event(new \App\Events\pusherSize(['size_id'=>$id,'user_email'=>p_user()['user_email']]));
        return response()->json(['success'=>'Insert size success']);
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {   
        if(!p_author('edit','tbl_size')){
            return view('error.403');
        }
        $title='Capheny - Cập nhật kích thước';
        $size=DB::table('tbl_size')->where('size_id',$id)->first();
        return view('admin/size/edit',['size'=>$size,'title'=>$title]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validated=Validator::make($request->all(),
            [
                'size'=>[
                        'required',
                        'regex:/^[A-z0-9]*$/',
                         Rule::unique('tbl_size')->ignore($id,'size_id')
                        ]
            ],
            [
                'size.required'=>'Size không được rỗng',
                'size.regex'=>'Size chỉ chứa chữ và số',
                'size.unique'=>'Size đã tồn tại'
            ],
            [

            ]
        );
        if($validated->fails()) return response()->json(['error'=>$validated->getMessageBag()]);
        DB::table('tbl_size')->where('size_id',$id)->update(
            [
                'size'=>$request->size,
                'update_at'=>Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString()
            ]
        );
        event(new \App\Events\pusherSizeEdit(['size_id'=>$id,'user_email'=>p_user()['user_email']]));
        p_history(1,'đã cập nhật kích thước #'.$id,p_user()['user_id']);
        return response()->json(['success'=>'Update success']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(!p_author('delete','tbl_size')){
            return view('error.403');
        }
        $check_isset=DB::table('tbl_product_size')->where('size_id',$id)->first();
        if(!empty($check_isset)) return redirect()->back()->withErrors(['error'=>'Kích cỡ này đã gán cho sản phẩm']);
        DB::table('tbl_size')->where('size_id',$id)->delete();
        p_history(2,'đã xóa kích thước #'.$id,p_user()['user_id']);
        event(new \App\Events\pusherSizeDelete(['size_id'=>$id,'user_email'=>p_user()['user_email']]));
        return redirect()->back()->with('success','success');
    }
    
}
