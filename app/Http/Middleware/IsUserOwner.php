<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class IsUserOwner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {   
        // check co phai dung user do dang sua thong tin cua ho khong
        if($request->user()->id !== (int) $request->route('id') ){
            return response()->json(['error' => 'You are not authorized to modify this user data.'], 403);
        }

        return $next($request);
    }
}
