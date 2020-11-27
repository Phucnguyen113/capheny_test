<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class authorController extends Controller
{
    public function is_admin(){
       return is_admin();
    }
    
}
