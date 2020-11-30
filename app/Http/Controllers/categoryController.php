<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class categoryController extends Controller
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
        if(!p_author('view','tbl_category')){
            die('Bạn đéo đủ quyền truy cập');
        }
        if($request->has('search')){
            if($request->create_at_from!==null){
                $request->create_at_from.=" 00:00:00";
            }else{
                $request->create_at_from="";
            }
            if($request->create_at_to==null){
                $request->create_at_to= Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString();
            }else{
                $request->create_at_to.=" 23:59:59";
            }

            if($request->update_at_from ==null && $request->update_at_to==null){
                $list_cate=DB::table('tbl_category')->where('category_name','LIKE',"%".$request->category_name."%")->whereBetween('create_at',[$request->create_at_from.' 00:00:00',$request->create_at_to.' 23:59:59'])->paginate(20);
            }else{
                
                if($request->update_at_to==null){
                    $request->update_at_to= Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString();
                }else{
                    $request->update_at_to.=" 23:59:59";
                }
                if($request->update_at_from!==null){
                    $request->update_at_from.=" 00:00:00";
                }else{
                    $request->update_at_from.="";
                }
                
                $list_cate=DB::table('tbl_category')->where('category_name','LIKE',"%".$request->category_name."%")->whereBetween('create_at',[$request->create_at_from,$request->create_at_to])->whereBetween('update_at',[$request->update_at_from,$request->update_at_to])->paginate(20);
                // $list_cate=DB::select("SELECT * FROM tbl_category where category_name LIKE '%%' and create_at between '' and '2020-10-19 15:18:12' and update_at between '2020-10-16 00:00:00' and '2020-10-19 23:59:59' ");
            }
            
        }else{
            $list_cate=DB::table('tbl_category')->paginate(20);
        }
        foreach ($list_cate as $cates => $cate) {
            $count=DB::table('tbl_category_product')->where('category_id',$cate->category_id)->get();
            $count=count($count);
            $list_cate[$cates]->totalProduct=$count;
        }
        $cate=DB::table('tbl_category')->get();
        $cate=$this->get_category_tree($cate);
        $title='Capheny - Danh sách danh mục';
        return view('admin.category.index',['list_cate'=>$list_cate,'cate_tree'=>$cate,'title'=>$title]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!p_author('add','tbl_category')){
            return view('error.403');
        }
        $list_cate=DB::table('tbl_category')->get();
        $list_cate=$this->get_category_tree($list_cate);
        $title='Capheny - Thêm danh mục';
        return view('admin.category.add',['list_cate'=>$list_cate,'title'=>$title]);
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
                'category_name'=>'required|bail',
                'category_parent_id'=>'required',
                'category_slug'=>'required|bail|unique:tbl_category,category_slug|regex:/^[A-z]*\-*[A-z0-9]*/'              
            ],
            [
                'required'=>':attribute không được rỗng',
                'unique'=>':attribute đã tồn tại '
            ],
            [
                'category_name'=>"Tên danh mục",
                'category_parent_id'=>'Danh mục cha',
                'category_slug'=>'Tên đường dẫn'
            ]
        );
        if($validated->fails()) return response()->json(['error'=>$validated->getMessageBag()]);
       
        DB::table('tbl_category')->insert(
            [
                'category_name'=>$request->category_name,
                'category_slug'=>$request->category_slug,
                'category_parent_id'=>$request->category_parent_id,
                'active'=>0,
                'create_at'=>Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString()
            ]
            );
        return response()->json(['success'=>$request->all()]);
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
        if(!p_author('edit','tbl_category')){
            return view('error.403');
        }
        $list_cate=DB::table('tbl_category')->get();
        $list_cate=$this->get_category_tree($list_cate);
        $cate=DB::table('tbl_category')->where('category_id',$id)->first();
        $title='Capheny - Cập nhật danh mục';
        return view('admin.category.edit',['title'=>$title,'cate'=>$cate,'list_cate'=>$list_cate,'update_at'=>Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString()]);
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
                'category_name'=>'required|bail',
                'category_parent_id'=>'required',
                'category_slug'=>[
                                'required',
                                'bail',
                                Rule::unique('tbl_category')->ignore($id,'category_id')
                                ]             
            ],
            [
                'required'=>':attribute không được rỗng',
                'unique'=>':attribute đã tồn tại '
            ],
            [
                'category_name'=>"Tên danh mục",
                'category_parent_id'=>'Danh mục cha',
                'category_slug'=>'Tên đường dẫn'
            ]
        );
        if($validated->fails()) return response()->json(['error'=>$validated->getMessageBag()]);
        DB::table('tbl_category')->where('category_id',$id)->update(
           array_merge($request->except(['_token']),['update_at'=>Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString()]) 
        );
        return response()->json(['success'=>'Edit success']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(!p_author('delete','tbl_category')){
            return view('error.403');
        }
        $check_child=DB::table('tbl_category')->where('category_parent_id',$id)->first();
        if(!empty($check_child)){
            return redirect()->back()->withErrors(['error'=>'Danh mục chứa danh mục con']);
        }
        $checck_product=DB::table('tbl_category_product')->where('category_id',$id)->first();
        if(!empty($checck_product)){
            return redirect()->back()->withErrors(['error_sp'=>'Danh mục chứa sản phẩm con']);
        }
        DB::table('tbl_category')->where('category_id',$id)->delete();
        return redirect('admin/category')->with('success','success');
    }
    public function dequy_delete($id){
        $id_child=DB::table('tbl_category')->select(['category_id','category_parent_id'])->where('category_parent_id',$id)->get();
        for ($i=0; $i <count($id_child) ; $i++) { 
            $this->dequy_delete($id_child[$i]->category_id);
            DB::table('tbl_category')->where('category_id',$id_child[$i]->category_id)->delete();
        }
       return;
    }
    public function convert_vi_to_en($str) {
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", "a", $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", "e", $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", "i", $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", "o", $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", "u", $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", "y", $str);
        $str = preg_replace("/(đ)/", "d", $str);
        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", "A", $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", "E", $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", "I", $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", "O", $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", "U", $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", "Y", $str);
        $str = preg_replace("/(Đ)/", "D", $str);
        $str= preg_replace('/\s/','-',$str);
        //$str = str_replace(" ", "-", str_replace("&*#39;","",$str));
        return $str;
    }
    public function get_category_tree($data,$parent_id=0,$level=0){
        $category_tree=[];
        foreach ($data as $key => $value) {
           if($value->category_parent_id== $parent_id){
                $value->level=$level;
                array_push($category_tree,$value);
                unset($data[$key]);
                $child= $this->get_category_tree($data,$value->category_id,$level+1);
                $category_tree=array_merge($category_tree,$child);
           }
        }
        return $category_tree;
    }

    public function get_category_select_tree($data,$parent_id=0,$level=0){
        $category_tree=[];
        foreach ($data as $key => $value) {
           if($value->category_parent_id== $parent_id){
                $value->id=$value->category_id;
                $value->title=$value->category_name;
                $child= $this->get_category_select_tree($data,$value->category_id,$level+1);
                if(!empty($child)) $value->subs=$child;
                array_push($category_tree,$value);
                unset($data[$key]);
           }
        }
        return $category_tree;
    }
    public function get_tree_category($id=0){
        if($id!=0){
            $data=DB::table('tbl_category')->select(['category_id','category_name','category_parent_id'])->where('category_id','<>',$id)->get()->toArray();
        }else{
            $data=DB::table('tbl_category')->select(['category_id','category_name','category_parent_id'])->get()->toArray();
        }
        $tree=$this->get_category_select_tree($data);
        return response()->json($tree);
    }

    // active category in list category
    public function active_api(Request $request){
        $check_category=DB::table('tbl_category')->where('category_id',$request->id)->first();
        if(empty($check_category)) return response()->json(['error'=>'error']);
        if($check_category->active==1){
            $active=0;
        }else{
            $active=1;
        }
        DB::table('tbl_category')->where('category_id',$request->id)->update(['active'=>$active]);
        return response()->json(['success'=>$active]);
    }
}

