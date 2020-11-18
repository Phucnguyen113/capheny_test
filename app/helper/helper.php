<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

if(!function_exists('p_auth')){
    function p_auth($data){
        $user=DB::table('tbl_user')->where([
                                        ['user_email','=',$data['user_email']],
                                        ['user_type','=',1]
        ])->first();
        if(!empty($user)){
            if(Hash::check($data['user_password'],$user->user_password)){
                $role_collection=DB::table('tbl_user_role')->where('user_id',$user->user_id)->get(['role_id'])->toArray();
                $role=[];
                foreach ($role_collection as $roles => $rolef) {
                  $role[]=$rolef->role_id;
                }
                $permission_collection=DB::table('tbl_user_permission')->where('user_id',$user->user_id)->get(['permission_id'])->toArray();
                $permission=[];
                foreach ($permission_collection as $permissions => $permissionf) {
                    $permission[]=$permissionf->permission_id;
                }
                $admin=false;
                if(in_array(1,$role)){
                    $admin=true;
                }
                session(
                    [
                        'user'=>[
                                    'user_email'=>$user->user_email,
                                    'user_name'=>$user->user_name,
                                    'user_phone'=>$user->user_phone,
                                    'role'     => $role,
                                    'permission' => $permission,
                                    'is_admin' => $admin
                                ]
                    ]
            );
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
}
if(!function_exists('p_logout')){
    function p_logout(){
        if(session()->has('user')){
            session()->forget('user');
        }
       
    }
}
if(!function_exists('p_check')){
    function p_check(){
        if(session()->has('user')){
            return true;
        }else{
            return false;
        }
    }
}


if(!function_exists('p_author')){
    function p_author($action,$table,$excetion=false,$view=false){
        if(!in_array($action,['add','edit','delete','view','add_product','active','add_role','edit_role','delete_role'])){
            echo 'Action is invalid<br>';
            echo 'Action must be in Array Rule';
            die();
        }

        if(!in_array($table,['tbl_user','tbl_category','tbl_product','tbl_comment','tbl_order','tbl_store','tbl_size','tbl_color','tbl_user_role','tbl_user_permission','tbl_role','tbl_permission'])){
            echo 'Table is invalid<br>';
            echo 'undefined table '.$table;
            die();
        }
        if(!session()->has('user')){  
            echo 'User not logined';
            die();
        }

        $user=session()->get('user');
        if($user['is_admin']){
            return true;

        }
        if($table !=='tbl_user'){
            if(in_array(2,$user['role'])){
                return true;
            }
        }

        $list_role_need=DB::table('tbl_role')->where('tble',$table)->get(['role_id']);
        // check role user 
        foreach ($user['role'] as $roles => $role_user) {
            foreach ($list_role_need as $role_needs => $role_need) {
                if($role_user==$role_need->role_id){
                    return true;
                }
            }
        }

        if(!$view){
            //check in controller
            $rule=DB::table('tbl_permission')->where([
                ['tble',$table],
                ['action',$action]
            ])->first();
            if(empty($rule)) return false;
            if(in_array($rule->permission_id,$user['permission'])){
                return true;
            }
        
            if($excetion){

            }
            return false;
        }else{
            // check in blade view
            $rule=DB::table('tbl_permission')->where('tble',$table)->get();
            $rule_id=[];
            foreach ($rule as $rules => $rule_) {
                $rule_id[]=$rule_->permission_id;
            }
            foreach ($user['permission'] as $permissions => $user_permission) {
                if(in_array($user_permission,$rule_id)) return true;
            }
            return false;
        }
    }
}
if(!function_exists('is_admin')){
    function is_admin(){
        if(session()->has('user')){
            return session()->get('user')['is_admin'];
        }else{
            return false;
        }
    }
}

?>