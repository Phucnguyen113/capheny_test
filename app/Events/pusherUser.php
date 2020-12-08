<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class pusherUser implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $message;
    public function __construct($message)
    {
        $user=DB::table('tbl_user')->join('tbl_province','tbl_province.id','=','tbl_user.province')
        ->join('tbl_district','tbl_district.id','=','tbl_user.district')
        ->join('tbl_ward','tbl_ward.id','=','tbl_user.ward')
        ->select(['tbl_user.*','tbl_district._name as district','tbl_province._name as province','tbl_ward._name as ward'])
        ->where('user_id',$message)->first();
        
        $role=DB::table('tbl_user_role')->join('tbl_role','tbl_role.role_id','=','tbl_user_role.role_id')->where('user_id',$user->user_id)->get(['tbl_role.role','tbl_role.role_id'])->toArray();
        $permission=DB::table('tbl_user_permission')->join('tbl_permission','tbl_permission.permission_id','=','tbl_user_permission.permission_id')->where('user_id',$user->user_id)->get(['tbl_permission.permission','tbl_permission.permission_id'])->toArray();
        $user->role=$role;
        $user->permission=$permission;
        $html='<tr>';
        $html.=' <td >'.$user->user_first_name.'&nbsp; '.$user->user_last_name.'</td>';
        $html.=' <td class="email p_setting"';
        if(!p_ui_setting('user','email')) $html.=' style="display:none"';
        $html.='>'.$user->user_email.'</td>';
        $html.='<td class="phone p_setting"';
        if(!p_ui_setting('user','phone')) $html.=' style="display:none"';
        $html.='>'.$user->user_phone.'</td>'; 
        $html.='<td class="province p_setting"';
        if(!p_ui_setting('user','province')) $html.=' style="display:none"';
        $html.='>'.$user->province.'</td>';
        $html.='<td class="district p_setting"';
        if(!p_ui_setting('user','district')) $html.=' style="display:none"';
        $html.=' >'.$user->district.'</td>';
        $html.='<td class="ward p_setting"';
        if(!p_ui_setting('user','ward')) $html.=' style="display:none"';
        $html.=' >'.$user->ward.'</td>';
        $html.='<td class="address p_setting"';
        if(!p_ui_setting('user','address')) $html.=' style="display:none"';
        $html.='>'.$user->user_address.'</td>';
        $html.='<td class="admin p_setting"';
        if(!p_ui_setting('user','admin')) $html.=' style="display:none"';
        $html.='>';
        if($user->user_type==1) $html.='<i class="fas fa-check" style="color:#3ac47d"></i>';
        else $html.='<i class="fas fa-times" style="color:#b81f44"></i>';
        $html.=' </td>';
        $html.=' <td class="role p_setting"';
        if(!p_ui_setting('user','role')) $html.=' style="display:none"';
        $html.=' >';
        if(p_author('edit_role','tbl_user')){
            if($user->user_type==1){
                if(!empty($user->role)){
                    $roleText=''; 
                    foreach ($user->role as $roles => $role) {
                        $roleText.=$role->role.',';  
                    } 
                    $html.=' <a href="'.url('admin/user').'/'.$user->user_id.'/editrole" title="'.rtrim($roleText,',').'">';
                    $html.=Str::limit(rtrim($roleText,','),30);
                    $html.='</a>';
                }else{
                     $html.=' <a href="'.url('admin/user').'/'.$user->user_id.'/editrole" style="font-size:25px" >+</a>'   ;
                }
            }
        }else{
            if($user->user_type==1){
                if(!empty($user->role)){
                    $roleText=''; 
                    foreach ($user->role as $roles => $role) {
                        $roleText.=$role->role.',';  
                    } 
                  
                    $html.=Str::limit(rtrim($roleText,','),30);
                   
                }
            }
        }
        $html.='</td>';
        $html.=' <td class="permission p_setting"';
        if(!p_ui_setting('user','permission')) $html.=' style="display:none"';
        $html.=' >';
        if(p_author('edit_permission','tbl_user')){
            if($user->user_type==1){
                $html.='<a href="'.url('admin/user').'/'.$user->user_id.'/editpermission" >';
                if(!empty($user->permission)){
                    $permissionText='';  
                    foreach ($user->permission  as $permissions => $permission) {
                        $permissionText.=$permission->permission.',';
                    }
                    $html.=Str::limit(rtrim($permissionText,','),30);
                    $html.='</a>';
                }else{
                    $html.='<a href="'.url('admin/user').'/'.$user->user_id.'/editpermission" style="font-size:25px" >+</a>';
                }
            }
        }else{
            if($user->user_type==1){
              
                if(!empty($user->permission)){
                    $permissionText='';  
                    foreach ($user->permission  as $permissions => $permission) {
                        $permissionText.=$permission->permission.',';
                    }
                    $html.=Str::limit(rtrim($permissionText,','),30);
                  
                }
            }
        }
        $html.='</td>';
        $html.='<td class="active p_setting"';
        if(!p_ui_setting('user','active')) $html.=' style="display:none"';
        $html.='>';
        if(!p_author('active','tbl_user')){
            if($user->active==1){
                $html.='<i class="fas fa-check" style="color:#3ac47d"></i>';
            }else{
                $html.='<i class="fas fa-times" style="color:#b81f44"></i>';
            }
        }else{
            if($user->active==1){
                $html.='<a href="#active" class="p_user_active" data-id="'.$user->user_id.'"><i class="fas fa-check" style="color:#3ac47d"></i></a>';
            }else{
                $html.='<a href="#" class="p_user_active" data-id="'.$user->user_id.'"><i class="fas fa-times" style="color:#b81f44"></i></a>';
            }
        }
        $html.='</td>';
        $html.=' <td class="create_at p_setting"';
        if(!p_ui_setting('user','create_at')) $html.=' style="display:none"';
        $html.='>'.$user->create_at.'</td>';
        $html.='<td class="update_at p_setting"';
        if(!p_ui_setting('user','update_at')) $html.='style="display:none"';
        $html.='>'.$user->update_at.'</td>';
        $html.='<td class="action p_setting"';
        if(!p_ui_setting('user','action')) $html.='style="display:none"';
        $html.='>';
        if(p_author('edit','tbl_user')){
            $html.='<a class="btn btn-primary" style="display:inline-block"';
            $html.='href="'.url('admin/user/').'/'.$user->user_id.'/edit"><div class="fa fa-edit"></div></a>';
        }
        if(p_author('delete','tbl_user')){
            $html.='<form action="'.url('admin/user').'/'.$user->user_id.'/delete" method="post" style="display:inline-block;margin:0">';
            $html.=csrf_field();
            $html.=' <button type="submit" class="btn btn-danger"><div class="fa fa-trash-alt"></div></button>';
            $html.='</form>';
        }
        $html.='</td>';
        $html.='</tr>';
        $this->message=$html;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return ['user-add'];
    }
}
