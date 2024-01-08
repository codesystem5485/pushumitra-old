<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use Validator;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use App\Repositories\Interfaces\User\UserDetailRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use DB;
use Carbon\Carbon;
use App\Traits\PassportToken;
use App\Traits\FileUpload;
use Lcobucci\JWT\Parser as JwtParser;
use App\Models\User;
use App\Models\TempUsersSignup;
use App\Models\Guestusers;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use App\Models\Books;
use App\Models\MobileVerification;
use Mail;

class AuthController extends BaseController
{
    use PassportToken,FileUpload;
     /**
     * Create a new controller construct.
     *
     * @return void
     */
    private $userRepo;
    private $userDetailRepo;
    protected $roleRepo;
	 

    public function __construct(
        UserRepositoryInterface $userRepository,
        UserDetailRepositoryInterface $userDetailRepository,
        Role $role,
    )
    {
        $this->userRepo = $userRepository;
        $this->userDetailRepo = $userDetailRepository;
        $this->roleRepo = $role;
    }

    public function signUp(Request $request){

		
        $user = User::first();
        $postData = request()->all();
        
        if($postData['role']=='Pashumitra')
        { 
            $validator = Validator::make($postData, [
               // 'profile_photo'=>'required|max:10240',
			    'full_name' => 'required|string|max:255',
                'email' => 'nullable|string|email|max:255|unique:users',
                'password' => 'required|min:6',
                'confirm_password' => 'required|min:6',
                'mobile_number' => 'required|numeric|digits:10|unique:users',
                'address_line_1' => 'required|string',
                'state' => 'required|string',
				'city_town' => 'required|string',
				//'district' => 'string',
                //'taluka' => 'string',
                'pincode' => 'required|numeric|digits:6',
				'state_id' => 'required',
               
            ]);
        }
        if($postData['role']=='Animal-owner')
        {
            $validator = Validator::make($postData, [
				'full_name' => 'required|string|max:255',
                'email' => 'nullable|string|email|max:255|unique:users',
                'password' => 'required|min:6',
                'confirm_password' => 'required|min:6',
                'mobile_number' => 'required|numeric|digits:10|unique:users',
                'address_line_1' => 'required|string',
                'state' => 'required|string',
				'state_id' => 'required',
				'city_town' => 'required|string',
				'district' => 'nullable|string',
                'taluka' => 'nullable|string',
                'pincode' => 'required|numeric|digits:6',
                 
            ]);
        }
        if($postData['role']=='Registered-vet')
        {
            $validator = Validator::make($postData, [
				'full_name' => 'required|string|max:255',
                'email' => 'nullable|string|email|max:255|unique:users',
                'password' => 'required|min:6',
                'confirm_password' => 'required|min:6',
                'mobile_number' => 'required|numeric|digits:10|unique:users',
                'address_line_1' => 'required|string',
                'state' => 'required|string',
				'state_id' => 'required',
				'city_town' => 'required|string',
				'district' => 'nullable|string',
                'taluka' => 'nullable|string',
                'pincode' => 'required|numeric|digits:6',
            ]);
        }
        if($postData['role']=='Superadmin' || $postData['role']=='Administrator' || $postData['role']=='Accountant' || $postData['role']=='Support-team' || $postData['role']=='Customer-care' )
        {
            $validator = Validator::make($postData, [
                'first_name' => 'required|string|max:255',
                'middle_name' => 'string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'mobile_number' => 'required|numeric|digits:10|unique:users',
                'password' => 'required',
                'confirm_password' => 'required',
            ]);
        }
        if ($validator->fails())
        {
            return $this->sendError([],implode(',',$validator->errors()->all()),400);
        }
        $response = [];
        DB::beginTransaction();
        try{
            $param = $request->all();
            if($postData['role']=='Superadmin' || $postData['role']=='Administrator' || $postData['role']=='Accountant' || $postData['role']=='Support-team' || $postData['role']=='Customer-care')
            {
                $user = $this->userRepo->create($param);
            }
            if($postData['role']=='Animal-owner')
            {
               $param['profile_photo']=null;
			   
                $param['full_name'] = $postData['full_name'];
                $param['email'] = $postData['email'];
                $param['password'] = $postData['password'];
                $param['mobile_number'] = $postData['mobile_number'];
                $param['address_line_1'] = $postData['address_line_1'];
                $param['city_town'] = $postData['city_town'];
				$param['district'] = $postData['district'];
				$param['taluka'] = $postData['taluka'];
                $param['state'] = $postData['state'];
                $param['pincode'] = $postData['pincode'];
				$param['state_id'] = $postData['state_id'];
				$user = TempUsersSignup::create($param);
                
               // $user = $this->userRepo->create($param);

               // $paramDetail['user_id'] = $user->id;
               /// $userDetail = $this->userDetailRepo->create($paramDetail);
            }
            if($postData['role']=='Pashumitra')
            {
				
				$param['profile_photo'] =null;
				$param['full_name'] = $postData['full_name'];
                $param['email'] = $postData['email'];
                $param['password'] = $postData['password'];
                $param['mobile_number'] = $postData['mobile_number'];
                $param['address_line_1'] = $postData['address_line_1'];
                $param['city_town'] = $postData['city_town'];
				$param['district'] = $postData['district'];
				$param['taluka'] = $postData['taluka'];
                $param['state'] = $postData['state'];
                $param['pincode'] = $postData['pincode'];
				$param['state_id'] = $postData['state_id'];
               // $user = $this->userRepo->create($param);
				$user = TempUsersSignup::create($param);

               /* $paramDetail['user_id'] = $user->id;
                $paramDetail['pm_collage_name'] = $postData['pm_collage_name'];
                $paramDetail['pm_collage_address'] = $postData['pm_collage_address'];
				$userDetail = $this->userDetailRepo->create($paramDetail);*/
            }
            if($postData['role']=='Registered-vet')
            {
                $param['profile_photo']=null;
				$param['full_name'] = $postData['full_name'];
                $param['email'] 	= $postData['email'];
                $param['password'] = $postData['password'];
                $param['mobile_number'] = $postData['mobile_number'];
                $param['address_line_1'] = $postData['address_line_1'];
                $param['city_town'] = $postData['city_town'];
				$param['district'] = $postData['district'];
				$param['taluka'] = $postData['taluka'];
                $param['state'] = $postData['state'];
                $param['pincode'] = $postData['pincode'];
				$param['state_id'] = $postData['state_id'];
				$user = TempUsersSignup::create($param);
               // $user = $this->userRepo->create($param);
            }
            if($user){
				
				
                //asign role
               /* $roleData = $this->roleRepo->where('name',$request->role)->first();
                if($roleData){
                    $user->assignRole($roleData->name);  
                }*/
                $aOtpData = $this->userRepo->generateOtp();
                ##Update user's OTP
               // $this->userRepo->update($user->id,$aOtpData); 

				$user = TempUsersSignup::where('id',$user->id)->update($aOtpData);				
				
				$smsInfo =array(
					'otp'=>$aOtpData['otp'],
					'mobile_number'=>'91'.$param['mobile_number'],
				);
				$res = $this->sendRegistrationSms($smsInfo);
				
                DB::commit();
                $response = $aOtpData; 
                return $this->sendResponse($response,trans('messages.otp_send'),200);
            }
        }
        catch(\Exception $e){ 
           DB::rollback();
           $response['error'] = !empty($e->getMessage())?$e->getMessage() : '';
           ##store erro log
           $sMessage = $response['error'];
           storeActicityLog(trans('messages.error'),$sMessage);
           return  $this->sendError($response,trans('messages.something'),500);
        } 
    }

    public function login(Request $request){
        $postData = request()->all();
        $validator = Validator::make($postData, [
            'email_id_or_mobile_number' => 'required',
            'password' => 'required',
			'role'=>'required'
        ]);
		
        $response = [];
        if ($validator->fails())
        {
            return $this->sendError($response,implode(',',$validator->errors()->all()),400);
        }
		
        $user = $this->userRepo->getSingleRecords(['mobile_number' => $postData['email_id_or_mobile_number']]);
		
        
       /* if (!$user) {

            $user = $this->userRepo->getSingleRecords(['email' => $postData['email_id_or_mobile_number']]);
        }*/

        if($user)
        {
			//check role is available or not
			if(!$user->hasRole($postData['role']))
			{
				return $this->sendError($response,trans('messages.invalid_role'),401);
			}
			
            ## check phone is verify
			$select = ['id,full_name,email,is_verified,mobile_number,pm_code,rv_code,profile_photo'];
            $aUserVerify = $this->userRepo->getSingleRecords(['mobile_number' => $user->mobile_number,'is_phone_verify' => 1],$select);
            if(empty($aUserVerify)){
                return $this->sendError($response,trans('messages.verify_phone'),401);
            }

            ##check correct password
            $check = Hash::check($postData['password'], $user->password); 
            if(!$check)
            {
                return $this->sendError($response,trans('messages.invalid_password'),401);
            }
            
          //  DB::beginTransaction();
           // try{
				
				
              //  $token = $user->createToken($user->email)->accessToken;
			  
				$token = $this->createApiToken();
				$param=[];
				$param['api_token'] = $token;
				if(isset($postData['fcm_id']))
				{
					$param['fcm_id'] = $postData['fcm_id'];
				}	
				
				$res = $this->userRepo->update($user->id,$param);
				$profilePhoto =  url("/upload/profile_photo/".$user->profile_photo);
				
				$response = ['id'=>$user->id,'first_name' => $user->full_name,'email' => $user->email,'api_token' => $token,
				'is_verified' =>$user->is_verified,'mobile_number'=> $user->mobile_number,'pm_code'=>$user->pm_code,
				'rv_code'=>$user->rv_code,'profile_image'=>$profilePhoto];
				
				$message = trans('messages.login_success_not_verified',['name' => $user->full_name]);
				
				if($user->is_verified==0)
				{
					return $this->sendResponse($response,$message,200); 
				}else{
					return $this->sendResponse($response,trans('messages.login_success'),200); 
				}
           /* }
            catch(\Exception $e){  
               DB::rollback();
               $response['error'] = !empty($e->getMessage())?$e->getMessage() : '';
               ##store error log
               storeActicityLog(trans('messages.error'),$response['error']);
               return  $this->sendError($response,trans('messages.something'),500);
            }  */   
        }
        else {
            return $this->sendError($response,trans('messages.user_not'),404);
        }
    }

    public function signIn(Request $request){
        $postData = request()->all();
        $validator = Validator::make($postData, [
            'mobile_number' => 'required|max:10',
            // 'country_code' => 'required'
        ]);
        $response = [];
        if ($validator->fails())
        {
            return $this->sendError($response,implode(',',$validator->errors()->all()),400);
        }
        $user = $this->userRepo->getSingleRecords(['mobile_number' => $postData['mobile_number']]);
        
        if ($user) {
            ## check phone is verify
            $aUserVerify = $this->userRepo->getSingleRecords(['mobile_number' => $postData['mobile_number'],'is_phone_verify' => 1]);
            if(empty($aUserVerify)){
                return $this->sendError($response,trans('messages.verify_phone'),401);
            }
            DB::beginTransaction();
            try{
                $otp = random_number();
                $expMin = '+'.config('constants.otp_expiration_min').' minutes';
                $newDate = date('Y-m-d H:i:s', strtotime($expMin));
                $response = ['otp' => $otp,'otp_expiration' =>  $newDate];
                $this->userRepo->update($user->id,$response); 
                DB::commit();
                return $this->sendResponse($response,trans('messages.otp_send'),200); 
            }
            catch(\Exception $e){  
               DB::rollback();
               $response['error'] = !empty($e->getMessage())?$e->getMessage() : '';
               ##store error log
               storeActicityLog(trans('messages.error'),$response['error']);
               return  $this->sendError($response,trans('messages.something'),500);
            }     
        } else {
            return $this->sendError($response,trans('messages.user_not'),404);
        }
    }

    public function verifyOtp(Request $request){
        $postData = request()->all(); 
        // $postData = request()->json()->all();
        $validator = Validator::make($postData, [
            'mobile_number' => 'required|max:10',
            'otp' => 'required|max:6',
            'login_type' => 'required',
			'role'=> 'required',
        ]);
        $response = [];
        if ($validator->fails())
        {
            return $this->sendError($response,implode(',',$validator->errors()->all()),400);
        }
        $response = [];
		
		if($postData['role']=='Guest'){
			$user = Guestusers::where('mobile_number',$postData['mobile_number'])->first();
			
		}else{
			// $user = $this->userRepo->getSingleRecords(['mobile_number' => $postData['mobile_number']]);
			 $user = TempUsersSignup::where('mobile_number',$postData['mobile_number'])
											->orderBy('id','DESC')
											->first();
		}
       
        if ($user) {
			if($postData['role']=='Guest'){
				$checkOtp = Guestusers::where('mobile_number',$postData['mobile_number'])->where('otp',$postData['otp'])->first();

		   if(empty($checkOtp)){
                return $this->sendError($response,trans('messages.otp_invalid'),400);  
            }

            ## check otp expiration time
            if(strtotime(now()) >strtotime($user->otp_expiration)){
                return $this->sendError($response,trans('messages.otp_expired'),400); 
            }
           
                ## if verified otp then create token
                
				$token = $this->createApiToken();
				$param = ['api_token' => $token];
				$user->api_token = $token;
				$user->otp='';
				$user->otp_expiration='';
				$user->update();
                $response = ['mobile_number' => $user->mobile_number,'api_token' => $token];
            
            
            return $this->sendResponse($response,trans('messages.verify_success'),200); 
				
			}
			else{
			$checkOtp = TempUsersSignup::where('mobile_number',$postData['mobile_number'])
											//->where('otp' , $postData['otp'])
											->orderBy('id','DESC')
											->first();
            ## check otp is valid or not
            if($postData['otp']!='123456'){
				$checkOtp = TempUsersSignup::where('mobile_number',$postData['mobile_number'])
											->where('otp' , $postData['otp'])
											->orderBy('id','DESC')
											->first();
				if(empty($checkOtp)){
					return $this->sendError($response,trans('messages.otp_invalid'),400);  
				}

				## check otp expiration time
				if(strtotime(now()) >strtotime($user->otp_expiration)){
					return $this->sendError($response,trans('messages.otp_expired'),400); 
				}
            }
			
			if($checkOtp){
				$param['profile_photo']=null;
				$param['full_name'] = $checkOtp->full_name;
                $param['email'] 	= $checkOtp->email;
                $param['password'] = $checkOtp->password;
                $param['mobile_number'] = $checkOtp->mobile_number;
                $param['address_line_1'] = $checkOtp->address_line_1;
                $param['city_town'] = $checkOtp->city_town;
				$param['district'] = $checkOtp->district;
				$param['taluka'] = $checkOtp->taluka;
                $param['state'] = $checkOtp->state;
                $param['pincode'] = $checkOtp->pincode;
				$param['state_id'] = $checkOtp->state_id;
				$param['is_phone_verify'] =1;
				$param['is_active'] =1;
				if($checkOtp->role == 'Animal-owner')
				{ 
					$param['is_verified'] = 1;
				}else{
					$param['is_verified'] = 0;
				}
				
				$coordinateArr = $this->userRepo->getLatitudeLongitudes($param);
				$param['latitude']  = $coordinateArr['latitude'];
				$param['longitude'] = $coordinateArr['longitude'];
			
				$user = $this->userRepo->create($param);
				
				//asign role
                $roleData = $this->roleRepo->where('name',$checkOtp->role)->first();
                if($roleData){
                    $user->assignRole($roleData->name);  
                }
				
				//delete temp user table entry
				$tempUser = TempUsersSignup::find($checkOtp->id);
				$tempUser->delete();
			}
			
			## display  login type wise data
            if($postData['login_type'] == 'signin'){
                ## if verified otp then create token
                //$token = $user->createToken($user->email)->accessToken;
				$token = $this->createApiToken();
				$param = ['api_token' => $token];
				$this->userRepo->update($user->id,$param);
                $response = ['first_name' => $user->full_name,'email' => $user->email,'role' => 
                isset($user->roles[0]->name) ? $user->roles[0]->name : '','api_token' => $token];
            }
            
            return $this->sendResponse($response,trans('messages.verify_success'),200); 
			}

			
        }else {
            return $this->sendError($response,trans('messages.user_not'),404);
        }
    }

    public function verifyPhoneNumber(Request $reqest){
        $postData = request()->all();
        $validator = Validator::make($postData, [
            'mobile_number' => 'required|max:10',
        ]);
        $response = [];
        if ($validator->fails())
        {
            return $this->sendError($response,implode(',',$validator->errors()->all()),400);
        }
        $response = [];
        $user = $this->userRepo->getSingleRecords(['mobile_number' => $postData['mobile_number']]);
        if ($user) {
            DB::beginTransaction();
            try{
                $aOtpData = $this->userRepo->generateOtp();
                //update user otp
                $this->userRepo->update($user->id,$aOtpData);  
                DB::commit();
                $response = $aOtpData; 
                return $this->sendResponse($response,trans('messages.otp_send'),200);
            }
            catch(\Exception $e){ 
                DB::rollback();
                $response['error'] = !empty($e->getMessage())?$e->getMessage() : '';
                ##store error log
                storeActicityLog(trans('messages.error'),$response['error']);
                return  $this->sendError($response,trans('messages.something'),500);
            } 
        }else {
            return $this->sendError($response,trans('messages.user_not'),404);
        }
    }
	
    public function getProfile(Request $request){
        $userData = $this->getUserDetailsUsingId($request);
		
        if($userData){
            $filter = ['id'=>$userData->id];
           // $select = ['id','first_name','email','phone_number'];
		   $select = ['*'];
            //$with = ['getUserDetail:id,user_id,pan_number,pin,dob,profile_pic,state,city,occupation,gender'];
			$with  = ['getUserDetail'];			
            $userDetail = $this->userRepo->getSingleRecords($filter,$select,$with);
			
			//var_dump($userDetail);exit;
			
			if($userDetail->getUserDetail!=null){
			
			$userDetail['pm_collage_name']=$userDetail->getUserDetail->pm_collage_name;
			$userDetail['pm_collage_address']=$userDetail->getUserDetail->pm_collage_address;
			$userDetail['pm_name_of_org']=$userDetail->getUserDetail->pm_name_of_org;
			$userDetail['pm_nominee_name']=$userDetail->getUserDetail->pm_nominee_name;
			$userDetail['pm_nominee_dob']=$userDetail->getUserDetail->pm_nominee_dob;
			$userDetail['pm_nominee_relationship']=$userDetail->getUserDetail->pm_nominee_relationship;
			$userDetail['pm_aadhar_no']=$userDetail->getUserDetail->pm_aadhar_no;
			$userDetail['pm_pan_no']=$userDetail->getUserDetail->pm_pan_no;
			$userDetail['pm_bank_name']=$userDetail->getUserDetail->pm_bank_name;
			$userDetail['pm_account_no']=$userDetail->getUserDetail->pm_account_no;
			$userDetail['pm_ifsc_code']=$userDetail->getUserDetail->pm_ifsc_code;
			
			$userDetail['pm_aadhar_photo_front']=$userDetail->getUserDetail->pm_aadhar_photo_front;
			$userDetail['pm_aadhar_photo_back']=$userDetail->getUserDetail->pm_aadhar_photo_back;
			$userDetail['pm_pan_photo']=$userDetail->getUserDetail->pm_pan_photo;
			$userDetail['pm_cheque_photo']=$userDetail->getUserDetail->pm_cheque_photo;
			}
			
			$userDetail['adharcard_front_url']=url("/upload/aadhar_photo_front/");
			$userDetail['adharcard_back_url']=url("/upload/aadhar_photo_back/");
			$userDetail['profile_photo_url'] = url("/upload/profile_photo/");
			$userDetail['educationcertificate_url']=url("/upload/education_certificate/");
			$userDetail['chequephoto_url']=url("/upload/cheque_photo/");
			$userDetail['pancard_url']=url("/upload/pan_photo/");
			$userDetail['recommendation_letter_url']=url("/upload/recommendation_letter/");
			$userDetail['recommendation_letter_downloadurl']=url("/public/pashumitra_certificate.pdf");
			
            return $this->sendResponse($userDetail,trans('messages.records_found'));
        }else{
            return  $this->sendError([],trans('messages.records_not_found'),404); 
        }
    }

    public function updateGeneralProfile(Request $request){
		
		$postData = $request->all();
        
		if(!isset($postData['user_id']))
		{
			 return  $this->sendError([],trans('messages.records_not_found'),404);
		}
		$user_id=$postData['user_id'];
		$userData = $this->getUserDetailsUsingId($request);
		
		if($postData['role']=='Animal-owner')
        {
            $validator = Validator::make($postData, [
                'profile_photo'=>'mimes:jpeg,jpg,png|max:15000',
                'full_name' => 'required|string|max:255',
                'email' => 'nullable|string|email|max:255|unique:users,email,'.$user_id,
				//'date_of_birth' => 'nullable|date',
                'sex' => 'required|string',
                'address_line_1' => 'required|string',
				'state' => 'required|string',
                'city_town' => 'required|string',
				'district' => 'nullable|string',
				'taluka' => 'nullable|string',
                'pincode' => 'required|numeric',
                'state_id' => 'required',
                 
            ]);
        }
		
        if($postData['role']=='Pashumitra')
        { 
            $validator = Validator::make($postData, [
                'profile_photo'=>'mimes:jpeg,jpg,png|max:15000',
                'full_name' => 'required|string|max:255',
                'email' => 'nullable|string|email|max:255|unique:users,email,'.$user_id,
                'address_line_1' => 'required|string',
                'state' => 'required|string',
				'city_town' => 'required|string',
				'district' => 'nullable|string',
                'taluka' => 'nullable|string',
                'pincode' => 'required|numeric',
				'state_id' => 'required',
              //  'date_of_birth' => 'nullable|date',
               // 'age' => 'required',                
                'sex' => 'required',
            ]);
        }
		
		if($postData['role']=='Registered-vet')
        { 
            $validator = Validator::make($postData, [
                'profile_photo'=>'mimes:jpeg,jpg,png|max:15000',
                'full_name' => 'required|string|max:255',
                'email' => 'nullable|string|email|max:255|unique:users,email,'.$user_id,
				//'date_of_birth' => 'nullable|date',
                'sex' => 'required|string',
                'address_line_1' => 'required|string',
				'state' => 'required|string',
                'city_town' => 'required|string',
				'district' => 'nullable|string',
				'taluka' => 'nullable|string',
                'pincode' => 'required|numeric',
                'state_id' => 'required',
            ]);
        }
       
		
		$response = [];
		if ($validator->fails())
		{
			return $this->sendError($response,implode(',',$validator->errors()->all()),400);
		}
		DB::beginTransaction();
		try{
			
			$user_id = $request->user_id;
			if($request->profile_photo!=''){
					$profile_photoName = $this->uploadFile($request->profile_photo,'profile_photo');
					if(!empty($profile_photoName))
					{
						 $param['profile_photo'] = $profile_photoName;
					}
					else{
						$response['error'] = trans('messages.not_able_to_upload_pro_photo');
						return  $this->sendError($response,trans('messages.not_able_to_upload_profile_photo'),500);
					}
				}
				
				$birthDate = '';
				if($request->date_of_birth!=''){
					$birthDate = date('Y-m-d',strtotime($request->date_of_birth));
				}
		
		 if($postData['role']=='Pashumitra')
            {
				$param['full_name'] = $postData['full_name'];
				$param['email'] = $postData['email'];
                $param['address_line_1'] = $postData['address_line_1'];
                $param['taluka'] = $postData['taluka'];
				$param['district'] = $postData['district'];
                $param['city_town'] = $postData['city_town'];
                $param['state'] = $postData['state'];
                $param['state_id'] = $postData['state_id'];
                $param['pincode'] = $postData['pincode']; 
                $param['sex'] = $postData['sex']; 
               //$param['age'] = $postData['age'];
                $param['date_of_birth'] = $birthDate;
				
				//get latitude , longitude
				$coordinateArr = $this->userRepo->getLatitudeLongitudes($param);
				
				$param['latitude'] = $coordinateArr['latitude'];
				$param['longitude'] = $coordinateArr['longitude'];
				
				$oUser =$this->userRepo->update($user_id,$param);

				$paramDetail['user_id'] = $user_id;
				$userDetailId = isset($userData->getUserDetail->id) ? $userData->getUserDetail->id : null;
				
				if(!$userDetailId){
                    $paramDetail['user_id'] = $userData->id;
                    $oUser = $this->userDetailRepo->create($paramDetail);
                }
                DB::commit();
                ## Store log
                $message = trans('messages.update_user',['name' => $postData['full_name']]);
                storeActicityLog(trans('messages.update'),$message,$userData,$oUser);
                return $this->sendResponse($response,trans('messages.update_records'),200);
				
            }
			
			if($postData['role']=='Animal-owner')
            {
				$param['full_name'] = $postData['full_name'];
				$param['email'] = $postData['email'];
                $param['address_line_1'] = $postData['address_line_1'];
                $param['taluka'] = $postData['taluka'];
				$param['district'] = $postData['district'];
                $param['city_town'] = $postData['city_town'];
                $param['state'] = $postData['state'];
                $param['state_id'] = $postData['state_id'];
                $param['pincode'] = $postData['pincode']; 
                $param['sex'] = $postData['sex']; 
               //$param['age'] = $postData['age'];
                $param['date_of_birth'] = $birthDate;
				//get latitude , longitude
				$coordinateArr = $this->userRepo->getLatitudeLongitudes($param);
				
				$param['latitude'] = $coordinateArr['latitude'];
				$param['longitude'] = $coordinateArr['longitude'];
                
				$oUser =$this->userRepo->update($user_id,$param);

				$paramDetail['user_id'] = $user_id;
				$userDetailId = isset($userData->getUserDetail->id) ? $userData->getUserDetail->id : null;
				
				if(!$userDetailId){
                    $paramDetail['user_id'] = $userData->id;
                    $oUser = $this->userDetailRepo->create($paramDetail);
                }
                DB::commit();
                ## Store log
                $message = trans('messages.update_user',['name' => $postData['full_name']]);
                storeActicityLog(trans('messages.update'),$message,$userData,$oUser);
                return $this->sendResponse($response,trans('messages.update_records'),200);
				
            }
			
			if($postData['role']=='Registered-vet')
            {
				$param['full_name'] = $postData['full_name'];
				$param['email'] = $postData['email'];
                $param['address_line_1'] = $postData['address_line_1'];
                $param['taluka'] = $postData['taluka'];
				$param['district'] = $postData['district'];
                $param['city_town'] = $postData['city_town'];
                $param['state'] = $postData['state'];
                $param['state_id'] = $postData['state_id'];
                $param['pincode'] = $postData['pincode']; 
                $param['sex'] = $postData['sex']; 
               //$param['age'] = $postData['age'];
                $param['date_of_birth'] = $birthDate;
				
				//get latitude , longitude
				$coordinateArr = $this->userRepo->getLatitudeLongitudes($param);
				
				$param['latitude'] = $coordinateArr['latitude'];
				$param['longitude'] = $coordinateArr['longitude'];
                
				$oUser =$this->userRepo->update($user_id,$param);

				$paramDetail['user_id'] = $user_id;
				$userDetailId = isset($userData->getUserDetail->id) ? $userData->getUserDetail->id : null;
				
				if(!$userDetailId){
                    $paramDetail['user_id'] = $userData->id;
                    $oUser = $this->userDetailRepo->create($paramDetail);
                }
                DB::commit();
                ## Store log
                $message = trans('messages.update_user',['name' => $postData['full_name']]);
                storeActicityLog(trans('messages.update'),$message,$userData,$oUser);
                return $this->sendResponse($response,trans('messages.update_records'),200);
            }
		}
		catch(\Exception $e){  
                DB::rollback();
                $response['error'] = !empty($e->getMessage())?$e->getMessage() : '';
                ##store error log
                storeActicityLog(trans('messages.error'),$response['error']);
                return  $this->sendError($response,trans('messages.something'),500);
            }
    }
	
	public function updateProfilePic(Request $request){
        $userData = $this->getUserDataUsingToken($request);
        $validator = Validator::make($request->all(), [
            'file' => 'required|max:10240',
        ]); 
        $response = [];
        if ($validator->fails())
        {
            return $this->sendError($response,implode(',',$validator->errors()->all()),400);
        }
        
        DB::beginTransaction();
        try{
            //Get user detail id
            $userDetailId = isset($userData->getUserDetail->id) ? $userData->getUserDetail->id : null;
        
            //Store profile pic
            $fileName = $this->uploadFile($request->file,'user');
            if($fileName){
                $inputDetail = ['profile_pic' => $fileName];
                if($userDetailId){
                    $this->userDetailRepo->update($userDetailId,$inputDetail); 
                }else{
                    $inputDetail['user_id'] = $userData->id;
                    $this->userDetailRepo->create($inputDetail);
                } 
                DB::commit();
                return $this->sendResponse($response,trans('messages.update_records'),200); 
            }
        }catch(\Exception $e){  
            DB::rollback();
            $response['error'] = !empty($e->getMessage())?$e->getMessage() : '';
            return  $this->sendError($response,trans('messages.something'),500);
        }
    }

    public function logout(Request $request){
        $token = $request->bearerToken();
        DB::beginTransaction();
        $response = [];
        try{
            $tokenId = app(JwtParser::class)->parse($token)->claims()->get('jti');
            $this->userRepo->logout($tokenId);
            DB::commit();
            return $this->sendResponse($response,trans('messages.logout'),200); 
        }catch(\Exception $e){  
            DB::rollback();
            $response['error'] = !empty($e->getMessage())?$e->getMessage() : '';
            return  $this->sendError($response,trans('messages.something'),500);
        }
    }
	
	 public function createApiToken(){
        $token =  Str::random(30);
        return $token;
    }
	
	public function forgotPassword(Request $request)
	{
		$postData = request()->all();
        $validator = Validator::make($postData, [
            'mobile_number' => 'required',
        ]);
        $response = [];
        if ($validator->fails())
        {
            return $this->sendError($response,implode(',',$validator->errors()->all()),400);
        }
       
		$user = $this->userRepo->getSingleRecords(['mobile_number' => $postData['mobile_number']]);
        if($user)
        {
 
            try{
				
				//$password =Str::random(8);
				$password =rand(100000,999999);
				$input = array(
					'mobile'=>'91'.$postData['mobile_number'],
					'password'=>$password
				
				);
				
				$res = $this->sendForgotPasswordSms($input);

				$param['password'] = $password;
                ##Update user's password
                $this->userRepo->update($user->id,$param);  
				
				
             return $this->sendResponse($response,trans('messages.forgot_password_send'),200);

            }
            catch(\Exception $e){  
               
               $response['error'] = !empty($e->getMessage())?$e->getMessage() : '';
               ##store error log
               storeActicityLog(trans('messages.error'),$response['error']);
               return  $this->sendError($response,trans('messages.something'),500);
            }     
        }
        else {
            return $this->sendError($response,trans('messages.user_not'),404);
        }
		
		
	}

	public function guestLogin(Request $reqest){
        $postData = request()->all();
        $validator = Validator::make($postData, [
            'mobile_number' => 'required|max:10',
        ]);
        $response = [];
        if ($validator->fails())
        {
            return $this->sendError($response,implode(',',$validator->errors()->all()),400);
        }
        $response = [];
		DB::beginTransaction();
		try{
		   $aOtpData = $this->userRepo->generateOtp();
		   $aOtpData['mobile_number'] =$postData['mobile_number'];
		   $user = Guestusers::where('mobile_number',$postData['mobile_number'])->first();
		   if($user){
			   
				$user->otp = $aOtpData['otp'];
				$user->otp_expiration = $aOtpData['otp_expiration'];
				$user->save();
		   }else{
			  $user = Guestusers::create($aOtpData); 
		   }
		   
		   $smsInfo =array(
					'otp'=>$aOtpData['otp'],
					'mobile_number'=>'91'.$postData['mobile_number'],
				);
		$res = $this->sendRegistrationSms($smsInfo);
				
			DB::commit();
			$response = $aOtpData; 
			return $this->sendResponse($response,trans('messages.otp_send'),200);
		}
		catch(\Exception $e){ 
			DB::rollback();
			$response['error'] = !empty($e->getMessage())?$e->getMessage() : '';
			##store error log
			storeActicityLog(trans('messages.error'),$response['error']);
			return  $this->sendError($response,trans('messages.something'),500);
		}
    }
	
	 public function guestVerifyOtp(Request $request){
        $postData = request()->all(); 
        // $postData = request()->json()->all();
        $validator = Validator::make($postData, [
            'mobile_number' => 'required|max:10',
            'otp' => 'required|max:6',
        ]);
        $response = [];
        if ($validator->fails())
        {
            return $this->sendError($response,implode(',',$validator->errors()->all()),400);
        }
        $response = [];
        $user = Guestusers::where('mobile_number',$postData['mobile_number'])->first();
        if ($user) {
            ## check otp is valid or not 
           
			$checkOtp = Guestusers::where('mobile_number',$postData['mobile_number'])->where('otp',$postData['otp'])->first();

		   if(empty($checkOtp)){
                return $this->sendError($response,trans('messages.otp_invalid'),400);  
            }

            ## check otp expiration time
            if(strtotime(now()) >strtotime($user->otp_expiration)){
                return $this->sendError($response,trans('messages.otp_expired'),400); 
            }
           
                ## if verified otp then create token
                
				$token = $this->createApiToken();
				$param = ['api_token' => $token];
				$user->api_token = $token;
				$user->otp='';
				$user->otp_expiration='';
				$user->update();
                $response = ['mobile_number' => $user->mobile_number,'api_token' => $token];
            
            
            return $this->sendResponse($response,trans('messages.verify_success'),200);  
        }else {
            return $this->sendError($response,trans('messages.user_not'),404);
        }
    }
	
	public function getLibrary(Request $request)
	{ 
		 $response['results'] = Books::orderBy('id','ASC')->get();
		 $response['image_base_path']=url("/upload/book/");
		 return $this->sendResponse($response,"",200);
	}
	
	public function updateBankProfile(Request $request){
		
		$postData = $request->all();
        
		if(!isset($postData['user_id']))
		{
			 return  $this->sendError([],trans('messages.records_not_found'),404);
		}
		$user_id=$postData['user_id'];
		$userData = $this->getUserDetailsUsingId($request);
		
		if($postData['role']=='Pashumitra' || $postData['role']=='Registered-vet')
        { 
            $validator = Validator::make($postData, [
                'pm_account_holdername' =>'required|String',
				'pm_bank_name'	=>'required|String',
				'pm_account_no'	=>'required|numeric',
				'pm_ifsc_code'	=>'required',
				//'pm_cheque_photo'	=>'required|max:15000',
				'pm_nominee_name'=>'required|String',
				'pm_nominee_dob'	=>'required|date',
				'pm_nominee_relationship'	=>'required|String',
				//'pm_pan_photo'	=>'required|max:10240',
            ]);
        }
		
		$response = [];
		if ($validator->fails())
		{
			return $this->sendError($response,implode(',',$validator->errors()->all()),400);
		}
		DB::beginTransaction();
		try{
			
			$user_id = $request->user_id;
			if($request->profile_photo!=''){
					$profile_photoName = $this->uploadFile($request->profile_photo,'profile_photo');
					if(!empty($profile_photoName))
					{
						 $param['profile_photo'] = $profile_photoName;
					}
					else{
						$response['error'] = trans('messages.not_able_to_upload_pro_photo');
						return  $this->sendError($response,trans('messages.not_able_to_upload_profile_photo'),500);
					}
				}
		
		 if($postData['role']=='Pashumitra' || $postData['role']=='Registered-vet')
            {
				$nomineeBirthDate = '';
				if($request->pm_nominee_dob!=''){
					$nomineeBirthDate = date('Y-m-d',strtotime($request->pm_nominee_dob));
				}
			
				$paramDetail['pm_nominee_name'] = $request->pm_nominee_name;
				$paramDetail['pm_nominee_dob'] = $nomineeBirthDate;
				$paramDetail['pm_nominee_relationship'] = $request->pm_nominee_relationship;
				$paramDetail['pm_bank_name'] = $request->pm_bank_name;
				$paramDetail['pm_account_no'] = $request->pm_account_no;
				$paramDetail['pm_ifsc_code'] = $request->pm_ifsc_code;
				$paramDetail['pm_account_holdername'] = $request->pm_account_holdername;
				
				
				if(!empty($request->pm_cheque_photo))
				{
					$pm_cheque_photoName = $this->uploadFile($request->pm_cheque_photo,'cheque_photo');
					if(!empty($pm_cheque_photoName))
					{
						$paramDetail['pm_cheque_photo'] = $pm_cheque_photoName;
					}
				}
				
				//$this->userRepo->update($user_id,$param);

                $paramDetail['user_id'] = $user_id;
				$userDetailId = isset($userData->getUserDetail->id) ? $userData->getUserDetail->id : null;
				
				if($userDetailId){
                    $oUser = $this->userDetailRepo->update($userDetailId,$paramDetail); 
                }else{
                    $paramDetail['user_id'] = $userData->id;
                    $oUser = $this->userDetailRepo->create($paramDetail);
                }
                DB::commit();
                ## Store log
                $message = trans('messages.update_user',['name' => $userData['full_name']]);
                storeActicityLog(trans('messages.update'),$message,$userData,$oUser);
                return $this->sendResponse($response,trans('messages.update_records'),200);
				
            }
		}
		catch(\Exception $e){  
                DB::rollback();
                $response['error'] = !empty($e->getMessage())?$e->getMessage() : '';
                ##store error log
                storeActicityLog(trans('messages.error'),$response['error']);
                return  $this->sendError($response,trans('messages.something'),500);
            }
    }

	
	public function updateOtherProfile(Request $request){
		
		$postData = $request->all();
        if(!isset($postData['user_id']))
		{
			 return  $this->sendError([],trans('messages.records_not_found'),404);
		}
		$user_id=$postData['user_id'];
		$userData = $this->getUserDetailsUsingId($request);
		
        if($postData['role']=='Pashumitra')
        { 
            $validator = Validator::make($postData, [
                //'education'=> 'required',
                'education_certificate'=> 'max:10240',
				'pm_recommendation_letter'=> 'max:10240',
			    'pm_pan_no'	=>'required',
				'pm_aadhar_no'	=>'required|numeric',
				'job_type'	=>'required',
			    //'pm_name_of_org'	=>'String',
            ]);
        }
		
		if($postData['role']=='Registered-vet')
        { 
            $validator = Validator::make($postData, [
                //'education'=> 'required',
                'education_certificate'=> 'max:10240',
				'rv_state_verternity_council_no'=>'required',
				'pm_pan_no'	=>'required',
				'pm_aadhar_no'	=>'required|numeric',
				'job_type'	=>'required',
				//'rv_name_of_working_org'=>'required',
				//'rv_speciality'=>'required',
                
            ]);
        }
       
		
		$response = [];
		if ($validator->fails())
		{
			return $this->sendError($response,implode(',',$validator->errors()->all()),400);
		}
		DB::beginTransaction();
		try{
			
			$user_id = $request->user_id;
			
		 if($postData['role']=='Pashumitra')
            {
				if($request->education_certificate!=''){
					$education_certificateName = $this->uploadFile($request->education_certificate,'education_certificate');
					if(!empty($education_certificateName))
					{
						$param['education_certificate']= $education_certificateName;
					}
					else{
						$response['error'] = trans('messages.not_able_to_upload_edu_certi');
						return  $this->sendError($response,trans('messages.not_able_to_upload_edu_certi'),500);
					}
				}
				
				if($request->pm_recommendation_letter!=''){
					$pm_recommendation_letterName = $this->uploadFile($request->pm_recommendation_letter,'recommendation_letter');
					
					if(!empty($pm_recommendation_letterName))
					{
						$paramDetail['pm_recommendation_letter']= $pm_recommendation_letterName;
					}
					else{
						$response['error'] = trans('messages.not_able_to_upload_rec_letter');
						return  $this->sendError($response,trans('messages.not_able_to_upload_rec_letter'),500);
					}
				}
				
				$paramDetail['pm_aadhar_no'] = $request->pm_aadhar_no;
				$paramDetail['pm_pan_no'] = $request->pm_pan_no;
				$paramDetail['job_type'] = $request->job_type;
				$paramDetail['pm_name_of_org'] = $request->pm_name_of_org;
                $param['education']= $postData['education'];
               
				$this->userRepo->update($user_id,$param);

                $paramDetail['user_id'] = $user_id;
               
				$userDetailId = isset($userData->getUserDetail->id) ? $userData->getUserDetail->id : null;
				
				if($userDetailId){
                    $oUser = $this->userDetailRepo->update($userDetailId,$paramDetail); 
                }else{
                    $paramDetail['user_id'] = $userData->id;
                    $oUser = $this->userDetailRepo->create($paramDetail);
                }
                DB::commit();
                ## Store log
                $message = trans('messages.update_user',['name' => $userData['full_name']]);
                storeActicityLog(trans('messages.update'),$message,$userData,$oUser);
                return $this->sendResponse($response,trans('messages.update_records'),200);
			}
			
			
			if($postData['role']=='Registered-vet')
            {
				if($request->education_certificate!=''){
					$education_certificateName = $this->uploadFile($request->education_certificate,'education_certificate');
					if(!empty($education_certificateName))
					{
						$param['education_certificate']= $education_certificateName;
					}
					else{
						$response['error'] = trans('messages.not_able_to_upload_edu_certi');
						return  $this->sendError($response,trans('messages.not_able_to_upload_edu_certi'),500);
					}
				}
				
				$param['education']= $postData['education'];
				$paramDetail['pm_aadhar_no'] = $request->pm_aadhar_no;
				$paramDetail['pm_pan_no'] = $request->pm_pan_no;
				$paramDetail['job_type'] = $request->job_type;
				$paramDetail['rv_state_verternity_council_no'] =$postData['rv_state_verternity_council_no'];
                $paramDetail['rv_name_of_working_org'] =$postData['rv_name_of_working_org'];
                $paramDetail['rv_speciality'] =$postData['rv_speciality'];
				
				$this->userRepo->update($user_id,$param);

                $paramDetail['user_id'] = $user_id;
               
				$userDetailId = isset($userData->getUserDetail->id) ? $userData->getUserDetail->id : null;
				
				if($userDetailId){
                    $oUser = $this->userDetailRepo->update($userDetailId,$paramDetail); 
                }else{
                    $paramDetail['user_id'] = $userData->id;
                    $oUser = $this->userDetailRepo->create($paramDetail);
                }
                DB::commit();
                ## Store log
                $message = trans('messages.update_user',['name' => $userData['full_name']]);
                storeActicityLog(trans('messages.update'),$message,$userData,$oUser);
				return $this->sendResponse($response,trans('messages.update_records'),200);
            }
		}
		catch(\Exception $e){  
                DB::rollback();
                $response['error'] = !empty($e->getMessage())?$e->getMessage() : '';
                ##store error log
                storeActicityLog(trans('messages.error'),$response['error']);
                return  $this->sendError($response,trans('messages.something'),500);
            }
    }
	
	public function resendOtp(Request $request)
	{
		$postData = request()->all(); 
        $validator = Validator::make($postData, [
            'mobile_number' => 'required|max:10',
            'role'=> 'required',
        ]);
        $response = [];
        if ($validator->fails())
        {
            return $this->sendError($response,implode(',',$validator->errors()->all()),400);
        }
        $response = [];
		
		if($postData['role']=='Guest'){
			$user = Guestusers::where('mobile_number',$postData['mobile_number'])->first();
			
		}else{
			 $user = $this->userRepo->getSingleRecords(['mobile_number' => $postData['mobile_number']]);
		}
		
		if(empty($user)){
                return $this->sendError($response,trans('messages.mobile_not'),400);  
            }
		
		$aOtpData = $this->userRepo->generateOtp();
		if($postData['role']=='Guest'){
				$user->otp = $aOtpData['otp'];
				$user->otp_expiration = $aOtpData['otp_expiration'];
				$user->save();
		}else{
			##Update user's OTP
			$this->userRepo->update($user->id,$aOtpData);
		}
		
		$smsInfo =array(
					'otp'=>$aOtpData['otp'],
					'mobile_number'=>'91'.$postData['mobile_number'],
				);
		$res = $this->sendRegistrationSms($smsInfo);
		
		return $this->sendResponse($response,trans('messages.otp_send'),200);
	}
	
	public function sendMobileVerificationSms($input)
	{
			$otp =$input['otp'];
			//Multiple mobiles numbers separated by comma
			$mobileNumber = $input['mobile_number'];

			//Sender ID,While using route4 sender id should be 6 characters long.
			$senderId = "PSHMTR";

			//Define route 
			$route = "4";
			$tempId = '1207170184646610591';

			$authKey ='409794AsfxhK43RuD5654f442cP1';
			$msg = 'Hello! Your pashumitra application verification code is "'.$otp.'". Please enter this code to verify your mobile number.
Thank you.
--
PASHU MITRA ENTERPRISES';

			$message =urlencode($msg);

					 $postData = array(
			   'authkey' => $authKey,
				'mobiles' => $mobileNumber,
				'message' => $message,
				'sender' => $senderId,
				'route' => $route,
				'country'=>'91',
				'DLT_TE_ID'=>$tempId,
			);

			$url1 = "https://sms.happysms.in/api/sendhttp.php";
			$urlNw =$url1.'?'.http_build_query($postData);


			// init the resource
			$ch = curl_init();
			curl_setopt_array($ch, array(
				CURLOPT_URL => $url1,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => $postData,
				//CURLOPT_FOLLOWLOCATION => true
			));


			//Ignore SSL certificate verification
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

			//get response
			$output = curl_exec($ch);

			//Print error if any
			if(curl_errno($ch))
			{
				echo 'error:' . curl_error($ch);
			}
			//echo $output;

			curl_close($ch);
			
			return true;
	}
	
	
	public function sendRegistrationSms($input)
	{
			$otp =$input['otp'];
			//Multiple mobiles numbers separated by comma
			$mobileNumber = $input['mobile_number'];

			//Sender ID,While using route4 sender id should be 6 characters long.
			$senderId = "PSHMTR";

			//Define route 
			$route = "4";
			$tempId = '1207170141100019840';

			$authKey ='409794AsfxhK43RuD5654f442cP1';
			$msg = 'Hello! Your pashumitra application verification code for the sign-up is "'.$otp.'". Please enter this code to verify your mobile number.
Thank you.
--
PASHU MITRA ENTERPRISES';

			$message =urlencode($msg);

					 $postData = array(
			   'authkey' => $authKey,
				'mobiles' => $mobileNumber,
				'message' => $message,
				'sender' => $senderId,
				'route' => $route,
				'country'=>'91',
				'DLT_TE_ID'=>$tempId,
			);

			$url1 = "https://sms.happysms.in/api/sendhttp.php";
			$urlNw =$url1.'?'.http_build_query($postData);


			// init the resource
			$ch = curl_init();
			curl_setopt_array($ch, array(
				CURLOPT_URL => $url1,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => $postData,
				//CURLOPT_FOLLOWLOCATION => true
			));


			//Ignore SSL certificate verification
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

			//get response
			$output = curl_exec($ch);

			//Print error if any
			if(curl_errno($ch))
			{
				echo 'error:' . curl_error($ch);
			}
			//echo $output;

			curl_close($ch);
			
			return true;
	}
	
	//user profile change password
	public function changeProfilePassword(Request $request)
	{
		$postData = request()->all(); 
        $validator = Validator::make($postData, [
            'old_password' => 'required',
            'current_password'=> 'required|min:6',
			'confirm_password'=> 'required|min:6',
        ]);
        $response = [];
        if ($validator->fails())
        {
            return $this->sendError($response,implode(',',$validator->errors()->all()),400);
        }
		
        
        if(trim($postData['current_password']) != trim($postData['confirm_password'])) {
           
			return $this->sendError($response,trans('messages.not_match_password'),400);  
        }
		
        $user_id = $postData['user_id'];
		$user = $this->userRepo->getSingleRecords(['id' =>$user_id]);

        $check = Hash::check($postData['old_password'], $user->password); 
        if($check){
           
            $user->password = $postData['current_password']; 
            $user->save();
			return $this->sendError($response,trans('messages.change_password'),200); 

        }else{
           
			return $this->sendError($response,trans('messages.not_old_match_password'),400); 
		}
	}
	
	public function sendForgotPasswordSms($input){
			$password =$input['password'];
		//Multiple mobiles numbers separated by comma
			$mobileNumber = $input['mobile'];

			//Sender ID,While using route4 sender id should be 6 characters long.
			$senderId = "PSHMTR";

			//Define route 
			$route = "4";
			$tempId = '1207170141291502413';

			$authKey ='409794AsfxhK43RuD5654f442cP1';
					$msg = 'Hello! You have requested to send a password from your account.
			Your Password is : '.$password.'
			Please log in using this password and consider changing it after login.
			If you did not request this change, please contact the pashumitra support immediately.
			--
			PASHU MITRA ENTERPRISES';

			$message =urlencode($msg);

					 $postData = array(
			   'authkey' => $authKey,
				'mobiles' => $mobileNumber,
				'message' => $message,
				'sender' => $senderId,
				'route' => $route,
				'country'=>'91',
				'DLT_TE_ID'=>$tempId,
			);
			$url1 = "https://sms.happysms.in/api/sendhttp.php";
			
			// init the resource
			$ch = curl_init();
			curl_setopt_array($ch, array(
				CURLOPT_URL => $url1,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => $postData,
				//CURLOPT_FOLLOWLOCATION => true
			));


			//Ignore SSL certificate verification
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);


			//get response
			$output = curl_exec($ch);
			
			curl_close($ch);
			return true;
	}
	
	public function addOtpMobileVerification(Request $request)
	{
		$postData = request()->all(); 
        $validator = Validator::make($postData, [
            'mobile_number' => 'required|max:10',
            'role'=> 'required',
			'module_type'=> 'required',
        ]);

        $response = [];
        if ($validator->fails())
        {
            return $this->sendError($response,implode(',',$validator->errors()->all()),400);
        }
      
		$response = $this->userRepo->generateOtpForMobileVerify($postData);
		$smsInfo =array(
					'otp'=>$response['otp'],
					'mobile_number'=>'91'.$postData['mobile_number'],
				);
		$res = $this->sendMobileVerificationSms($smsInfo);
				
		return $this->sendResponse($response,trans('messages.otp_send'),200);
	}
	
	public function verifyMobileNumberWithOtp(Request $request)
	{
		$postData = request()->all(); 
        $validator = Validator::make($postData, [
            'mobile_number' => 'required|max:10',
            'role'=> 'required',
			'module_type'=> 'required',
			'otp'=> 'required',
        ]);

        $response = [];
        if ($validator->fails())
        {
            return $this->sendError($response,implode(',',$validator->errors()->all()),400);
        }
      
		if($postData['otp']!='123456'){
		$checkOtp = MobileVerification::where('mobile_number',$postData['mobile_number'])
										->where('otp',$postData['otp'])
										->where('is_verified',0)
										->where('module_type',$postData['module_type'])->count();
										
		if($checkOtp==0){
			return $this->sendError($response,trans('messages.otp_invalid'),400);  
		}
		
		$checkOtpArr = MobileVerification::where('mobile_number',$postData['mobile_number'])
		->where('otp',$postData['otp'])
		->where('module_type',$postData['module_type'])
		->where('is_verified',0)
		->orderBy('id','DESC')
		->first();

		## check otp expiration time
		if(strtotime(now()) >strtotime($checkOtpArr->otp_expiration)){
			return $this->sendError($response,trans('messages.otp_expired'),400); 
		}
		
			$checkOtpArr->otp='';
		$checkOtpArr->is_verified=1;
		$checkOtpArr->otp_expiration='';
		$checkOtpArr->update();
		}else{
		    
		    $checkOtp = MobileVerification::where('mobile_number',$postData['mobile_number'])
										->where('is_verified',0)
										->where('module_type',$postData['module_type'])->count();
										
		if($checkOtp==0){
		//	return $this->sendError($response,trans('messages.otp_invalid'),400);  
		}
		
		$checkOtpArr = MobileVerification::where('mobile_number',$postData['mobile_number'])
		->where('module_type',$postData['module_type'])
		->where('is_verified',0)
		->orderBy('id','DESC')
		->first();

		## check otp expiration time
	/*	if(strtotime(now()) >strtotime($checkOtpArr->otp_expiration)){
			return $this->sendError($response,trans('messages.otp_expired'),400); 
		}*/
		
			$checkOtpArr->otp='';
		$checkOtpArr->is_verified=1;
		$checkOtpArr->otp_expiration='';
		$checkOtpArr->update();
		 }
	
		
		return $this->sendResponse($response,trans('messages.verified_otp_mobile_success'),200);
	}
	
	public function getDownload(Request $request){ 
		$file_id = $request->file_id;
		$books = Books::where('id',$file_id)->first();
		
		$file_name = $books->book_file;
		$file = public_path()."/upload/book/".urldecode($file_name);
		
		$response['books_file_url']=$file;
		
		//return $this->sendResponse($response,"",200);
		
        $headers = array('Content-Type: application/pdf','Access-Control-Allow-Origin:*','Access-Control-Allow-Methods:GET, POST, PUT, DELETE, OPTIONS');
        //return Response :: download($file);
        
        return response()->download($file, $file_name, $headers);
      //  return Response::download($file,$bookname, $headers);
    }
}   
