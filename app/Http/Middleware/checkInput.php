<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class checkInput
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
        foreach ($request->all() as $requests => $value) {
            if($value==null) continue;
            $value=preg_replace("/\'/","",$value);
            $value=preg_replace('/\"/',"",$value);
            $value=preg_replace("/<script>/","",$value);
            $value=preg_replace("/<\/script>/","",$value);
            $request[$requests]=$value;
        }
       
        return $next($request);
    }
}
