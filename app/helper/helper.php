<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

if(!function_exists('p_auth')){
    function p_auth($data){
        $user=DB::table('tbl_user')->where([
                                        ['user_email','=',$data['user_email']],
                                        ['user_type','=',1],
                                        ['active','=',1]
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
                // $ui_setting=DB::table('tbl_system_ui')->where(
                //     [
                //         ['user_id','=',session()->get('user')['user_id']],
                //     ]
                // )->get(['name','value']);
                // $ui_setting_parse=[];
                // foreach ($ui_setting as $settings => $setting) {
                //     $ui_setting_parse[$setting->name]=json_decode($setting->value);
                // }
                session(
                    [
                        'user'=>[
                                    'user_id'=>$user->user_id,
                                    'user_email'=>$user->user_email,
                                    'user_name'=>$user->user_name,
                                    'user_phone'=>$user->user_phone,
                                    'role'     => $role,
                                    'permission' => $permission,
                                    'is_admin' => $admin,
                                    // 'ui_setting'=>$ui_setting_parse
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
        if(!in_array($action,['add','edit','delete','view','add_product','active','add_role','edit_role','delete_role','add_permission','edit_permission'])){
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
        if($table !=='tbl_user' && $table!=='tbl_permission' && $table!=='tbl_role'){
            if(in_array(2,$user['role'])){
                return true;
            }
        }

        // $list_role_need=DB::table('tbl_role')->where('tble',$table)->get(['role_id']);
        // // check role user 
        // foreach ($user['role'] as $roles => $role_user) {
        //     foreach ($list_role_need as $role_needs => $role_need) {
        //         if($role_user==$role_need->role_id){
        //             return true;
        //         }
        //     }
        // }

        // list permission of role user
        $list_role_of_permission=DB::table('tbl_role_permission')->whereIn('role_id',$user['role'])->distinct(['permission_id'])->get(['permission_id']);
        $list_role_id_of_perimssion=[];
        foreach($list_role_of_permission as $permissions => $permission){
            $list_role_id_of_perimssion[]=$permission->permission_id;
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
            // check permission in role_user
            if(in_array($rule->permission_id,$list_role_id_of_perimssion)) return true;
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
            $user['permission']=array_merge($user['permission'],$list_role_id_of_perimssion);
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
if(!function_exists('p_get_ui_setting')){
    function p_get_ui_setting(){
        return session()->get('user')['ui_setting'];
    }
}
if(!function_exists('p_ui_setting')){
    function p_ui_setting($table,$column){
        $ui_setting=DB::table('tbl_system_ui')->where([
            ['name','=',$table],
            ['user_id','=',p_user()['user_id']]
        ])->first(['value']);
        if(empty($ui_setting)) return true;
        $ui_setting=json_decode($ui_setting->value);
        if(!isset($ui_setting->$column)) return false;
        if($ui_setting->$column==1) return true;
        return false;
    }
}
if(!function_exists('p_user')){
    function p_user(){
        return session()->get('user');
    }
}
?>