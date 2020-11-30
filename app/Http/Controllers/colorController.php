<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class colorController extends Controller
{
    public function __construct()
    {
         $this->middleware('checkInput');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        if(!p_author('view','tbl_color')){
            return view('error.403');
        }
        $list_color=DB::table('tbl_color');
        if($request->color!==null && empty($request->color)){
            $list_color=$list_color->whereIn('color_id',$request->color);
        }
        
        // create_at product process
        if($request->create_at_from!==null && $request->create_at_to!==null){
            $list_color=$list_color->whereBetween('create_at',[$request->create_at_from,$request->create_at_to]);
        }else if($request->create_at_from==null && $request->create_at_to!==null ){
            $list_color=$list_color->whereBetween('create_at',['',$request->create_at_to]);
        }else if($request->create_at_from!==null && $request->create_at_to==null){
            $list_color=$list_color->whereBetween('create_at',[$request->create_at_from,Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString()]);
        }
       
        // update_at product process
        if($request->update_at_from!==null && $request->update_at_to!==null){
            $list_color=$list_color->whereBetween('update_at',[$request->update_at_from,$request->update_at_to]);
        }else if($request->update_at_from==null && $request->update_at_to!==null){
            $list_color=$list_color->whereBetween('update_at',['',$request->update_at_to]);
        }else if($request->update_at_from!==null && $request->update_at_to==null){
            $list_color=$list_color->whereBetween('update_at',[$request->update_at_from,Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString()]);
        }
        
        $list_color=$list_color->orderByDesc('color_id')->paginate(20);
        
        $list_color_for_search=DB::table('tbl_color')->orderByDesc('color_id')->get();
        $title='Capheny - Danh sách màu';
        return view('admin/color/index',['list_color'=>$list_color,'color_search'=>$list_color_for_search,'title'=>$title]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!p_author('add','tbl_color')){
            return view('error.403');
        }
        $title='Capheny - Thêm màu';
        return view('admin/color/add',compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      
        $validated= Validator::make($request->all(),
            [
                'color'=>'required|bail|unique:tbl_color,color|regex:/^[A-z0-9]*$/'
            ],
            [
                'color.required'=>'Color không được rỗng',
                'color.unique'=>'Color đã tồn tại',
                'color.regex'=>'Color chỉ chứa chữ và số'
            ],
            [

            ]
        );
        if($validated->fails()) return response()->json(['error'=>$validated->getMessageBag()]);
        DB::table('tbl_color')->insert(['color'=>$request->color,'create_at'=>Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString()]);
        return response()->json(['success'=>'insert color success']);
            
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
        if(!p_author('edit','tbl_color')){
            return view('error.403');
        }
        $color=DB::table('tbl_color')->where('color_id',$id)->first();
        $title='Capheny - Cập nhật màu';
        return view('admin/color/edit',['color'=>$color,'title'=>$title]);
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
        
        $validated= Validator::make($request->all(),
            [
                'color'=>[
                            'required',
                            'regex:/^[A-z0-9]*$/',
                            Rule::unique('tbl_color')->ignore($id,'color_id')
                        ]
            ],
            [
                'color.required'=>' Mã màu không được rỗng',
                'color.regex'=>'Mã màu chỉ chứa chữ và số',
                'color.unique'=>'Mã màu đã tồn tại'
            ],
            [

            ]
        );
        if($validated->fails()) return response()->json(['error'=>$validated->getMessageBag()]);
        DB::table('tbl_color')->where('color_id',$id)->update(
            [
                'color'=>$request->color,
                'update_at'=>Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString()
            ]
            );
            return response()->json(['success'=>'update color success']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(!p_author('delete','tbl_color')){
            return view('error.403');
        }
        $check_isset=DB::table('tbl_product_color')->where('color_id',$id)->first();
        if(!empty($check_isset)) return redirect()->back()->withErrors(['error'=>'Màu này đã có sản phẩm']);
        DB::table('tbl_color')->where('color_id',$id)->delete();
        return redirect()->back()->with('success','success');
    }
}
