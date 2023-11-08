<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Lcobucci\JWT\Parser as JwtParser;
use DB;
use App\Models\User;
trait PassportToken {

    public function getUserDataUsingToken($request,$token = ''){
        $token = $request->bearerToken();
        $tokenId = app(JwtParser::class)->parse($token)->claims()->get('jti');
        $data = DB::table('oauth_access_tokens')->where('id',$tokenId)->first();
        if($data){
           return  User::with('getUserDetail')->find($data->user_id);
        }
        return false;
    }
	
	public function getUserDetailsUsingId($request){
        $userId = $request->user_id;
		
        if($userId)
        {
           return  User::with('getUserDetail')->find($userId);
		}
        return false;
    }
}

?>