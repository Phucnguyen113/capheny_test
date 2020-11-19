<?php

namespace App\Http\Controllers;

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
        $list_permission=DB::table('tbl_permission')->orderByDesc('permission_id')->paginate(15);
        return view('admin.permission.index',compact('list_permission'));
    }
    public function add_permission_for_user_form(){
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
}
