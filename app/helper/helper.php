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
        if(!in_array($action,['add','edit','delete','view','add_product','active'])){
            echo 'Action is invalid<br>';
            echo 'Action must be in Array Rule';
            die();
        }

        if(!in_array($table,['tbl_user','tbl_category','tbl_product','tbl_comment','tbl_order','tbl_store','tbl_size','tbl_color'])){
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

        switch ($table) {
            case 'tbl_user':
                $rule=[
                    'add'    => 1,
                    'edit'   => 2,
                    'delete' => 3,
                    'view'   => 4,
                ];
                $role=6;
                break;
            case 'tbl_product':
                $rule=[
                    'add'    => 5,
                    'edit'   => 6,
                    'delete' => 7,
                    'view'   => 8,
                    'active' => 36
                ];
                $role=4;
                break;
            case 'tbl_category':
                $rule=[
                    'add'    => 9,
                    'edit'   => 10,
                    'delete' => 11,
                    'view'   => 12,
                    'active' => 35
                ];
                $role=3;
                break;
            case 'tbl_order':
                $rule=[
                    'add'    => 13,
                    'edit'   => 14,
                    'delete' => 15,
                    'view'   => 23,
                ];
                $role=9;
                break;
            case 'tbl_store':
                $rule=[
                    'add'    => 16,
                    'edit'   => 17,
                    'delete' => 18,
                    'view'   => 24,
                    'add_product'=>19
                ];
                $role=8;
                break;
            case 'tbl_color':
                $rule=[
                    'add'    => 20,
                    'edit'   => 21,
                    'delete' => 22,
                    'view'   => 26,
                ];
                $role=10;
                break;
            case 'tbl_size':
                $rule=[
                    'add'    => 27,
                    'edit'   => 28,
                    'delete' => 29,
                    'view'   => 30,
                ];
                $role=11;
                break;
            case 'tbl_comment':
                $rule=[
                    'add'    => 31,
                    'edit'   => 32,
                    'delete' => 33,
                    'view'   => 34,
                ];
                $role=7;
                break;                
            default:
                # code...
                break;
        }
        // check role user 
        foreach ($user['role'] as $roles => $role_user) {
            if($role_user==$role){
                return true;
            }
        }
        if(!$view){
            //check in controller
            if(!isset($rule[$action])) return false;
            if(in_array($rule[$action],$user['permission'])){
                return true;
            }
        
            if($excetion){

            }
            return false;
        }else{
            // check in blade view
            foreach ($user['permission'] as $permissions => $user_permission) {
                if(in_array($user_permission,$rule)) return true;
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