<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class roleController extends Controller
{
    public function index(Request $request){
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
        return view('admin.role.index');
    }
    public function add_role_for_user_form(){
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
}
