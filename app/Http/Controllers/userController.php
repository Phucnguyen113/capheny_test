<?php

namespace App\Http\Controllers;

use App\Jobs\sendEmailVerify;
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
        if(!p_author('view','tbl_user')){
            return view('error.403');
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
        
        $list_user=$list_user->where($param_search)->orderByDesc('tbl_user.user_id')->paginate(20);
        foreach ($list_user as $users => $Euser) {
            $role=DB::table('tbl_user_role')->join('tbl_role','tbl_role.role_id','=','tbl_user_role.role_id')->where('user_id',$Euser->user_id)->get(['tbl_role.role','tbl_role.role_id'])->toArray();
            $list_user[$users]->role=$role;
        }
        foreach ($list_user as $users => $Euser) {
            $permission=DB::table('tbl_user_permission')->join('tbl_permission','tbl_permission.permission_id','=','tbl_user_permission.permission_id')->where('user_id',$Euser->user_id)->get(['tbl_permission.permission','tbl_permission.permission_id'])->toArray();
            $list_user[$users]->permission=$permission;
        }
        $title='Capheny - Danh sách người dùng';
        return view('admin.user.index',compact('title','list_user','list_province','list_ward','list_district','list_role','list_permission'));
    }
    public function add_form(){
        if(!p_author('add','tbl_user')){
            return view('error.403');
        }
        $list_province=DB::table('tbl_province')->get();
        $title='Capheny - Thêm người dùng';
        return view('admin.user.add',compact('list_province','title'));
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
                'avatar' =>'sometimes|nullable|mimes:jpg,png,jpeg,svg,gif'
            ],
            [
                'required' => ':attribute không được trống',
                'min' => ':attribute phải dài hơn :min ký tự',
                'same' => ':attribute không khớp với mật khẩu',
                'not_in' => ':attribute không được trống',
                'email' => ':attribute không khớp định dạng Email',
                'unique' => ':attribute đã tồn tại',
                'user_name.regex' => ':attribute phải viết liền và không dấu',
                'user_phone.regex' => ':attribute không đúng định dạng số điện thoại',
                'mimes'=>':attribute phải có dạng jpg,png,jpeg,svg,gif'
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
                'user_address'=> 'Địa chỉ',
                'avatar'=> 'Ảnh đại diện'
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
            $idUser=DB::table('tbl_user')->insertGetId(array_merge($request->except(['_token','user_password_confirm','user_password','avatar']),['avatar'=>$newNameImg],['create_at'=>$create_at],['active'=>$active],['user_type'=>$user_type],['password'=>$user_password] ) );
        }else{
            $idUser=DB::table('tbl_user')->insertGetId(array_merge($request->except(['_token','user_password_confirm','user_password','avatar']),['create_at'=>$create_at],['active'=>$active],['user_type'=>$user_type],['password'=>$user_password] ) );
        }
       
        event(new \App\Events\pusherUser(['user_id'=>$idUser,'user_email'=>p_user()['user_email']]));
        p_history(0,'đã thêm người dùng mới #'.$idUser,p_user()['user_id']);
        return response()->json(['success'=>'success']);
    }
    public function edit_form($id){
        if(!p_author('edit','tbl_user')){
           return view('error.403');
        }
        // user action is admin
        $is_admin=false;
        $user_action_role_collection=DB::table('tbl_user_role')->where('user_id',p_user()['user_id'])->get();
        foreach ($user_action_role_collection as $roles => $role) {
            if($role->role_id==1){
                $is_admin=true;
            }
        }
        // check user admin
        if(!$is_admin){
            $user_role_collection=DB::table('tbl_user_role')->where('user_id',$id)->get();
            foreach ($user_role_collection as $roles => $role) {
                if($role->role_id==1){
                    return redirect()->back();
                }
            }
        }
        $user=DB::table('tbl_user')->where('user_id',$id)->first();
        $list_province=DB::table('tbl_province')->get();
        $list_district=DB::table('tbl_district')->where('_province_id',$user->province)->get();
        $list_ward=DB::table('tbl_ward')->where('_district_id',$user->district)->get();
        $title='Capheny - Cập nhật người dùng';
        return view('admin.user.edit',compact('user','list_province','list_district','list_ward','title'));
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
                'avatar'=>'sometimes|nullable|mimes:jpg,png,jpeg,svg,gif'
            ],
            [
                'required' => ':attribute không được trống',
                'min' => ':attribute phải dài hơn :min ký tự',
                'same' => ':attribute không khớp với mật khẩu',
                'not_in' => ':attribute không được trống',
                'email' => ':attribute không khớp định dạng Email',
                'unique' => ':attribute đã tồn tại',
                'user_name.regex' => ':attribute phải viết liền và không dấu',
                'user_phone.regex' => ':attribute không đúng định dạng số điện thoại',
                'mimes'=>':attribute phải có dạng jpg,png,jpeg,svg,gif'
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
                'user_address'=> 'Địa chỉ',
                'avatar'=>'Ảnh đại diện'
            ]
        );
        if($validated->fails()) return response()->json(['error'=> $validated->getMessageBag()]);
        $update_at=Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString();
        if($request->has('active') && $request->active!==2){
            if($request->active==1)$active=1;
            else $active=0;
        }else{
            $active='';
        }
        
        if($request->has('user_type')) $user_type=1;
        else $user_type=0;
       
        if($request->user_password!==null){
            $user_password=bcrypt($request->user_password);
            $data_update=array_merge($request->except(['_token','user_password_confirm','user_password','avatar']),['update_at'=>$update_at],['user_type'=>$user_type],['password'=>$user_password] );
        }else{
            $data_update=array_merge($request->except(['_token','user_password_confirm','user_password','avatar']),['update_at'=>$update_at],['user_type'=>$user_type]);
        }
        if($active!==''){
            $data_update=array_merge($data_update,['active'=>$active]);
        }
        if($request->hasFile('avatar')){
            $newNameImg=$request->avatar->getClientOriginalName().date('Y_m_d').'.'.$request->avatar->getClientOriginalExtension();
            $request->avatar->move('images/user',$newNameImg);
            $data_update=array_merge($data_update,['avatar'=>$newNameImg]);
            $img_old=DB::table('tbl_user')->where('user_id',$id)->first(['avatar']);
            if($img_old->avatar!==null){
                if(file_exists(public_path('images/user/'.$img_old->avatar))){
                    unlink(public_path('images/user/'.$img_old->avatar));
                }
            }
        }
        
        DB::table('tbl_user')->where('user_id',$id)->update($data_update);
        p_history(1,'đã cập nhật người dùng #'.$id,p_user()['user_id']);
        event(new \App\Events\pusherUserEdit(['user_email'=>p_user()['user_email'],'user_id'=>$id]));
        return response()->json(['success'=>'success']);
    }
    public function delete($id){
        if(!p_author('delete','tbl_user')){
            return view('error.403');
        }
        try{
            //check user need delete is admin
            $role_user_collection=DB::table('tbl_user_role')->where('user_id',$id)->get();
            foreach ($role_user_collection as $roles => $role) {
                if($role->role_id==1){
                    return redirect()->back()->withErrors(['admin'=>'error admin']);
                }
            }
            if($id==p_user()['user_id']){
                return redirect()->back()->withErrors(['user_index'=>'error admin']);
            }
            $user=DB::table('tbl_order')->where('user_id',$id)->first();
            if(!empty($user)) return redirect()->back()->withErrors(['error'=>'User đã mua hàng']);
            DB::table('tbl_user')->where('user_id',$id)->delete();
            DB::table('tbl_user_role')->where('user_id',$id)->delete();
            DB::table('tbl_user_permission')->where('user_id',$id)->delete();
            DB::table('tbl_system_ui')->where('user_id',$id)->delete();
            p_history(2,'đã xóa người dùng #'.$id,p_user()['user_id']);
            event(new \App\Events\pusherUserDelete(['user_email'=>p_user()['user_email'],'user_id'=>$id]));
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
    // active user api in page list user
    public function active_api(Request $request){
        $user=DB::table('tbl_user')->where('user_id',$request->id)->first();
        //check user is admin
        $list_role=DB::table('tbl_user_role')->where('user_id',$request->id)->get(['role_id']);
        foreach ($list_role as $roles => $role) {
            if($role->role_id==1){
                return response()->json(['error'=>['admin'=>'failed']]);
            }
        }
        if(empty($user)) return response()->json(['error'=>'error']);
        if($user->active==1){
            $active=0;
        }else{
            $active=1;
        }
        DB::table('tbl_user')->where('user_id',$request->id)->update(['active'=>$active]);
        return response()->json(['success'=>$active]);
    }
    public function verify_form(){
        return view('admin.auth.forgot_password');
    }
    public function confirm_pin_form($token){
        $user=DB::table('tbl_user')->where('verify_token',$token)->first();
        if(empty($user)) return view('error.404');
        return view('admin.auth.confirm_pin',compact('token'));
    }
    public function change_password_form($token){
        $user=DB::table('tbl_user')->where('verify_token',$token)->first();
        if(empty($user)) return view('error.404');
        if($user->verify_check==0 || $user->verify_check==null) return view('error.404');
        return view('admin.auth.change_password',compact('token'));
    }
    public function confirm_pin(Request $request,$token){
        $validated=Validator::make($request->all(),
            [
                'verify_token'=>'bail|required|min:50',
                'verify_pin'=>'bail|required|min:6',

            ],
            [
                'required'=> ':attribute không được trống',
                'min' => ':attribute phải có :min ký tự'
            ],
            [
                'verify_pin'=>'Mã xác nhận'
            ]
        );
        if($validated->fails()) return response()->json(['error'=>$validated->getMessageBag()]);
        $user=DB::table('tbl_user')->where('verify_token','=',$request->verify_token)->first();
        if(empty($user)) return response()->json(['error'=>['verify_token'=>'Không tìm thấy token']]);

        $user=DB::table('tbl_user')->where([
            ['verify_token','=',$request->verify_token],
            ['verify_pin','=',$request->verify_pin]
        ])->first();
        if(empty($user)) return response()->json(['error'=>['verify_pin'=>'Mã xác nhận không đúng']]);
        DB::table('tbl_user')->where([
            ['verify_token','=',$request->verify_token],
            ['verify_pin','=',$request->verify_pin]
        ])->update(['verify_check'=>1]);
        return response()->json(['success'=>true,'token'=>$request->verify_token]);
    }

    public function verify(Request $request){
        $validated=Validator::make($request->all(),
            ['user_email'=>'bail|required|email'],
            [
                'required' => ':attribute không được trống',
                'email' => ':attribute không đúng định dạng'
            ],
            [
                'user_email' => 'Email'
            ]
        );
        if($validated->fails()) return response()->json(['error'=>$validated->getMessageBag()]);
        $user=DB::table('tbl_user')->where('user_email',$request->user_email)->first();
        if(empty($user)) return response()->json(['error'=>['user_email'=>'Không tồn tại email này']]);
        $pin='0123456789';
        $random='';
        for ($i=0; $i <6 ; $i++) { 
            $random.=$pin[rand(0,strlen($pin)-1)];
        }
        $character='qwertyuiopasdfghjklzxcvbnm0123456789QWERTYUIOPASDFGHJKLZXCVBNM';
        $token='';
        for ($i=0; $i <50 ; $i++) { 
            $token.=$character[rand(0,strlen($character)-1)];
        }
       DB::table('tbl_user')->where('user_email',$request->user_email)->update([
           'verify_pin'=>$random,
           'verify_token'=>$token
       ]);
       $user=DB::table('tbl_user')->where('user_email',$request->user_email)->first();
       
       dispatch(new sendEmailVerify([
            'user_name'=>$user->user_first_name.' '.$user->user_last_name,
            'verify_pin' => $user->verify_pin,
            'user_email'=>$user->user_email
        ]))->delay(now()->addSeconds(1));
        return response()->json(['success'=>true,'token'=>$token]);
    }
    public function change_password(Request $request){
        $validated=Validator::make($request->all(),
            [
                'verify_token' => 'bail|required',
                'password' => 'bail|required|min:6',
                'password_confirm'=>'bail|required|same:password'
            ],
            [
                'required' => ':attribute không được rỗng',
                'min' => ':attribute phải có 6 ký tự',
                'same' => ':attribute không trùng với mật khẩu'
            ],
            [
               
                'password' => 'Mật khẩu',
                'password_confirm'=>'Mật khẩu xác nhận'
            ]
        );
        if($validated->fails()) return response()->json(['error'=>$validated->getMessageBag()]);
        $user=DB::table('tbl_user')->where([
            ['verify_token','=',$request->verify_token],
            ['verify_pin','<>',null]
        ])->first();
        if(empty($user)) return response()->json(['error'=>['user'=>'Người dùng không tồn tại, hoặc không yêu cầu đặt lại mật khẩu']]);
        if($user->verify_check==0 || $user->verify_check==null) return response()->json(['error'=>['authen'=>'lỗi xác thực người dùng']]);
        
        
        DB::table('tbl_user')->where('verify_token',$request->verify_token)->update([
            'verify_pin'=>null,
            'password'=>bcrypt($request->password),
            'verify_token'=>null,
            'verify_check'=>0
        ]);
        return response()->json(['success'=>true]);
    }

    public function edit_private_form(){
        $user=DB::table('tbl_user')->where('user_id',p_user()['user_id'])->first();
        $list_province=DB::table('tbl_province')->get();
        $list_district=DB::table('tbl_district')->where('_province_id',$user->province)->get();
        $list_ward=DB::table('tbl_ward')->where('_district_id',$user->district)->get();
        $title='Capheny - Cập nhật người dùng';
       
        return view('admin.user.edit_private',compact('user','list_province','list_district','list_ward'));
    }
    public function edit_private(Request $request){
        $id=p_user()['user_id'];
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
                'avatar'=>'sometimes|nullable|mimes:jpg,png,jpeg,svg,gif'
            ],
            [
                'required' => ':attribute không được trống',
                'min' => ':attribute phải dài hơn :min ký tự',
                'same' => ':attribute không khớp với mật khẩu',
                'not_in' => ':attribute không được trống',
                'email' => ':attribute không khớp định dạng Email',
                'unique' => ':attribute đã tồn tại',
                'user_name.regex' => ':attribute phải viết liền và không dấu',
                'user_phone.regex' => ':attribute không đúng định dạng số điện thoại',
                'mimes'=>':attribute phải có dạng jpg,png,jpeg,svg,gif'
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
                'user_address'=> 'Địa chỉ',
                'avatar'=>'Ảnh đại diện'
            ]
        );
        if($validated->fails()) return response()->json(['error'=> $validated->getMessageBag()]);
        if($request->user_password!==null){
            $user_password=bcrypt($request->user_password);
            $data_update=array_merge($request->except(['_token','user_password_confirm','user_password','avatar']),['password'=>$user_password] );
        }else{
            $data_update=array_merge($request->except(['_token','user_password_confirm','user_password','avatar']));
        }
        
        if($request->hasFile('avatar')){
            $newNameImg=$request->avatar->getClientOriginalName().date('Y_m_d').'.'.$request->avatar->getClientOriginalExtension();
            $request->avatar->move('images/user',$newNameImg);
            $data_update=array_merge($data_update,['avatar'=>$newNameImg]);
            $img_old=DB::table('tbl_user')->where('user_id',$id)->first(['avatar']);
            if($img_old->avatar!==null){
                if(file_exists(public_path('images/user/'.$img_old->avatar))){
                    unlink(public_path('images/user/'.$img_old->avatar));
                }
            }
        }
        
        DB::table('tbl_user')->where('user_id',$id)->update($data_update);
        
        return response()->json(['success'=>'success']);
    }
}
