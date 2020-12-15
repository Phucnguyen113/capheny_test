<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Jobs\sendEmailVerify;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

use Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth as FacadesJWTAuth;

class jwtAuthController extends Controller
{   
    private $user;

    public function __construct(User $user){
        $this->user = $user;
    }
   
    public function register(Request $request){
        
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
        $active=1;
        $user_type=0;
        $user_password=bcrypt($request->user_password);
        if($request->hasFile('avatar')){
            $newNameImg=$request->avatar->getClientOriginalName().date('Y_m_d').'.'.$request->avatar->getClientOriginalExtension();
            $request->avatar->move('images/user',$newNameImg);
            DB::table('tbl_user')->insert(array_merge($request->except(['_token','user_password_confirm','user_password','avatar']),['avatar'=>$newNameImg],['create_at'=>$create_at],['active'=>$active],['user_type'=>$user_type],['password'=>$user_password] ) );
        }else{
            DB::table('tbl_user')->insert(array_merge($request->except(['_token','user_password_confirm','user_password','avatar']),['create_at'=>$create_at],['active'=>$active],['user_type'=>$user_type],['password'=>$user_password] ) );
        }
        $credentials = ['user_email'=>$request->user_email,'password'=>$request->user_password];
        $user_info=DB::table('tbl_user')->where([
            ['user_email','=',$request->user_email],
            ['active','=',1]
        ])
        ->join('tbl_province','tbl_province.id','=','tbl_user.province')
        ->join('tbl_district','tbl_district.id','=','tbl_user.district')
        ->join('tbl_ward','tbl_ward.id','=','tbl_user.ward')
        ->first([
            'tbl_user.user_name',
            'tbl_user.user_email',
            'tbl_user.avatar',
            'tbl_user.user_first_name',
            'tbl_user.user_last_name',
            'tbl_province._name as province',
            'tbl_district._name as district',
            'tbl_ward._name as ward',
            'tbl_user.user_address'
        ]);
        $user=JWTAuth::attempt(array_merge($credentials,['active'=>1]));
        return response()->json([
            'status'=> 200,
            'message'=> 'User created successfully',
            'token'=>$user,
            'user_info'=>$user_info
        ]);
    }
    
    public function login(Request $request){
        
        $credentials = $request->only('user_email', 'password');
        $token = null;
        try {
           if (!$token = JWTAuth::attempt(array_merge($credentials,['active'=>1]))) {
            return response()->json(['invalid_email_or_password'], 422);
           }
        } catch (JWTAuthException $e) {
            return response()->json(['failed_to_create_token'], 500);
        }
        $user_info=DB::table('tbl_user')->where([
            ['user_email','=',$request->user_email],
            ['active','=',1]
        ])
        ->join('tbl_province','tbl_province.id','=','tbl_user.province')
        ->join('tbl_district','tbl_district.id','=','tbl_user.district')
        ->join('tbl_ward','tbl_ward.id','=','tbl_user.ward')
        ->first([
            'tbl_user.user_name',
            'tbl_user.user_email',
            'tbl_user.avatar',
            'tbl_user.user_first_name',
            'tbl_user.user_last_name',
            'tbl_province._name as province',
            'tbl_district._name as district',
            'tbl_ward._name as ward',
            'tbl_user.user_address'
        ]);
        return response()->json(compact('token','user_info'));
    }

    public function getUserInfo(Request $request){
        $user = JWTAuth::parseToken()->authenticate();
        return response()->json(['result' => $user]);
    }

    public function verify(Request $request){
        // $validated=Validator::make($request->all(),
        //     ['user_email'=>'bail|required|email'],
        //     [
        //         'required' => ':attribute không được trống',
        //         'email' => ':attribute không đúng định dạng'
        //     ],
        //     [
        //         'user_email' => 'Email'
        //     ]
        // );
        // if($validated->fails()) return response()->json(['error'=>$validated->getMessageBag()]);
        $user=DB::table('tbl_user')->where('user_email',$request->user_email)->first();
        if(empty($user)) return response()->json(['error'=>['Không tồn tại email này']]);
        $pin='0123456789';
        $random='';
        for ($i=0; $i <6 ; $i++) { 
            $random.=$pin[rand(0,strlen($pin)-1)];
        }
       DB::table('tbl_user')->where('user_email',$request->user_email)->update([
           'verify_pin'=>$random
       ]);
       $user=DB::table('tbl_user')->where('user_email',$request->user_email)->first();
       
       dispatch(new sendEmailVerify([
            'user_name'=>$user->user_first_name.' '.$user->user_last_name,
            'verify_pin' => $user->verify_pin,
            'user_email'=>$user->user_email
        ]))->delay(now()->addSeconds(1));
        return response()->json(['success'=>true]);
    }

    public function change_password(Request $request){
        $validated=Validator::make($request->all(),
            [
                'user_email'=>'bail|required|email',
                'verify_pin' => 'bail|required|min:6',
                'password' => 'bail|required|min:6'
            ],
            [
                'required' => ':attribute không được rỗng',
                'email' => ':attribute sai định dạng',
                'min' => ':attribute phải có 6 ký tự'
            ],
            [
                'email' =>'Email',
                'verify_pin'=> 'Mã xác nhận',
                'password' => 'Mật khẩu'
            ]
        );
        if($validated->fails()) return response()->json(['error'=>$validated->getMessageBag()]);
        $user=DB::table('tbl_user')->where([
            ['user_email','=',$request->user_email],
            ['verify_pin','<>',null]
        ])->first();
        if(empty($user)) return response()->json(['error'=>['user'=>'Người dùng không tồn tại, hoặc không yêu cầu đặt lại mật khẩu']]);
        if($user->verify_pin!==$request->verify_pin) return response()->json(['error'=>['verify_pin'=>'Mã xác nhận không chính xác']]);
        DB::table('tbl_user')->where('user_email',$request->user_email)->update([
            'verify_pin'=>null,
            'password'=>bcrypt($request->password)
        ]);
        return response()->json(['success'=>true]);
    }
}  