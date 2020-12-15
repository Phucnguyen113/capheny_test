<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class p_auth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        
        if(session()->has('user')){
            p_fresh();
            return $next($request);
        }else{
            return redirect('admin/auth')->with('redirect',$request->path());
        }
        
    }
}
