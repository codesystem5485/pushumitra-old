<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use App\Http\Controllers\BaseController as BaseController;
use DB;
use Carbon\Carbon;
use Auth;
use Validator;
use config;
use URL;
use Hash;
class LoginController extends BaseController
{
    private $userRepo;
    /**
     * Create a new controller construct.
     *
     * @return void
     */

    public function __construct(
        UserRepositoryInterface $userRepository
    )
    {
        $this->userRepo = $userRepository;
    }

    public function showLoginForm(){
      
        return view('auth.login');   
    }

    public function checkCredential(Request $request){

        $postData = $request->all();
        $validator = Validator::make($postData, [
            'email' => 'required',
            'password'=>'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['message'=>$validator->errors()->all(),'statusCode'=>404]);
        }

        $response = [];
        $password = Hash::make($request->password);
        
        $user = $this->userRepo->getSingleRecords(['email' => $request->email]);

        if(!$user)
        {
            $user = $this->userRepo->getSingleRecords(['mobile_number' => $request->email]);
        }        
        if ($user) {
            $check = Hash::check($request->password, $user->password); 
            if($check)
            {            
                try{
                    if(Auth::loginUsingId($user->id)){ 
                        $response = [];
                        return $this->sendResponse($response,trans('messages.login_success'),200);  
                    } else{
                        return  $this->sendError([],trans('messages.something'),400);
                    }
                }
                catch(\Exception $e){   
                   return  $this->sendError([],trans('messages.something'),500);
                }   
            }
            else
            {
                return $this->sendError($response,trans('messages.invalid_credentials'),404);
            }  
        } else {
            return $this->sendError($response,trans('messages.user_not'),404);
        }
    }

    public function logout(Request $request)
    {
        // $this->guard()->logout();
        $request->session()->flush();
        $request->session()->regenerate();
        return redirect('auth/login'); 
    }
}
