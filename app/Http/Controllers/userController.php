<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class userController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkInput');
    }
    public function index(Request $request){
        if(!is_admin()){
            die('Bạn đéo đủ quyền truy cập');
        }
        $list_province=DB::table('tbl_province')->get();
        $list_role=DB::table('tbl_role')->get();
        $list_permission=DB::table('tbl_permission')->get();
        $param_search=[];
        $list_user=DB::table('tbl_user')->join('tbl_province','tbl_province.id','=','tbl_user.province')
        ->join('tbl_district','tbl_district.id','=','tbl_user.district')
        ->join('tbl_ward','tbl_ward.id','=','tbl_user.ward')
        ->select(['tbl_user.*','tbl_district._name as district','tbl_province._name as province','tbl_ward._name as ward']);
         // create_at product process
         if($request->create_at_from!==null && $request->create_at_to!==null){
            $list_user=$list_user->whereBetween('create_at',[$request->create_at_from,$request->create_at_to]);
        }else if($request->create_at_from==null && $request->create_at_to!==null ){
            $list_user=$list_user->whereBetween('create_at',['',$request->create_at_to]);
        }else if($request->create_at_from!==null && $request->create_at_to==null){
            $list_user=$list_user->whereBetween('create_at',[$request->create_at_from,Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString()]);
        }
        // update_at product process
        if($request->update_at_from!==null && $request->update_at_to!==null){
            $list_user=$list_user->whereBetween('update_at',[$request->update_at_from,$request->update_at_to]);
        }else if($request->update_at_from==null && $request->update_at_to!==null){
            $list_user=$list_user->whereBetween('update_at',['',$request->update_at_to]);
        }else if($request->update_at_from!==null && $request->update_at_to==null){
            $list_user=$list_user->whereBetween('update_at',[$request->update_at_from,Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString()]);
        }
        //address process
        $list_district=[];
        if($request->province!==null && $request->province!=="0"){
            $param_search[]=['province','=',$request->province];
            $list_district=DB::table('tbl_district')->where('_province_id',$request->province)->get();
        }
        $list_ward=[];
        if($request->district!==null && $request->district!=="0"){
            $param_search[]=['district','=',$request->district];
            $list_ward=DB::table('tbl_ward')->where('_district_id',$request->district)->get();
        }
        if($request->ward!==null && $request->ward!=="0"){
            $param_search[]=['ward','=',$request->ward];
        }
        // email & phone
        if($request->user_email !==null)  $param_search[]=['user_email','LIKE','%'.$request->user_email.'%'];
        if($request->user_phone !==null)  $param_search[]=['user_phone','LIKE','%'.$request->user_phone.'%'];
        if($request->has('active') && $request->active!=='0'){
            if($request->active==1){
                $param_search[]=['active','=',1];
            }
            if($request->active==2){
                $param_search[]=['active','=',0];
            }
        }
        if($request->has('user_type') && $request->user_type!=='0'){
            if($request->user_type==1){
                $param_search[]=['user_type','=',1];
            }
            if($request->user_type==2){
                $param_search[]=['user_type','=',0];
            }
        }
        $user_id_role=[];
        if($request->has('role') && $request->role!=='0'){
            $role_user=DB::table('tbl_user_role')->where('role_id',$request->role)->get(['user_id']);
            foreach ($role_user as $roles => $role) {
                if(!in_array($role->user_id,$user_id_role)){
                    $user_id_role[]=$role->user_id;
                }
                
            }
        }
        $user_id_permission=[];
        if($request->has('permission') && $request->permission!=='0'){
            $permission_user=DB::table('tbl_user_permission')->where('permission_id',$request->permission)->get(['user_id']);
            foreach ($permission_user as $permissions => $permission) {
                if(!in_array($permission->user_id,$user_id_permission)){
                    $user_id_permission[]=$permission->user_id;
                }
            }
            
        }
     
        if(!empty($user_id_role) && !empty($user_id_permission) ){
            $final_user_id=[];
            foreach ($user_id_role as $key_id1 => $user_id1) {
                foreach ($user_id_permission as $key_id2 => $user_id2) {
                    if($user_id1==$user_id2){
                        $final_user_id[]=$user_id2;
                        unset($user_id_permission[$key_id2]);
                        unset($user_id_role[$key_id1]);
                        break;
                    } 
                }
            }
            
            $list_user=$list_user->whereIn('user_id',$final_user_id);
            
        }else{
            if(!empty($user_id_role) && empty($user_id_permission)){
                $list_user=$list_user->whereIn('user_id',$user_id_role);
            }else{
                if(empty($user_id_role) && !empty($user_id_permission)){
                    $list_user=$list_user->whereIn('user_id',$user_id_permission);
                }
            }
        }
        
        $list_user=$list_user->where($param_search)->paginate(15);
        return view('admin.user.index',compact('list_user','list_province','list_ward','list_district','list_role','list_permission'));
    }
    public function add_form(){
        if(!is_admin()){
            die('Bạn đéo đủ quyền truy cập');
        }
        $list_province=DB::table('tbl_province')->get();
        return view('admin.user.add',compact('list_province'));
    }
    public function add(Request $request){
        $validated=Validator::make($request->all(),
            [
                'user_name'     => 'bail|required|min:5|regex:/^[A-z0-9]*$/',
                'user_email'    => [
                                    'bail',
                                    'required',
                                    'email',
                                    Rule::unique('tbl_user')
                                ],
                'user_password' => 'bail|required|min:6',
                'user_password_confirm'=> 'bail|required|min:6|same:user_password',
                'user_first_name' => 'bail|required',
                'user_last_name' => 'bail|required',
                'user_phone' => 'bail|required|numeric|regex:/^0[0-9]{9,10}$/',
                'province'=> 'bail|required|not_in:0',
                'district'=> 'bail|required|not_in:0',
                'ward'=> 'bail|required|not_in:0',
                'user_address'=> 'bail|required',
                'avatar' =>'sometimes|nullable|image'
            ],
            [
                'required' => ':attribute không được trống',
                'min' => ':attribute phải dài hơn :min ký tự',
                'same' => ':attribute không khớp với mật khẩu',
                'not_in' => ':attribute không được trống',
                'email' => ':attribute không khớp định dạng Email',
                'unique' => ':attribute đã tồn tại',
                'user_name.regex' => ':attribute phải viết liền và không dấu',
                'user_phone.regex' => ':attribute không đúng định dạng số điện thoại'
            ],
            [
                'user_name'  => 'Tên tài khoản',
                'user_email' => 'Email',
                'user_password' => 'Mật khẩu',
                'user_password_confirm' => 'Mật khẩu xác nhận',
                'user_first_name' =>'Tên',
                'user_last_name' =>'Họ',
                'user_phone' => 'Điện thoại',
                'province'=> 'Thành phố/Tỉnh',
                'district'=> 'Quận/Huyện',
                'ward'=> 'Khu vực',
                'user_address'=> 'Địa chỉ'
            ]
        );
        if($validated->fails()) return response()->json(['error'=>$validated->getMessageBag()]);
        $create_at=Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString();
        if($request->has('active')) $active=1;
        else $active=0;
        if($request->has('user_type')) $user_type=1;
        else $user_type=0;
        $user_password=bcrypt($request->user_password);
        if($request->hasFile('avatar')){
            $newNameImg=$request->avatar->getClientOriginalName().date('Y_m_d').'.'.$request->avatar->getClientOriginalExtension();
            $request->avatar->move('images/user',$newNameImg);
            DB::table('tbl_user')->insert(array_merge($request->except(['_token','user_password_confirm','user_password','avatar']),['avatar'=>$newNameImg],['create_at'=>$create_at],['active'=>$active],['user_type'=>$user_type],['user_password'=>$user_password] ) );
        }else{
            DB::table('tbl_user')->insert(array_merge($request->except(['_token','user_password_confirm','user_password','avatar']),['create_at'=>$create_at],['active'=>$active],['user_type'=>$user_type],['user_password'=>$user_password] ) );
        }
       
       
        return response()->json(['success'=>'success']);
    }
    public function edit_form($id){
        if(!is_admin()){
            die('Bạn đéo đủ quyền truy cập');
        }
        $user=DB::table('tbl_user')->where('user_id',$id)->first();
        $list_province=DB::table('tbl_province')->get();
        $list_district=DB::table('tbl_district')->where('_province_id',$user->province)->get();
        $list_ward=DB::table('tbl_ward')->where('_district_id',$user->district)->get();
        return view('admin.user.edit',compact('user','list_province','list_district','list_ward'));
    }
    public function edit(Request $request,$id){
        $data= $request->all();
        if($request->user_password==null)  $data= $request->except(['user_password']);
        $validated=Validator::make($data,
            [
                'user_name'     => 'bail|required|min:5|regex:/^[A-z0-9]*$/',
                'user_email'    => [
                                    'bail',
                                    'required',
                                    'email',
                                    Rule::unique('tbl_user')->ignore($id,'user_id')
                                ],
                'user_password' => 'bail|sometimes|nullable|required|min:6',
                'user_password_confirm'=> 'bail|required_unless:user_password,null|same:user_password',
                'user_first_name' => 'bail|required',
                'user_last_name' => 'bail|required',
                'user_phone' => 'bail|required|numeric|regex:/^0[0-9]{9,10}$/',
                'province'=> 'bail|required|not_in:0',
                'district'=> 'bail|required|not_in:0',
                'ward'=> 'bail|required|not_in:0',
                'user_address'=> 'bail|required',
                'avatar'=>'sometimes|nullable|image'
            ],
            [
                'required' => ':attribute không được trống',
                'min' => ':attribute phải dài hơn :min ký tự',
                'same' => ':attribute không khớp với mật khẩu',
                'not_in' => ':attribute không được trống',
                'email' => ':attribute không khớp định dạng Email',
                'unique' => ':attribute đã tồn tại',
                'user_name.regex' => ':attribute phải viết liền và không dấu',
                'user_phone.regex' => ':attribute không đúng định dạng số điện thoại'
            ],
            [
                'user_name'  => 'Tên tài khoản',
                'user_email' => 'Email',
                'user_password' => 'Mật khẩu',
                'user_password_confirm' => 'Mật khẩu xác nhận',
                'user_first_name' =>'Tên',
                'user_last_name' =>'Họ',
                'user_phone' => 'Điện thoại',
                'province'=> 'Thành phố/Tỉnh',
                'district'=> 'Quận/Huyện',
                'ward'=> 'Khu vực',
                'user_address'=> 'Địa chỉ'
            ]
        );
        if($validated->fails()) return response()->json(['error'=> $validated->getMessageBag()]);
        $update_at=Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString();
        if($request->has('active')) $active=1;
        else $active=0;
        if($request->has('user_type')) $user_type=1;
        else $user_type=0;
       
        if($request->user_password!==null){
            $user_password=bcrypt($request->user_password);
            $data_update=array_merge($request->except(['_token','user_password_confirm','user_password','avatar']),['update_at'=>$update_at],['active'=>$active],['user_type'=>$user_type],['user_password'=>$user_password] );
        }else{
            $data_update=array_merge($request->except(['_token','user_password_confirm','user_password','avatar']),['update_at'=>$update_at],['active'=>$active],['user_type'=>$user_type]);
        }
        if($request->hasFile('avatar')){
            $newNameImg=$request->avatar->getClientOriginalName().date('Y_m_d').'.'.$request->avatar->getClientOriginalExtension();
            $request->avatar->move('images/user',$newNameImg);
            $data_update=array_merge($data_update,['avatar'=>$newNameImg]);
            $img_old=DB::table('tbl_user')->where('user_id',$id)->first(['avatar']);
            if(file_exists(public_path('images/user/'.$img_old->avatar))){
                unlink(public_path('images/user/'.$img_old->avatar));
            }
        }
        
        DB::table('tbl_user')->where('user_id',$id)->update($data_update);
        return response()->json(['success'=>'success']);
    }
    public function delete($id){
        if(!is_admin()){
            die('Bạn đéo đủ quyền truy cập');
        }
        try{
            $user=DB::table('tbl_order')->where('user_id',$id)->first();
            if(!empty($user)) return redirect()->back()->withErrors(['error'=>'User đã mua hàng']);
            DB::table('tbl_user')->where('user_id',$id)->delete();
            return redirect()->back()->withErrors(['success'=>'success']);
        }catch(\Exception $e){
            return redirect()->back()->withErrors(['error_sv'=>'Lỗi không xác định']);
        }
        
    }
    public function user($id){
        try {
            $data=DB::table('tbl_user')
            ->where('user_id',$id)->first(['user_first_name','user_last_name','user_email','user_phone','province','district','ward','user_address']);
            $list_district=DB::table('tbl_district')->where('_province_id',$data->province)->get();
            $list_ward=DB::table('tbl_ward')->where('_district_id',$data->district)->get();
            return response()->json(['user'=>$data,'list_district'=>$list_district,'list_ward'=>$list_ward]);
        } catch (Exception $e){
            return response()->json(['error'=>'server error'],500);
        }
        
    }
}
