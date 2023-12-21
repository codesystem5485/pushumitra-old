<?php

namespace App\Repositories\Implementation\User;

use App\Base\BaseRepository;
use App\Models\User;
use App\Models\Payments;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use DB;
use DataTables;
use Spatie\Activitylog\Models\Activity;
use App\Models\MobileVerification;

class UserRepository  extends BaseRepository implements UserRepositoryInterface
{
    /**
     * @var User
     */
    protected $userModel; 

    /**
     * UserRepository constructor.
     *
     * @param User $userModel
     */
    public function __construct(User $userModel)
    {
        parent::__construct($userModel);
        $this->userModelRepo = $userModel;
    }

    public function getSiteUsers()
    {     
        return  $this->userModelRepo->with(['roles','getCreatedBy:id,first_name,middle_name,last_name,mobile_number'])
        ->whereHas('roles', function($q) {
            // if(!empty($input['sRoleName'])){
                $q->where('name','=','Pashumitra')
                ->orWhere('name','=','Registered-vet')
                ->orWhere('name','=','Animal-owner');
            // }
        })
        //->where('id','!=',1)->where('is_phone_verify',1)
            ->orderBy('id', 'DESC')
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getUsers(array $input = [])
    {     
        return  $this->userModelRepo->with(['roles','getCreatedBy:id,full_name,mobile_number'])
        ->whereHas('roles', function($q) use($input) {
            if(!empty($input['sRoleName'])){
                $q->where('name', $input['sRoleName']);
            }
        })
        //->where('id','!=',1)->where('is_phone_verify',1)
            ->orderBy('id', 'DESC')
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getUser(int $userId)
    {
        return  $this->userModelRepo->findOrFail($userId);
    }

    /**
     * {@inheritDoc}
     */
    public function updateUser($userId, $request = []) 
    {
        DB::beginTransaction();
        try {
            $user =  $this->userModelRepo->find($userId);
            $user->update($request);
            DB::commit();
            return true;
        } catch (\Exception $e) {  
            DB::rollback();
            return false;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function deleteUser(int $userId)
    { 
        try{
            $category =  $this->userModelRepo->findOrFail($catId);
            return $category->delete();
        } catch (\Exception $e) {  
            DB::rollback();
            return false;
        }
    }

    public function with(array $input)
    {
        return  $this->userModelRepo->with($input)->orderBy('id', 'ASC')
        ->get();
    }
    
    public function logout($id = ''){
        DB::table('oauth_access_tokens')
        ->where('id', $id)
        ->delete();
    }
    
    public function checkUniqueMobileNumber($userId,$mobile){
       return $this->userModelRepo->where('id','!=',$userId)->where('mobile_number' ,$mobile)->first();
    }

    public function getUsersData($sRoleName = ''){
        
        $users = $this->getUsers(['sRoleName' => $sRoleName]); 
        return Datatables::of($users)
        ->addIndexColumn()
        ->addColumn('roles', function ($user) { 
            return isset($user->roles[0]['name']) ? $user->roles[0]['name'] : "-";
        })
        ->editColumn('first_name', function ($user) { 
            return $user->first_name." ".$user->middle_name." ".$user->last_name;
        })
        ->editColumn('mobile_number', function ($user) { 
            return !empty($user->dial_code) ? $user->dial_code.$user->mobile_number: $user->mobile_number;
        })
        ->addColumn('action', function($user){
            $actionBtn = '';
            if(auth()->user()->can('user-list')){
                $actionBtn .= '<a href="'.route('user.detail',['id' => $user->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default button-view" data-toggle="tooltip" data-original-title="User Detail"><i class="icon-user" aria-hidden="true"></i>
                </button></a>';
            }
            if(auth()->user()->can('user-edit')){
                $actionBtn .= '<a href="'.route('user.edit',['id' => $user->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default m-r-5 button-edit" data-toggle="tooltip" data-original-title="Edit"><i class="icon-pencil" aria-hidden="true"></i> 
                </button></a>';
            }
            if(auth()->user()->can('user-delete')){
                $actionBtn .= '<a href="'.route('user.delete',['id' => $user->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default button-remove" data-toggle="tooltip" data-original-title="Remove"><i class="icon-trash" aria-hidden="true"></i></button></a>';
            }
            return $actionBtn;
           
        })
        ->rawColumns(['action','roles'])
        ->make(true);
    }

    public function getAnimalownersData($sRoleName = ''){
        
        $users = $this->getUsers(['sRoleName' => $sRoleName]); 
        return Datatables::of($users)
        ->addIndexColumn()
        ->addColumn('roles', function ($user) { 
            return isset($user->roles[0]['name']) ? $user->roles[0]['name'] : "-";
        })
        ->editColumn('first_name', function ($user) { 
            return $user->full_name;
        })
        ->editColumn('mobile_number', function ($user) { 
            return !empty($user->dial_code) ? $user->dial_code.$user->mobile_number: $user->mobile_number;
        })
		->editColumn('mypets', function ($user) { 
            return 0;
        })
        ->addColumn('action', function($user){
            $actionBtn = '';
			$actionBtn .= '<a href="'.route('animal-owner.detail',['id' => $user->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default button-view" data-toggle="tooltip" data-original-title="User Detail"><i class="icon-user" aria-hidden="true"></i>
                </button></a>';
            /*if(auth()->user()->can('animal-owner-detail')){
                
            }*/
            if(auth()->user()->can('animal-owner-edit')){
                $actionBtn .= '<a href="'.route('animal-owner.edit',['id' => $user->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default m-r-5 button-edit" data-toggle="tooltip" data-original-title="Edit"><i class="icon-pencil" aria-hidden="true"></i> 
                </button></a>';
            }
            if(auth()->user()->can('animal-owner-delete')){
                $actionBtn .= '<a href="'.route('animal-owner.delete',['id' => $user->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default button-remove" data-toggle="tooltip" data-original-title="Remove"><i class="icon-trash" aria-hidden="true"></i></button></a>';
            }
            return $actionBtn;
           
        })
        ->rawColumns(['action','roles'])
        ->make(true);
    }

    public function getPashumitrasData($sRoleName = ''){
        
        $users = $this->getUsers(['sRoleName' => $sRoleName]); 
        return Datatables::of($users)
        ->addIndexColumn()
        ->addColumn('roles', function ($user) { 
            return isset($user->roles[0]['name']) ? $user->roles[0]['name'] : "-";
        })
        ->editColumn('first_name', function ($user) { 
            return $user->full_name;
        })
        ->editColumn('mobile_number', function ($user) { 
            return !empty($user->dial_code) ? $user->dial_code.$user->mobile_number: $user->mobile_number;
        })
		->editColumn('rating', function ($user) { 
            return '-';
        })
        ->addColumn('action', function($user){
            $actionBtn = '';
			$actionBtn .= '<a href="'.route('pashumitra.detail',['id' => $user->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default button-view" data-toggle="tooltip" data-original-title="User Detail"><i class="icon-user" aria-hidden="true"></i>
                </button></a>';
            /*if(auth()->user()->can('pashumitra-detail')){
                $actionBtn .= '<a href="'.route('pashumitra.detail',['id' => $user->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default button-view" data-toggle="tooltip" data-original-title="User Detail"><i class="icon-user" aria-hidden="true"></i>
                </button></a>';
            }*/
            if(auth()->user()->can('pashumitra-edit')){
                $actionBtn .= '<a href="'.route('pashumitra.edit',['id' => $user->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default m-r-5 button-edit" data-toggle="tooltip" data-original-title="Edit"><i class="icon-pencil" aria-hidden="true"></i> 
                </button></a>'; 
            }
			
			if($user->is_verified==0){
			$actionBtn .= '<a href="'.route('pashumitra.pashumitra-verify',['id' => $user->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default m-r-5 button-edit" data-toggle="tooltip" data-original-title="Verify">Verify 
                </button></a>';
			}else{
				$actionBtn .='<a href="javascript:void(0)">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default m-r-5 button-edit" data-toggle="tooltip" data-original-title="Verify">Verified 
                </button></a>';
			}	
				
            if(auth()->user()->can('pashumitra-delete')){
                $actionBtn .= '<a href="'.route('pashumitra.delete',['id' => $user->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default button-remove" data-toggle="tooltip" data-original-title="Remove"><i class="icon-trash" aria-hidden="true"></i></button></a>';
            }
            return $actionBtn;
           
        })
        ->rawColumns(['action','roles'])
        ->make(true);
    }

    public function getRegisteredvetData($sRoleName = ''){
        
        $users = $this->getUsers(['sRoleName' => $sRoleName]); 
        return Datatables::of($users)
        ->addIndexColumn()
        ->addColumn('roles', function ($user) { 
            return isset($user->roles[0]['name']) ? $user->roles[0]['name'] : "-";
        })
        ->editColumn('first_name', function ($user) { 
            return $user->full_name;
        })
        ->editColumn('mobile_number', function ($user) { 
            return !empty($user->dial_code) ? $user->dial_code.$user->mobile_number: $user->mobile_number;
        })
		->editColumn('rating', function ($user) { 
            return '-';
        })
		->editColumn('rv_speciality', function ($user) {
			$speciality = '-';
				$filter = ['id'=>$user->id];
				$select = ['*'];
				$with  = ['getUserDetail'];			
				$userDetail = $this->getSingleRecords($filter,[],$with);
				if(isset($userDetail->getUserDetail->rv_speciality))
				{
					$speciality = $userDetail->getUserDetail->rv_speciality;
				}
            return $speciality;
        })
        ->addColumn('action', function($user){
            $actionBtn = '';
           /* if(auth()->user()->can('registeredvet-detail')){*/
                $actionBtn .= '<a href="'.route('registered-vet.detail',['id' => $user->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default button-view" data-toggle="tooltip" data-original-title="User Detail"><i class="icon-user" aria-hidden="true"></i>
                </button></a>';
          /*  }*/
            if(auth()->user()->can('registeredvet-edit')){
                $actionBtn .= '<a href="'.route('registered-vet.edit',['id' => $user->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default m-r-5 button-edit" data-toggle="tooltip" data-original-title="Edit"><i class="icon-pencil" aria-hidden="true"></i> 
                </button></a>';
            }
			
			if($user->is_verified==0){
			$actionBtn .= '<a href="'.route('registered-vet.registeredvet-verify',['id' => $user->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default m-r-5 button-edit" data-toggle="tooltip" data-original-title="Verify">Verify 
                </button></a>';
			}else{
				$actionBtn .='<a href="javascript:void(0)">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default m-r-5 button-edit" data-toggle="tooltip" data-original-title="Verify">Verified 
                </button></a>';
			}
			
            if(auth()->user()->can('registeredvet-delete')){
                $actionBtn .= '<a href="'.route('registered-vet.delete',['id' => $user->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default button-remove" data-toggle="tooltip" data-original-title="Remove"><i class="icon-trash" aria-hidden="true"></i></button></a>';
            }
            return $actionBtn;
           
        })
        ->rawColumns(['action','roles'])
        ->make(true);
    }

    public function generateOtp(){ 
        //$otp = random_number();
		$otp = rand(100000,999999);
		//$otp = 123456;
        $expMin = '+'.config('constants.otp_expiration_min').' minutes';
        $newDate = date('Y-m-d H:i:s', strtotime($expMin));
        return ['otp' => $otp,'otp_expiration' =>  $newDate];
    }
	
	public function generatePashumitraCode(){
		
		//$pm_code = str_rand(10 only digit);
		$workingPmcode = 0;
		$user=User::select('pm_code')->where('pm_code','!=','')->orderBy('id', 'DESC')->limit(1)->first();
		
		if($user){ 
			$existingPmcode = $user->pm_code;
			if($existingPmcode!=''){
				$existingPmcodeArr = explode('PM',$existingPmcode); 
				if(count($existingPmcodeArr) ==2){
					if(isset($existingPmcodeArr[1])){
						$workingPmcode = $existingPmcodeArr[1];
					}
				}
			}
		}
		
		if($workingPmcode==0){
			$workingPmcode = intval('0000000000');
		}
		
		$new_index = str_pad($workingPmcode, 10, "0", STR_PAD_LEFT);
		$newGeneretedPmcode = $new_index + 1;
		$new_index1 = str_pad($newGeneretedPmcode, 10, "0", STR_PAD_LEFT);
		$pm_code = "PM".$new_index1;
		return $pm_code;
	}
	
	public function generateRegisteredvetCode(){
		
		$workingRvcode = 0;
		$user=User::select('rv_code')->where('rv_code','!=','')->orderBy('id', 'DESC')->limit(1)->first();
		
		if($user){ 
			$existingRvcode = $user->rv_code;
			if($existingRvcode!=''){
				$existingRvcodeArr = explode('PM',$existingRvcode); 
				if(count($existingRvcodeArr) ==2){
					if(isset($existingRvcodeArr[1])){
						$workingRvcode = $existingRvcodeArr[1];
					}
				}
			}
		}
		
		if($workingRvcode==0){
			$workingRvcode = intval('0000000000');
		}
		
		$new_index = str_pad($workingRvcode, 10, "0", STR_PAD_LEFT);
		$newGeneretedRvcode = $new_index + 1;
		$new_index1 = str_pad($newGeneretedRvcode, 10, "0", STR_PAD_LEFT);
		$rv_code = "RV".$new_index1;
		return $rv_code;
	}
	
	public function checkPashumitraCode($pm_code)
	{
		/*$check = User::where('pm_code',$pm_code)->count();
		if($check==0)
		{
			return $pm_code;
		}else{
			
			$pmcode = $this->checkPashumitraCode();
			
		}*/
	}

    public function getLogsData($filter = []){
        $oLogs = Activity::where(function($query) use ($filter){
            if(!empty($filter['log_type'])){
                $query->where('log_name',$filter['log_type']);
            }else{
                $query->where('log_name','!=','error');
            }
        })->orderBy('id','desc')->get();
        return Datatables::of($oLogs)
        ->addIndexColumn()
        ->addColumn('log_name', function ($value) { 
           
            switch($value->log_name){
                case 'error':
                $class = 'badge badge-danger';
                break;
                case 'created':
                $class = 'badge badge-success';
                break;
                case 'updated':
                $class = 'badge badge-info';
                break;
                case 'deleted':
                $class = 'badge badge-light';
                break;       
                default:
                $class = 'badge badge-light';
            } 
         
            return  "<span class='".$class."'>".ucfirst($value->log_name)."</span>";
                              
        })
        ->editColumn('subject', function ($value) { 
            return !empty($value->subject->name) ? $value->subject->name: '-';
        })
        ->editColumn('properties', function ($value) { 
            return count($value->properties)>0 ? $value->properties: '-';
        })
        ->editColumn('causer_type', function ($value) { 
            return (isset($value->causer_type) &&!empty($value->causer_type)) ? $value->causer->name: '-';
        })
        ->editColumn('created_at', function ($value) { 
            return $value->created_at->format('d-m-Y H:i:s');
        })
        ->rawColumns(['log_name'])
        ->make(true);
    }
	
	public function checkUserRegistrationPayment($userId,$type)
	{
		$paymentflag= Payments::where('user_id',$userId)
							->where('payment_id','!=','')
							->where('type',$type)->where('status',1)
							->count();
		return $paymentflag;
	}
	
	public function checkProfilePaymentDetails($user_id,$role)
	{
		$filter = ['id'=>$user_id];
		$profileArray = array();
		$completedProfile =0; 
		$completedPayment =1;
		$verified=0;
		$paymentMsg = '';
		$verifyMsg = '';
		$profileMsg ='';
		
		$select = ['*'];
		$with  = ['getUserDetail'];			
		$userDetail = $this->getSingleRecords($filter,$select,$with);
		
		
		if($role=="Pashumitra")
		{
			//echo $userDetail->mobile;
			if($userDetail->full_name!='' && $userDetail->mobile_number!='' && $userDetail->date_of_birth!='' &&
			 $userDetail->sex!=''  && $userDetail['state_id']!='' && $userDetail['pincode']!='' && $userDetail['city_town']!='' && $userDetail->getUserDetail->pm_aadhar_no!='' && 
			 $userDetail->getUserDetail->pm_pan_no!='' && $userDetail->getUserDetail->job_type!=''){
				 
				 $completedProfile =1;
			 }
			 
			 $registrationPaytype = 1; // fee table pashumitra registration
			 $completedPayment = $this->checkUserRegistrationPayment($user_id,$registrationPaytype);
			 if($completedPayment == 0){
				 
				 $paymentMsg = trans('messages.complete_payment');;
				 $completedPayment =0;
			 }
		}
		
		if($role=="Registered-vet")
		{
			
			if($userDetail->full_name!='' && $userDetail->mobile_number!='' && $userDetail->date_of_birth!='' &&
			 $userDetail->sex!=''  && $userDetail['state_id']!='' && $userDetail['pincode']!='' && $userDetail['city_town']!='' && $userDetail->getUserDetail->pm_aadhar_no!='' && 
			 $userDetail->getUserDetail->pm_pan_no!='' && $userDetail->getUserDetail->job_type!='' && $userDetail->getUserDetail->rv_state_verternity_council_no!='')
			 {
				 $completedProfile =1;
			 }
			 $registrationPaytype = 6; // fee table registered vet registration
			 $completedPayment = $this->checkUserRegistrationPayment($user_id,$registrationPaytype);
			 if($completedPayment == 0){
				 
				 $paymentMsg = trans('messages.complete_payment');;
				 $completedPayment =0;
			 }
		}
		
		if($role=="Animal-owner")
		{
			$completedProfile =1;
		}
		
		if($completedProfile==0)
		{
			$profileMsg = trans('messages.complete_profile');
		}
		 
		 
		 
		 $verified=$userDetail->is_verified;
		 if($verified==0){
			 $user_name = $userDetail->full_name;
			 $verifyMsg = trans('messages.user_not_verified',['name' => $user_name]);
		}
		
		 $profileArray['verifyMsg'] = $verifyMsg;
		 $profileArray['profileMsg'] = $profileMsg;
		 $profileArray['paymentMsg'] = $paymentMsg;
		 $profileArray['completedProfile'] = $completedProfile;
		 $profileArray['completedPayment'] = $completedPayment;
		 $profileArray['verified'] = $verified; 
		
		return $profileArray;
	}
	
	public function getNearestPashumitraData($input)
	{
		return  $this->userModelRepo->whereHas('roles', function($q) use($input) {
            if(!empty($input['role'])){
                $q->where('name', 'Pashumitra');
            }
        })
		->select('id','full_name','email','mobile_number','profile_photo','address_line_1','city_town','district','taluka','pincode','latitude','longitude')
		->where('is_verified',1)
		->orderBy('id', 'DESC')
		->get();
	}
	
	public function getNearestRegisteredVetData($input)
	{
		return  $this->userModelRepo->whereHas('roles', function($q) use($input) {
            if(!empty($input['role'])){
                $q->where('name', 'Registered-vet');
            }
        })
		->select('id','full_name','email','mobile_number','profile_photo','address_line_1','city_town','district','taluka','pincode','latitude','longitude')
		->where('is_verified',1)
		->orderBy('id', 'DESC')
		->get();
	}
	
	//get latitude longitude geolocation
	public function getLatitudeLongitudes($input)
	{
		$address = $input['pincode'];
		// Google Maps API Key 
		$GOOGLE_API_KEY = 'AIzaSyBMNKT7xu6QAhJckofnXO_hFFB2OMs4u-s'; 
		 
		// Address from which the latitude and longitude will be retrieved 
		$formatted_address =$address;
		//$formatted_address = str_replace(' ', '+', $address);
		// Get geo data from Google Maps API by address 
		$geocodeFromAddr = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address={$formatted_address}&key={$GOOGLE_API_KEY}"); 
		 
		// Decode JSON data returned by API 
		$apiResponse = json_decode($geocodeFromAddr);
		
		 
		// Retrieve latitude and longitude from API data 
		$response = array();
		$response['latitude']='';$response['longitude']='';
		
		if(isset($apiResponse->results[0]->geometry->location->lat)){
			$response['latitude']  = $apiResponse->results[0]->geometry->location->lat;
		}
		
		if(isset($apiResponse->results[0]->geometry->location->lng)){
			$response['longitude'] = $apiResponse->results[0]->geometry->location->lng;
		}
		return $response;
	}
	
	public function generateOtpForMobileVerify($input)
	{ 
		$mobileverify = new MobileVerification();
		$aOtpData = $this->generateOtp();
		$mobileverify->role = $input['role'];
		$mobileverify->module_type = $input['module_type'];
		$mobileverify->mobile_number = $input['mobile_number'];
		$mobileverify->otp = $aOtpData['otp'];
		$mobileverify->otp_expiration = $aOtpData['otp_expiration'];
		$mobileverify->save();
		return $aOtpData;
	}
}
