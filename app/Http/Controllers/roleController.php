<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class roleController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkInput');
    }
    public function index(Request $request){
        if(!p_author('view','tbl_role')){
            die('Bạn del đủ quyền truy cập');
        }
        $list_role=DB::table('tbl_role');
        if($request->role!==null){
            $list_role=$list_role->where('role','LIKE','%'.$request->role.'%');
        }
        // create_at product process
        if($request->create_at_from!==null && $request->create_at_to!==null){
            $list_role=$list_role->whereBetween('create_at',[$request->create_at_from,$request->create_at_to]);
        }else if($request->create_at_from==null && $request->create_at_to!==null ){
            $list_role=$list_role->whereBetween('create_at',['',$request->create_at_to]);
        }else if($request->create_at_from!==null && $request->create_at_to==null){
            $list_role=$list_role->whereBetween('create_at',[$request->create_at_from,Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString()]);
        }
        // update_at product process
        if($request->update_at_from!==null && $request->update_at_to!==null){
            $list_role=$list_role->whereBetween('update_at',[$request->update_at_from,$request->update_at_to]);
        }else if($request->update_at_from==null && $request->update_at_to!==null){
            $list_role=$list_role->whereBetween('update_at',['',$request->update_at_to]);
        }else if($request->update_at_from!==null && $request->update_at_to==null){
            $list_role=$list_role->whereBetween('update_at',[$request->update_at_from,Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString()]);
        }
        $list_role=$list_role->orderByDesc('role_id')->paginate(20);
        return view('admin.role.index',compact('list_role'));
    }
    public function add_role_for_user_form(){
        if(!p_author('add_role','tbl_user')){
            die('Bạn del đủ quyền truy cập');
        }
        $list_user=DB::table('tbl_user')->where('user_type',1)->orderByDesc('user_id')->get();
        $list_role=DB::table('tbl_role')->orderByDesc('role_id')->get();
        return view('admin.user.addrole',compact('list_user','list_role'));
    }
    public function add_role(Request $request){
        $validated=Validator::make($request->all(),
            [
                'user_id'=>'bail|required',
                'role_id'=>'bail|required|array',
                'role_id.*'=>'bail|numeric'
            ],
            [
                'required'=> 'Chưa chọn :attribute'
            ],
            [
                'user_id' =>'người dùng',
                'role_id' => 'vai trò'
            ]
        );
        if($validated->fails()) return response()->json(['error'=>$validated->getMessageBag()]);
        foreach ($request->role_id as $roles => $role) {
            $role_old=DB::table('tbl_user_role')->where([
                ['user_id','=',$request->user_id],
                ['role_id','=',$role]
            ])->first();
            if(!empty($role_old)) continue;
            DB::table('tbl_user_role')->insert(['user_id'=>$request->user_id,'role_id'=>$role]);
        }
        return response()->json(['success'=>'success']);
    }
    public function edit_role_for_user_form($id){
        if(!p_author('edit','tbl_role')){
            die('Bạn del đủ quyền truy cập');
        }
        $user=DB::table('tbl_user')->where([
            ['user_id','=',$id],
            ['user_type','=',1]
        ])->first();
        if(empty($user)) return redirect()->back();
        //check user is super admin
        $is_admin_arr=DB::table('tbl_user_role')->where('user_id',$user->user_id)->get(['role_id'])->toArray();
        for ($i=0; $i <count($is_admin_arr) ; $i++) { 
           if($is_admin_arr[$i]->role_id==1){
                return redirect()->back();
           }
        }
        $list_role=DB::table('tbl_role')->where('role_id','<>',1)->get();
        $list_role_of_user=DB::table('tbl_user_role')->where('user_id',$id)->get(['role_id'])->toArray();
       
        $list_role_id_of_user=[];
        foreach ($list_role_of_user as $roles => $role) {
            $list_role_id_of_user[]=$role->role_id;
        }
        return view('admin.user.editrole', compact('user','list_role','list_role_id_of_user') );
    }
    public function edit_role(Request $request,$id){
        $validated=Validator::make($request->all(),
            [
                'role_id' =>'array|required',
                'role_id.*' => 'numeric'
            ],
            [
                'required'=> 'Chưa chọn vai trò'
            ],
            [
                
            ]
        );
        if($validated->fails()) return response()->json(['error'=>$validated->getMessageBag()]);
        $user=DB::table('tbl_user')->where([
            ['user_id','=',$id],
            ['user_type','=',1]
        ])->first();
        if(empty($user)) return response()->json(['error'=>['user_id'=>'Không tìm thấy người dùng']]);
        //delete role old 
        DB::table('tbl_user_role')->where('user_id',$user->user_id)->delete();
        //add new role
        for ($i=0; $i <count($request->role_id) ; $i++) { 
            if($request->role_id[$i]==0){
                return response()->json(['success'=>'success']);
            }
        }
        for ($i=0; $i <count($request->role_id) ; $i++) { 
            DB::table('tbl_user_role')->insert(['user_id'=>$user->user_id,'role_id'=>$request->role_id[$i]]);
        }
        return response()->json(['success'=>'success']);
    }
    public function delete_role($id){
        $check_permission=DB::table('tbl_role_permission')->where('role_id',$id)->first();
        if(!empty($check_permission)) return redirect()->back()->withErrors(['error_permission'=>'error']);
        $check_user =DB::table('tbl_user_role')->where('role_id',$id)->first();
        if(!empty($check_user)) return redirect()->back()->withErrors(['error_user'=>'error']);
        DB::table('tbl_role')->where('role_id',$id)->delete();
        return redirect()->back()->with('success','success');
    }
    public function edit_name_role_form($id){
        if(!p_author('view','tbl_role')){
            die('bạn không đủ quyền truy cập');
        }
        $role=DB::table('tbl_role')->where('role_id',$id)->first();
        $permission_old=DB::table('tbl_role_permission')->where('role_id',$id)->get();
        $permission_id_old=[];
        foreach ($permission_old as $permissions => $permission) {
            $permission_id_old[]=$permission->permission_id;
        }
        $list_permission=DB::table('tbl_permission')->get();
        return view('admin.role.edit',compact('role','permission_id_old','list_permission'));
    }
    public function edit_name_role(Request $request,$id){
        $validated=Validator::make($request->all(),
            [
                'role'=>'required',
                'permission'=>'bail|required|array',
                'permission.*'=>'bail|numeric'
            ],
            [
                'required'=>'Chưa nhập :attribute',
                'permission.required'=>'Chưa chọn quyền'
            ],
            [
                'role'=>'vai trò',
                'permission'=>'quyền'
            ]
        );
        if($validated->fails()) return response()->json(['error'=>$validated->getMessageBag()]);
        
        DB::table('tbl_role')->where('role_id',$id)->update(['role'=>$request->role,'update_at'=>Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString()]);
        DB::table('tbl_role_permission')->where('role_id',$id)->delete();
        foreach ($request->permission as $permissions => $permission) {
            DB::table('tbl_role_permission')->insert(['role_id'=>$id,'permission_id'=>$permission]);
        }
        return response()->json(['success'=>'success']);
    }
    public function add_form(){
        return view('admin.role.add');
    }
    public function add(Request $request){
        $validated=Validator::make($request->all(),
            [
                'role'=>'bail|required|unique:tbl_role,role'
            ],
            [
                'required'=>':attribute không được trống',
                'unique' => ':attribute đã tồn tại'
            ],
            [
                'role'=>'Vai trò'
            ]
        );
        if($validated->fails()) return response()->json(['error'=>$validated->getMessageBag()]);
        DB::table('tbl_role')->insert(['role'=>$request->role,'create_at'=>Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString()]);
        return response()->json(['success'=>'success']);

    }
}
