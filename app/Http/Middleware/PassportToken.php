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
        if(!$request->bearerToken()){
            return $this->sendError([],trans('messages.token_missing'),401);
        }
        try{
            $token = $request->bearerToken();
            $tokenId = app(JwtParser::class)->parse($token)->claims()->get('jti');
            ## Get user data from token / check user exist
            $data = DB::table('oauth_access_tokens')->where('id',$tokenId)->first();
            if(!$data){
                return  $this->sendError([],trans('messages.user_not'),config('constants.status_code.not_found'));
            }
        } 
        catch(\Exception $e){ 
            return $this->sendError([],trans('messages.token_invalid'),401);
        }
        return $next($request);
    }
}
