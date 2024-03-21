<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class localization
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
       // return $next($request);
	   // Check header request and determine localizaton
		 $local = ($request->hasHeader('localization')) ? $request->header('localization') : 'en';
		//$local = ($request->localization) ? $request->localization : 'en';
		
		//$local =$request->localization;
		 // set laravel localization
		 app()->setLocale($local);
		// continue request
		return $next($request);
    }
}
