<?php

use Carbon\Carbon;
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
            if(Hash::check($data['password'],$user->password)){
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
                // list permission of role user
                $list_role_of_permission=DB::table('tbl_role_permission')->whereIn('role_id',$role)->distinct(['permission_id'])->get(['permission_id']);
                $list_role_id_of_perimssion=[];
                foreach($list_role_of_permission as $permissions => $permissionf){
                    $list_role_id_of_perimssion[]=$permissionf->permission_id;
                }
                session(
                    [
                        'user'=>[
                                    'user_id'=>$user->user_id,
                                    'user_email'=>$user->user_email,
                                    'user_name'=>$user->user_name,
                                    'user_phone'=>$user->user_phone,
                                    'user_image'=>$user->avatar,
                                    'role'      =>$role,
                                    'permission'=>$permission,
                                    'list_permission_of_role'=>$list_role_id_of_perimssion
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
if(!function_exists('p_fresh')){
    function p_fresh(){
        
        $user=DB::table('tbl_user')->where('user_id',p_user()['user_id'])->first();
        
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
        // list permission of role user
        $list_role_of_permission=DB::table('tbl_role_permission')->whereIn('role_id',$role)->distinct(['permission_id'])->get(['permission_id']);
        $list_role_id_of_perimssion=[];
        foreach($list_role_of_permission as $permissions => $permissionf){
            $list_role_id_of_perimssion[]=$permissionf->permission_id;
        }
        session(
            [
                'user'=>[
                            'user_id'=>$user->user_id,
                            'user_email'=>$user->user_email,
                            'user_name'=>$user->user_name,
                            'user_phone'=>$user->user_phone,
                            'user_image'=>$user->avatar,
                            'role'      =>$role,
                            'permission'=>$permission,
                            'list_permission_of_role'=>$list_role_id_of_perimssion
                            // 'ui_setting'=>$ui_setting_parse
                        ]
            ]
    );
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
        
        $role=p_user()['role'];
        
       
        $permission=p_user()['permission'];
       

        if(in_array(1,$role)){
            return true;
        }
        if($table !=='tbl_user' && $table!=='tbl_permission' && $table!=='tbl_role'){
            if(in_array(2,$role)){
                return true;
            }
        }

       
        $list_role_id_of_perimssion=p_user()['list_permission_of_role'];
      
       
        if(!$view){
            //check in controller
            $rule=DB::table('tbl_permission')->where([
                ['tble',$table],
                ['action',$action]
            ])->first();
            if(empty($rule)) return false;
            if(in_array($rule->permission_id,$permission)){
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
            $permission=array_merge($permission,$list_role_id_of_perimssion);
            foreach ($permission as $permissions => $user_permission) {
                if(in_array($user_permission,$rule_id)) return true;
            }
            return false;
        }
    }
}
if(!function_exists('is_admin')){
    function is_admin(){
        if(session()->has('user')){
            $user=session()->get('user');
            $role_collection=DB::table('tbl_user_role')->where('user_id',$user['user_id'])->get(['role_id'])->toArray();
            $role=[];
            foreach ($role_collection as $roles => $rolef) {
                $role[]=$rolef->role_id;
            }
            if(in_array(1,$role)){
                return true;
            }
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
        if(session()->has('ui_setting')){
            $ui_setting=session()->get('ui_setting');
        }else{
            $ui_setting=DB::table('tbl_system_ui')->where([
            ['name','=',$table],
            ['user_id','=',p_user()['user_id']]
            ])->first(['value']);
            if(empty($ui_setting)) return true;
            session()->flash('ui_setting',$ui_setting);
        }
       
        
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
if(!function_exists('p_redirect_login_admin')){
    function p_redirect_login_admin(){
        $id=p_user()['user_id'];
        $role_path=DB::table('tbl_user_role')
        ->join('tbl_role','tbl_role.role_id','=','tbl_user_role.role_id')
        ->where('tbl_user_role.user_id',$id)->first();
        if(!empty($role_path)){
            return redirect($role_path->url_path);
        }
        $permission_path=DB::table('tbl_user_permission')
        ->join('tbl_permission','tbl_permission.permission_id','=','tbl_user_permission.permission_id')
        ->where('tbl_user_permission.user_id',$id)->get()->toArray();
        if(!empty($permission_path)){
            foreach ($permission_path as $permissions => $permission) {
               if($permission->url_path!==null){
                   return redirect($permission->url_path);
               }
            }
            return view('error.role-er');
        }
    }
}
if(!function_exists('p_method')){
    function p_method($method){
        return '<input type="hidden" name="_method" value="'.$method.'">';
    }
}
if(!function_exists('p_history')){
    function p_history($history_type,$history,$user_id){
        $now=Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString();
        DB::table('tbl_history')->insert([
            'history_type'=>$history_type,
            'history'=>$history,
            'user_id'=>$user_id,
            'create_at'=>$now
        ]);
    }
}
?>