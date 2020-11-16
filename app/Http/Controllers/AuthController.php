<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login_form(){
        return view('admin.auth.login');
    }
    public function login(Request $request){
        if( p_auth($request->only(['user_email','user_password']))){
           
        }else{
            return redirect()->back();
        }
    }
    public function logout(){
        p_logout();
        return redirect('admin/auth');
    }
    public function check(){
        if(p_check()){
            dd(session()->get('user'));
            echo 'Đã đăng nhập';
        }else{
            echo 'Chưa đăng nhập';
        }
    }
}
