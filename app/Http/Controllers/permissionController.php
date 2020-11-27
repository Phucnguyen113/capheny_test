<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class permissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkInput');
    }
    public function index(Request $request){
        if(!is_admin()){
            return view('error.403');
        }
        $list_permission=DB::table('tbl_permission')->orderByDesc('permission_id')->paginate(15);
        return view('admin.permission.index',compact('list_permission'));
    }

    public function add_permission_for_user_form(){
        if(!is_admin()){
            return view('error.403');
        }
        $list_user=DB::table('tbl_user')->where('user_type','=',1)->orderByDesc('user_id')->get();
        $list_permission=DB::table('tbl_permission')->orderByDesc('permission_id')->get();
        return view('admin.user.addpermission',compact('list_permission','list_user'));
    }

    public function add_permission(Request $request){
        $validated=Validator::make($request->all(),
            [
                'permission_id'=>'bail|required|array',
                'permission_id.*' => 'bail|numeric',
                'user_id' => 'bail|required|numeric'
            ],
            [
                'permission_id.required' => 'Chưa chọn quyền',
                'user_id.required' =>'Chưa chọn người dùng'
            ],
            [

            ]
        );
        if($validated->fails()) return response()->json(['error'=>$validated->getMessageBag()]);
        //check user is isset
        $user_check=DB::table('tbl_user')->where('user_id',$request->user_id)->first();
        if(empty($user_check)) return response()->json(['error'=>['user_id'=>'Không tìm thấy người dùng này']]);
        $admin=DB::table('tbl_user_role')->where('user_id',$request->user_id)->get(['role_id']);
        foreach ($admin as $adms => $ad) {
            if($ad->role_id==1){
                return response()->json(['error'=>['admin'=>'Đây là Super admin']]);
            }
        }
        for ($i=0; $i <count($request->permission_id) ; $i++) { 
            //check permission is already ?
            $check_permission_isset=DB::table('tbl_user_permission')->where(
                [
                    ['permission_id','=',$request->permission_id[$i]],
                    ['user_id','=',$user_check->user_id]
                ]
            )->first();
           if(!empty($check_permission_isset)) continue;
           DB::table('tbl_user_permission')->insert(['user_id'=>$request->user_id,'permission_id'=>$request->permission_id[$i]]);
        }
        return response()->json(['success'=>'success']);
    }

    public function edit_permission_for_user_form($id){
        if(!is_admin()){
            return view('error.403');
        }
        $user=DB::table('tbl_user')->where('user_id',$id)->first();
        if(empty($user)) return redirect()->back();
        $permission_of_user=DB::table('tbl_user_permission')->where('user_id',$id)->get(['permission_id']);
        $id_permission_of_user=[];
        foreach ($permission_of_user as $permissions => $permission) {
            $id_permission_of_user[]=$permission->permission_id;
        }
        $list_permission=DB::table('tbl_permission')->orderByDesc('permission_id')->get();
        return view('admin.user.editpermission',compact('user','list_permission','id_permission_of_user'));
    }

    public function edit_permission(Request $request,$id){
        $validated=Validator::make($request->all(),
            [
                'permission_id' =>'array|required',
                'permission_id.*' => 'numeric'
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
        //check user is SP admin
        $admin=DB::table('tbl_user_role')->where('user_id',$id)->get(['role_id']);
        foreach ($admin as $adms => $ad) {
            if($ad->role_id==1){
                return response()->json(['error'=>['admin'=>'Đây là Super admin']]);
            }
        }
        //delete permission old 
        DB::table('tbl_user_permission')->where('user_id',$user->user_id)->delete();
        // case remove all permission
        for ($i=0; $i <count($request->permission_id) ; $i++) { 
            if($request->permission_id[$i]==0){
                return response()->json(['success'=>'success']);
            }
        }
        //add new permission
        for ($i=0; $i <count($request->permission_id) ; $i++) { 
            DB::table('tbl_user_permission')->insert(['user_id'=>$user->user_id,'permission_id'=>$request->permission_id[$i]]);
        }
        return response()->json(['success'=>'success']);
    }
    public function add_form(){
        if(!is_admin()){
            return view('error.403');
        }
        return view('admin.permission.add');
    }
    public function add(Request $request){
        $validated=Validator::make($request->all(),
            [
                'permission' => 'bail|required|unique:tbl_permission,permission',
                'action' => 'bail|required',
                'table' => 'bail|required'
            ],
            [
                'permission.required'=>'Quyền không được rỗng',
                'permission.unique' => 'Quyền này đã tồn tại',
                'action.required' => 'Chưa chọn hành động',
                'table.required' => 'Chưa chọn bảng'
            ],
            [

            ]
        );
        if($validated->fails()) return response()->json(['error'=>$validated->getMessageBag()]);
        DB::table('tbl_permission')->insert(['permission'=>$request->permission,'tble'=>$request->table,'action'=>$request->action,'create_at'=>Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString()]);
        return response()->json(['success'=>'success']);
    }
    public function get_action($table){
       try {
            $data=DB::table('tbl_permission')->where('tble',$table)->distinct()->get(['action']);
            return response()->json(['data'=>$data]);
       } catch (\Throwable $th) {
            return response()->json(['error'=>'error']);
       }
    }
}
