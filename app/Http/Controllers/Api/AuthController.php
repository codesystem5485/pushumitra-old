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
use App\Models\Guestusers;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
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
        Role $role
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
                'first_name' => 'required|string|max:255',
                'middle_name' => 'string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required',
                'confirm_password' => 'required',
                'mobile_number' => 'required|numeric|unique:users',
                'address_line_1' => 'required|string',
                'address_line_2' => 'required|string',
               // 'village' => 'required|string',
                'city_town' => 'required|string',
                'state' => 'required|string',
                'pincode' => 'required|numeric',
                'education'=> 'required',
                'education_certificate'=> 'required|max:10240',
                'pm_collage_name' => 'required|string',
                'pm_collage_address' => 'required|string',
                'date_of_birth' => 'date',
                'age' => 'required',                
                'nationality' => 'required',
                'sex' => 'required',
                'marital_status' => 'required',
            ]);
        }
        if($postData['role']=='Animal-owner')
        {
            $validator = Validator::make($postData, [
                //'profile_photo'=>'required|max:10240',
                'first_name' => 'required|string|max:255',
                'middle_name' => 'string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'mobile_number' => 'required|numeric|unique:users',
                'password' => 'required',
                'confirm_password' => 'required',
                'address_line_1' => 'required|string',
                'address_line_2' => 'required|string',
                'city_town' => 'required|string',
                'city_id' => 'required',
                'state_id' => 'required',
                'state' => 'required|string',
                'pincode' => 'required|numeric',
                 
            ]);
        }
        if($postData['role']=='Registered-vet')
        {
            $validator = Validator::make($postData, [
               // 'profile_photo'=>'required|max:10240',
                'first_name' => 'required|string|max:255',
                'middle_name' => 'string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'mobile_number' => 'required|numeric|unique:users',
                'password' => 'required',
                'confirm_password' => 'required',
                'address_line_1' => 'required|string',
                'address_line_2' => 'required|string',
                'city_town' => 'required|string',
                'state' => 'required|string',
                'pincode' => 'required|numeric',
                'education_certificate'=> 'required|max:10240',
                'education'=> 'required|string',
				'rv_current_working_address'=> 'required|string',
                'rv_state_verternity_council'=>'required',
                'rv_state_verternity_council_no'=>'required|numeric',
                'rv_working_place' => 'required|string',
                'rv_working_city_town' => 'required|string',
                'rv_working_state' => 'required|string',
                'rv_working_pincode' => 'required|numeric',
                'date_of_birth' => 'date',
                'age' => 'required|numeric',
                'sex' => 'required|string',
                'job_type' => 'required|string',
                'rv_name_of_working_org'=>'required|string',
                'nationality' => 'required|string',
                'alternate_mobile_number' => 'required|numeric',                
                'rv_speciality'=>'required|string',
            ]);
        }
        if($postData['role']=='Superadmin' || $postData['role']=='Administrator' || $postData['role']=='Accountant' || $postData['role']=='Support-team' || $postData['role']=='Customer-care' )
        {
            $validator = Validator::make($postData, [
                'first_name' => 'required|string|max:255',
                'middle_name' => 'string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'mobile_number' => 'required|numeric|unique:users',
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
			   /* $profile_photoName = $this->uploadFile($request->profile_photo,'profile_photo');
                if(!empty($profile_photoName))
                {
                    $param['profile_photo'] = $profile_photoName;
                }
                else{

                    $response['error'] = trans('messages.not_able_to_upload_pro_photo');
                    return  $this->sendError($response,trans('messages.not_able_to_upload_edu_certi'),500);
                }*/
                
                $param['first_name'] = $postData['first_name'];
                $param['middle_name'] = $postData['middle_name'];
                $param['last_name'] = $postData['last_name'];
                $param['email'] = $postData['email'];
                $param['password'] = $postData['password'];
                $param['mobile_number'] = $postData['mobile_number'];
                $param['address_line_1'] = $postData['address_line_1'];
                $param['address_line_2'] = $postData['address_line_2'];
               // $param['village'] = $postData['village'];
                $param['city_town'] = $postData['city_town'];
                $param['state'] = $postData['state'];
                $param['pincode'] = $postData['pincode'];
				$param['state_id'] = $postData['state_id'];
                $param['city_id'] = $postData['city_id'];
                
                $user = $this->userRepo->create($param);

                $paramDetail['user_id'] = $user->id;
                $userDetail = $this->userDetailRepo->create($paramDetail);
            }
            if($postData['role']=='Pashumitra')
            {
				$param['pm_code'] = $this->userRepo->generatePashumitraCode();
				$param['profile_photo'] =null;
				/*$profile_photoName = $this->uploadFile($request->profile_photo,'profile_photo');
                if(!empty($profile_photoName))
                {
                    $param['profile_photo'] = $profile_photoName;
                }
                else{
                    $response['error'] = trans('messages.not_able_to_upload_pro_photo');
                    return  $this->sendError($response,trans('messages.not_able_to_upload_edu_certi'),500);
                }*/

                $education_certificateName = $this->uploadFile($request->education_certificate,'education_certificate');
                if(!empty($education_certificateName))
                {
                    $param['education_certificate']= $education_certificateName;
                }
                else{
                    $response['error'] = trans('messages.not_able_to_upload_edu_certi');
                    return  $this->sendError($response,trans('messages.not_able_to_upload_edu_certi'),500);
                }
                
                $param['first_name'] = $postData['first_name'];
                $param['middle_name'] = $postData['middle_name'];
                $param['last_name'] = $postData['last_name'];
                $param['email'] = 	$postData['email'];
                $param['password'] = $postData['password'];
                $param['mobile_number'] = $postData['mobile_number'];
                $param['address_line_1'] = $postData['address_line_1'];
                $param['address_line_2'] = $postData['address_line_2'];
              //  $param['village'] = $postData['village'];
                $param['city_town'] = $postData['city_town'];
                $param['state'] = $postData['state'];
                $param['state_id'] = $postData['state_id'];
                $param['city_id'] = $postData['city_id'];
                $param['pincode'] = $postData['pincode']; 
                $param['education']= $postData['education'];
                $param['pm_collage_name'] = $postData['pm_collage_name'];
                $param['pm_collage_address'] = $postData['pm_collage_address'];
                $param['nationality'] = $postData['nationality']; 
                $param['sex'] = $postData['sex']; 
                $param['marital_status'] = $postData['marital_status']; 
                $param['age'] = $postData['age'];
                $param['date_of_birth'] = $postData['date_of_birth'];

                $user = $this->userRepo->create($param);

                $paramDetail['user_id'] = $user->id;
                $paramDetail['pm_collage_name'] = $postData['pm_collage_name'];
                $paramDetail['pm_collage_address'] = $postData['pm_collage_address'];

                $userDetail = $this->userDetailRepo->create($paramDetail);
            }
            if($postData['role']=='Registered-vet')
            {
                $param['profile_photo']=null;
				/*$profile_photoName = $this->uploadFile($request->profile_photo,'profile_photo');
                if(!empty($profile_photoName))
                {
                    $param['profile_photo'] = $profile_photoName;
                }
                else{
                    $response['error'] = trans('messages.not_able_to_upload_pro_photo');
                    return  $this->sendError($response,trans('messages.not_able_to_upload_edu_certi'),500);
                }*/

                $education_certificateName = $this->uploadFile($request->education_certificate,'education_certificate');
                if(!empty($education_certificateName))
                {
                    $param['education_certificate']= $education_certificateName;
                }
                else{
                    $response['error'] = trans('messages.not_able_to_upload_edu_certi');
                    return  $this->sendError($response,trans('messages.not_able_to_upload_edu_certi'),500);
                }
                
                $param['first_name'] = $postData['first_name'];
                $param['middle_name'] = $postData['middle_name'];
                $param['last_name'] = $postData['last_name'];
                $param['email'] = $postData['email'];
                $param['password'] = $postData['password'];
                $param['mobile_number'] = $postData['mobile_number'];
                $param['address_line_1'] = $postData['address_line_1'];
                $param['address_line_2'] = $postData['address_line_2'];
               // $param['village'] = $postData['village'];
                $param['city_town'] = $postData['city_town'];
                $param['state'] = $postData['state'];
                $param['state_id'] = $postData['state_id'];
                $param['city_id'] = $postData['city_id'];
                $param['pincode'] = $postData['pincode']; 
                $param['education']= $postData['education'];
                $param['date_of_birth'] =$postData['date_of_birth'];
                $param['age'] =$postData['age'];
                $param['sex'] =$postData['sex'];
                $param['nationality'] =$postData['nationality'];
                $param['alternate_mobile_number'] =$postData['alternate_mobile_number'];
                $user = $this->userRepo->create($param);

                $paramDetail['rv_state_verternity_council'] =$postData['rv_state_verternity_council'];
                $paramDetail['rv_state_verternity_council_no'] =$postData['rv_state_verternity_council_no'];
                $paramDetail['rv_working_village'] =$postData['rv_working_place'];
                $paramDetail['rv_working_city_town'] =$postData['rv_working_city_town'];
                $paramDetail['rv_working_city_id'] =$postData['rv_working_city_id'];
                $paramDetail['rv_working_state'] =$postData['rv_working_state'];
                $paramDetail['rv_working_state_id'] =$postData['rv_working_state_id'];
                $paramDetail['rv_working_pincode'] =$postData['rv_working_pincode'];
				$paramDetail['rv_current_working_address'] =$postData['rv_current_working_address'];
                $paramDetail['job_type'] =$postData['job_type'];
                $paramDetail['user_id'] = $user->id;
                $paramDetail['rv_name_of_working_org'] =$postData['rv_name_of_working_org'];
                $paramDetail['rv_speciality'] =$postData['rv_speciality'];
                $userDetail = $this->userDetailRepo->create($paramDetail);
            }
            if($user){
                //asign role
                $roleData = $this->roleRepo->where('name',$request->role)->first();
                if($roleData){
                    $user->assignRole($roleData->name);  
                }
                $aOtpData = $this->userRepo->generateOtp();
                ##Update user's OTP
                $this->userRepo->update($user->id,$aOtpData);  
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
			//'role'=>'required'
        ]);
        $response = [];
        if ($validator->fails())
        {
            return $this->sendError($response,implode(',',$validator->errors()->all()),400);
        }
        $user = $this->userRepo->getSingleRecords(['mobile_number' => $postData['email_id_or_mobile_number']]);
		
		
        
        if (!$user) {

            $user = $this->userRepo->getSingleRecords(['email' => $postData['email_id_or_mobile_number']]);
        }

        if($user)
        {
            ## check phone is verify
            $aUserVerify = $this->userRepo->getSingleRecords(['mobile_number' => $user->mobile_number,'is_phone_verify' => 1]);
            if(empty($aUserVerify)){
                return $this->sendError($response,trans('messages.verify_phone'),401);
            }

            ##check correct password
            $check = Hash::check($postData['password'], $user->password); 
            if(!$check)
            {
                return $this->sendError($response,trans('messages.invalid_password'),401);
            }
            
            DB::beginTransaction();
            try{
              //  $token = $user->createToken($user->email)->accessToken;
			  
				$token = $this->createApiToken();
				$param = ['api_token' => $token];
				$this->userRepo->update($user->id,$param);
                $response = ['id'=>$user->id,'first_name' => $user->first_name,'email' => $user->email,'token' => $token,
				'is_verified' =>$user->is_verified ];
				if($user->is_verified==0)
				{
					return $this->sendResponse($response,trans('messages.login_success_not_verified'),200); 
				}else{
					return $this->sendResponse($response,trans('messages.login_success'),200); 
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
			//'role'=> 'required',
        ]);
        $response = [];
        if ($validator->fails())
        {
            return $this->sendError($response,implode(',',$validator->errors()->all()),400);
        }
        $response = [];
        $user = $this->userRepo->getSingleRecords(['mobile_number' => $postData['mobile_number']]);
        if ($user) {
            ## check otp is valid or not 
            $checkOtp = $this->userRepo->getSingleRecords(['mobile_number' => $postData['mobile_number'],'otp' => $postData['otp']]);
            if(empty($checkOtp)){
                return $this->sendError($response,trans('messages.otp_invalid'),400);  
            }

            ## check otp expiration time
            if(strtotime(now()) >strtotime($user->otp_expiration)){
                return $this->sendError($response,trans('messages.otp_expired'),400); 
            }
            $param = ['otp' => null,'otp_expiration' =>  null,
                    'is_phone_verify' => 1,'is_active' => 1, 'is_verified' => 1];
					
		/*	if($postData['role'] == 'Pashumitra')
			{
				$param = ['otp' => null,'otp_expiration' =>  null,
                    'is_phone_verify' => 1,'is_active' => 1,'is_verified' => 0];
			}*/
					
            $this->userRepo->update($user->id,$param);  

            ## display  login type wise data
            if($postData['login_type'] == 'signin'){
                ## if verified otp then create token
                //$token = $user->createToken($user->email)->accessToken;
				$token = $this->createApiToken();
				$param = ['api_token' => $token];
				$this->userRepo->update($user->id,$param);
                $response = ['first_name' => $user->first_name,'email' => $user->email,'role' => 
                isset($user->roles[0]->name) ? $user->roles[0]->name : '','token' => $token];
            }
            
            return $this->sendResponse($response,trans('messages.verify_success'),200);  
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
			
			$userDetail['adharcard_front_url']=url("/upload/aadhar_photo_front/");
			$userDetail['adharcard_back_url']=url("/upload/aadhar_photo_back/");
			$userDetail['profile_photo_url'] = url("/upload/profile_photo/");
			$userDetail['educationcertificate_url']=url("/upload/education_certificate/");
			$userDetail['chequephoto_url']=url("/upload/cheque_photo/");
			$userDetail['pancard_url']=url("/upload/pan_photo/");
			
            return $this->sendResponse($userDetail,trans('messages.records_found'));
        }else{
            return  $this->sendError([],trans('messages.records_not_found'),404); 
        }
    }

    public function updateProfile(Request $request){
		
		$postData = $request->all();
        $user_id=$postData['user_id'];
		$userData = $this->getUserDetailsUsingId($request);
		
		if($postData['role']=='Animal-owner')
        {
            $validator = Validator::make($postData, [
                'profile_photo'=>'max:10240',
                'first_name' => 'required|string|max:255',
                'middle_name' => 'string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,'.$user_id,
                'mobile_number' => 'required|numeric|unique:users,mobile_number,'.$user_id,
                'password' => 'required',
                'confirm_password' => 'required',
                'address_line_1' => 'required|string',
                'address_line_2' => 'required|string',
                'city_town' => 'required|string',
                'city_id' => 'required',
                'state_id' => 'required',
                'state' => 'required|string',
                'pincode' => 'required|numeric',
                 
            ]);
        }
		
        if($postData['role']=='Pashumitra')
        { 
            $validator = Validator::make($postData, [
                'profile_photo'=>'max:10240',
                'first_name' => 'required|string|max:255',
                'middle_name' => 'string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,'.$user_id,
                'password' => 'required',
                'confirm_password' => 'required',
                'mobile_number' => 'required|numeric|unique:users,mobile_number,'.$user_id,
                'address_line_1' => 'required|string',
                'address_line_2' => 'required|string',
                //'village' => 'required|string',
                'city_town' => 'required|string',
                'state' => 'required|string',
                'pincode' => 'required|numeric',
                'education'=> 'required',
               // 'education_certificate'=> 'max:10240',
                'pm_collage_name' => 'required|string',
                'pm_collage_address' => 'required|string',
                'date_of_birth' => 'date',
                'age' => 'required',                
                'nationality' => 'required',
                'sex' => 'required',
                'marital_status' => 'required',
				'pm_collage_name' => 'required|string',
				'pm_collage_address' => 'required|string',            
				'pm_nominee_name'=>'required|String',
				'pm_nominee_dob'	=>'required|date',
				'pm_collage_address'	=>'required|String',
				'pm_nominee_relationship'	=>'required|String',
				'pm_aadhar_no'	=>'required|numeric',
				'pm_aadhar_photo_front'	=>'required|max:10240',
				'pm_aadhar_photo_back'	=>'required|max:10240',
				'job_type'	=>'required',
				'pm_pan_no'	=>'required|',
				'pm_pan_photo'	=>'required|max:10240',
				'pm_bank_name'	=>'required|String',
				'pm_account_no'	=>'required|numeric',
				'pm_ifsc_code'	=>'required',
				'pm_cheque_photo'	=>'required|max:10240',
				'pm_name_of_org'	=>'required|String',
            ]);
        }
		
		if($postData['role']=='Registered-vet')
        { 
            $validator = Validator::make($postData, [
                'profile_photo'=>'max:10240',
                'first_name' => 'required|string|max:255',
                'middle_name' => 'string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,'.$user_id,
                'mobile_number' => 'required|numeric|unique:users,mobile_number,'.$user_id,
                'password' => 'required',
                'confirm_password' => 'required',
                'address_line_1' => 'required|string',
                'address_line_2' => 'required|string',
                'city_town' => 'required|string',
                'state' => 'required|string',
                'pincode' => 'required|numeric',
                'education_certificate'=> 'max:10240',
                'education'=> 'required|string',
				'rv_state_verternity_council'=>'required',
                'rv_state_verternity_council_no'=>'required|numeric',
				'rv_current_working_address' => 'required|string',
                'rv_working_place' => 'required|string',
                'rv_working_city_town' => 'required|string',
                'rv_working_state' => 'required|string',
                'rv_working_pincode' => 'required|numeric',
                'date_of_birth' => 'date',
                'age' => 'required|numeric',
                'sex' => 'required|string',
                'job_type' => 'required|string',
                'rv_name_of_working_org'=>'required|string',
                'nationality' => 'required|string',
                'alternate_mobile_number' => 'required|numeric',                
                'rv_speciality'=>'required|string',
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
		
		 if($postData['role']=='Pashumitra')
            {
				if($request->education_certificate!=''){
					/*$education_certificateName = $this->uploadFile($request->education_certificate,'education_certificate');
					if(!empty($education_certificateName))
					{
						$param['education_certificate']= $education_certificateName;
					}
					else{
						$response['error'] = trans('messages.not_able_to_upload_edu_certi');
						return  $this->sendError($response,trans('messages.not_able_to_upload_edu_certi'),500);
					}*/
				}
				
				$paramDetail['pm_nominee_name'] = $request->pm_nominee_name;
				$paramDetail['pm_nominee_dob'] = date('Y-m-d',strtotime($request->pm_nominee_dob));
				$paramDetail['pm_nominee_relationship'] = $request->pm_nominee_relationship;
				$paramDetail['pm_aadhar_no'] = $request->pm_aadhar_no;
				$paramDetail['pm_pan_no'] = $request->pm_pan_no;
				$paramDetail['pm_bank_name'] = $request->pm_bank_name;
				$paramDetail['pm_account_no'] = $request->pm_account_no;
				$paramDetail['pm_ifsc_code'] = $request->pm_ifsc_code;
				$paramDetail['job_type'] = $request->job_type;
				$paramDetail['pm_name_of_org'] = $request->pm_name_of_org;
				
				if(!empty($request->pm_aadhar_photo_front))
				{
					$pm_aadhar_photo_frontName = $this->uploadFile($request->pm_aadhar_photo_front,'aadhar_photo_front');
					if(!empty($pm_aadhar_photo_frontName))
					{
						$paramDetail['pm_aadhar_photo_front'] = $pm_aadhar_photo_frontName;
					}
				}
				if(!empty($request->pm_aadhar_photo_back))
				{
					$pm_aadhar_photo_backName = $this->uploadFile($request->pm_aadhar_photo_back,'aadhar_photo_back');
					if(!empty($pm_aadhar_photo_backName))
					{
						$paramDetail['pm_aadhar_photo_back'] = $pm_aadhar_photo_backName;
					}
				}
			
            if(!empty($request->pm_pan_photo))
            {
                $pm_pan_photoName = $this->uploadFile($request->pm_pan_photo,'pan_photo');
                if(!empty($pm_pan_photoName))
                {
                   $paramDetail['pm_pan_photo'] = $pm_pan_photoName;
                }
            }
			
            if(!empty($request->pm_cheque_photo))
            {
                $pm_cheque_photoName = $this->uploadFile($request->pm_cheque_photo,'cheque_photo');
                if(!empty($pm_cheque_photoName))
                {
                    $paramDetail['pm_cheque_photo'] = $pm_cheque_photoName;
                }
            }  
                $param['first_name'] = $postData['first_name'];
                $param['middle_name'] = $postData['middle_name'];
                $param['last_name'] = $postData['last_name'];
                $param['email'] = $postData['email'];
                $param['password'] = $postData['password'];
                $param['mobile_number'] = $postData['mobile_number'];
                $param['address_line_1'] = $postData['address_line_1'];
                $param['address_line_2'] = $postData['address_line_2'];
                //$param['village'] = $postData['village'];
                $param['city_town'] = $postData['city_town'];
                $param['state'] = $postData['state'];
                $param['state_id'] = $postData['state_id'];
                $param['city_id'] = $postData['city_id'];
                $param['pincode'] = $postData['pincode']; 
                $param['education']= $postData['education'];
                $param['pm_collage_name'] = $postData['pm_collage_name'];
                $param['pm_collage_address'] = $postData['pm_collage_address'];
                $param['nationality'] = $postData['nationality']; 
                $param['sex'] = $postData['sex']; 
                $param['marital_status'] = $postData['marital_status']; 
                $param['age'] = $postData['age'];
                $param['date_of_birth'] = $postData['date_of_birth'];
				
				$this->userRepo->update($user_id,$param);

                $paramDetail['user_id'] = $user_id;
                $paramDetail['pm_collage_name'] = $postData['pm_collage_name'];
                $paramDetail['pm_collage_address'] = $postData['pm_collage_address'];

                
				$userDetailId = isset($userData->getUserDetail->id) ? $userData->getUserDetail->id : null;
				
				if($userDetailId){
                    $oUser = $this->userDetailRepo->update($userDetailId,$paramDetail); 
                }else{
                    $paramDetail['user_id'] = $userData->id;
                    $oUser = $this->userDetailRepo->create($paramDetail);
                }
                DB::commit();
                ## Store log
                $message = trans('messages.update_user',['name' => $postData['first_name'].' '.$postData['last_name']]);
                storeActicityLog(trans('messages.update'),$message,$userData,$oUser);
                return $this->sendResponse($response,trans('messages.update_records'),200);
				
            }
			
			if($postData['role']=='Animal-owner')
            {
				$param['first_name'] = $postData['first_name'];
                $param['middle_name'] = $postData['middle_name'];
                $param['last_name'] = $postData['last_name'];
                $param['email'] = $postData['email'];
                $param['password'] = $postData['password'];
                $param['mobile_number'] = $postData['mobile_number'];
                $param['address_line_1'] = $postData['address_line_1'];
                $param['address_line_2'] = $postData['address_line_2'];
              //  $param['village'] = $postData['village'];
                $param['city_town'] = $postData['city_town'];
                $param['state'] = $postData['state'];
                $param['pincode'] = $postData['pincode'];
				$param['state_id'] = $postData['state_id'];
                $param['city_id'] = $postData['city_id'];
                
				$this->userRepo->update($user_id,$param);
				$paramDetail['user_id'] = $userData->id;
				$userDetailId = isset($userData->getUserDetail->id) ? $userData->getUserDetail->id : null;
				
				if($userDetailId){
                    $oUser = $this->userDetailRepo->update($userDetailId,$paramDetail); 
                }else{
                    $paramDetail['user_id'] = $userData->id;
                    $oUser = $this->userDetailRepo->create($paramDetail);
                }
                DB::commit();
                ## Store log
                $message = trans('messages.update_user',['name' => $postData['first_name'].' '.$postData['last_name']]);
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
                
                $param['first_name'] = $postData['first_name'];
                $param['middle_name'] = $postData['middle_name'];
                $param['last_name'] = $postData['last_name'];
                $param['email'] = $postData['email'];
                $param['password'] = $postData['password'];
                $param['mobile_number'] = $postData['mobile_number'];
                $param['address_line_1'] = $postData['address_line_1'];
                $param['address_line_2'] = $postData['address_line_2'];
               // $param['village'] = $postData['village'];
                $param['city_town'] = $postData['city_town'];
                $param['state'] = $postData['state'];
                $param['state_id'] = $postData['state_id'];
                $param['city_id'] = $postData['city_id'];
                $param['pincode'] = $postData['pincode']; 
                $param['education']= $postData['education'];
                $param['date_of_birth'] =$postData['date_of_birth'];
                $param['age'] =$postData['age'];
                $param['sex'] =$postData['sex'];
                $param['nationality'] =$postData['nationality'];
                $param['alternate_mobile_number'] =$postData['alternate_mobile_number'];
                
				$this->userRepo->update($user_id,$param);

                $paramDetail['rv_state_verternity_council'] =$postData['rv_state_verternity_council'];
                $paramDetail['rv_state_verternity_council_no'] =$postData['rv_state_verternity_council_no'];
                $paramDetail['rv_working_village'] =$postData['rv_working_place'];
				$paramDetail['rv_current_working_address'] =$postData['rv_current_working_address'];
                $paramDetail['rv_working_city_town'] =$postData['rv_working_city_town'];
                $paramDetail['rv_working_city_id'] =$postData['rv_working_city_id'];
                $paramDetail['rv_working_state'] =$postData['rv_working_state'];
                $paramDetail['rv_working_state_id'] =$postData['rv_working_state_id'];
                $paramDetail['rv_working_pincode'] =$postData['rv_working_pincode'];
                $paramDetail['job_type'] =$postData['job_type'];
                
                $paramDetail['rv_name_of_working_org'] =$postData['rv_name_of_working_org'];
                $paramDetail['rv_speciality'] =$postData['rv_speciality'];
                
				$userDetailId = isset($userData->getUserDetail->id) ? $userData->getUserDetail->id : null;
				
				if($userDetailId){
                    $oUser = $this->userDetailRepo->update($userDetailId,$paramDetail); 
                }else{
                    $paramDetail['user_id'] = $userData->id;
                    $oUser = $this->userDetailRepo->create($paramDetail);
                }
                DB::commit();
                ## Store log
                $message = trans('messages.update_user',['name' => $postData['first_name'].' '.$postData['last_name']]);
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
           
		/*
		
        $userData = $this->getUserDataUsingToken($request);
        if($userData){
            ## Get user detail id
            $userDetailId = isset($userData->getUserDetail->id) ? $userData->getUserDetail->id : null;
            
            $postData = request()->json()->all();
            $validator = Validator::make($postData, [
                'name' => 'required|max:10',
                'email' => 'required|email|unique:users,email,'.$userData->id,
            ]); 
            $response = [];
            if ($validator->fails())
            {
                return $this->sendError($response,implode(',',$validator->errors()->all()),400);
            }
            DB::beginTransaction();
            try{
                ## Update user data
                $inputData = ['name' => $postData['name'],'email' => $postData['email']];
                $this->userRepo->update($userData->id,$inputData);
                
                ## Update user detail data
                $inputDetail = skip_empty_field($request->except(['email','name']));
                if($userDetailId){
                    $oUser = $this->userDetailRepo->update($userDetailId,$inputDetail); 
                }else{
                    $inputDetail['user_id'] = $userData->id;
                    $oUser = $this->userDetailRepo->create($inputDetail);
                }
                DB::commit();
                ## Store log
                $message = trans('messages.update_user',['name' => $postData['name']]);
                storeActicityLog(trans('messages.update'),$message,$userData,$oUser);
                return $this->sendResponse($response,trans('messages.update_records'),200); 
            }
            catch(\Exception $e){  
                DB::rollback();
                $response['error'] = !empty($e->getMessage())?$e->getMessage() : '';
                ##store error log
                storeActicityLog(trans('messages.error'),$response['error']);
                return  $this->sendError($response,trans('messages.something'),500);
            }
        }
        else{
            return  $this->sendError([],trans('messages.records_not_found'),404);  
        }  
*/		
          
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
            'email_id' => 'required',
        ]);
        $response = [];
        if ($validator->fails())
        {
            return $this->sendError($response,implode(',',$validator->errors()->all()),400);
        }
       
		$user = $this->userRepo->getSingleRecords(['email' => $postData['email_id']]);
        if($user)
        {
 
            try{
				$to_name = 'minakshichavan@codesystem.co.in';
				$to_email = 'minakshi31.chavan@gmail.com';
				$data = array('name'=>'Cloudways', 'body' => 'A test mail');
				  
				/*Mail::send(['text'=>'mail'], $data, function($message) use ($to_name, $to_email) {
				$message->to($to_email, $to_name)
				->subject('Laravel Test Mai');
				$message->from($to_name,'Test Mail');
				});*/
				
				
             return $this->sendResponse($response,trans('messages.email_password'),200);

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
	
}   
