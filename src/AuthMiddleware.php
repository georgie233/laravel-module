<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    protected $exclude = [
        'login','register'
    ];
    public function handle($request, Closure $next)
    {
        $next = $next($request);
//        dd($request->route()->getAction());
        if (!in_array($request->path(),$this->exclude)){
            if (!\Illuminate\Support\Facades\Auth::user()){
                return redirect('/login');
            }
        }
        return $next;
    }
}
