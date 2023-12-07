<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use Lcobucci\JWT\Parser as JwtParser;
use DB;
use App\Models\User;
class PassportToken extends BaseController
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
        if(!$request->api_token){
            return $this->sendError([],trans('messages.token_missing'),401);
        }
		if(!$request->user_id){
            return $this->sendError([],trans('messages.user_id_missing'),401);
        }

        try{
            $token = $request->api_token;
			$user_id = $request->user_id;
            ## Get user data from token / check user exist
			$userExists = User::where('id',$user_id)->first();
			if($userExists){
				if($userExists->api_token!= $token){
					return  $this->sendError([],trans('messages.token_invalid'),401);
				}
			}else{
				return  $this->sendError([],trans('messages.user_not'),401);
			}
        } 
        catch(\Exception $e){ 
            return $this->sendError([],trans('messages.token_invalid'),401);
        }
        return $next($request);
    }
}
