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
        if($request->has('search')){ 
            
            if($request->create_at_from == null) $request->create_at_from='';
            if($request->create_at_to   == null)  $request->create_at_to=Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString();

            if($request->update_at_from == null && $request->update_at_to == null){
                if(!empty($request->color)) $list_color=DB::table('tbl_color')->whereIn('color_id',$request->color)->whereBetween('create_at',[$request->create_at_from,$request->create_at_to])->paginate(20);
                else $list_color=DB::table('tbl_color')->whereBetween('create_at',[$request->create_at_from,$request->create_at_to])->paginate(20);
            }else{
                if($request->upddate_at_from== null) $request->update_at_from='';
                if($request->upddate_at_to== null) $request->update_at_to= Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString();
                if(!empty($request->color)) $list_color=DB::table('tbl_color')->whereIn('color_id',$request->color)->whereBetween('create_at',[$request->create_at_from,$request->create_at_to])->whereBetween('update_at',[$request->update_at_from,$request->update_at_to])->paginate(20);
                else $list_color=DB::table('tbl_color')->whereBetween('create_at',[$request->create_at_from,$request->create_at_to])->whereBetween('update_at',[$request->update_at_from,$request->update_at_to])->paginate(20);
            }
        }else{
            $list_color= DB::table('tbl_color')->orderByDesc('color_id')->paginate(20);
        }
        $list_color_for_search=DB::table('tbl_color')->orderByDesc('color_id')->get();
        return view('admin/color/index',['list_color'=>$list_color,'color_search'=>$list_color_for_search]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin/color/add');
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
        $color=DB::table('tbl_color')->where('color_id',$id)->first();

        return view('admin/color/edit',['color'=>$color]);
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
        $check_isset=DB::table('tbl_product_color')->where('color_id',$id)->first();
        if(!empty($check_isset)) return redirect()->back()->withErrors(['error'=>'Màu này đã có sản phẩm']);
        DB::table('tbl_color')->where('color_id',$id)->delete();
        return redirect()->back()->with('success','success');
    }
}
