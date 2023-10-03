<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Session;
use Auth;
class CheckRole
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
        // dd(auth()->user()->roles[0]->name);
        if (isset(auth()->user()->roles[0]->name) && auth()->user()->roles[0]->name == 'User') {
            Session::flash('warning', trans('messages.access_denied'));
            Auth::logout();
            return redirect('/login');
        }
        return $next($request);
    }
}
