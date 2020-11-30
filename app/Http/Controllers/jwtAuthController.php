<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

use Hash;

class jwtAuthController extends Controller
{   
    private $user;

    public function __construct(User $user){
        $this->user = $user;
    }
   
    public function register(Request $request){
        // $user = $this->user->create([
        //   'name' => $request->get('name'),
        //   'email' => $request->get('email'),
        //   'password' => Hash::make($request->get('password'))
        // ]);
       

        return response()->json([
            'status'=> 200,
            'message'=> 'User created successfully',
            // 'data'=>$user
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
        return response()->json(compact('token'));
    }

    public function getUserInfo(Request $request){
        $user = JWTAuth::toUser($request->token);
        return response()->json(['result' => $user]);
    }
}  