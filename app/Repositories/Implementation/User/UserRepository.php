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
use App\Models\Fee;
use App\Models\Notifications;
use App\Models\Subcategories;
use App\Models\Animals;
use App\Models\UserModuleCounts;
use App\Models\Easycares;
use App\Models\Ngo;
use App\Models\Labs;
use App\Models\Institutions;
use App\Models\PoultryHatchery;
use App\Models\MilkCollections;
use App\Models\CsrActivities;
use App\Models\Veterinaryhospitals;
use App\Models\AnimalForSale;
use App\Models\Transporters;
use App\Models\TrainingCenters;
use App\Models\Suppliers;
use App\Models\Shops;
use App\Models\ProductForSale;
use App\Models\Farms;
use App\Models\DogShelters;
use App\Models\Chemist;
use App\Models\Breeder;
use App\Models\Panjarpol;
use App\Models\UserDeleteHistory;


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
        return  $this->userModelRepo->with(['roles','getCreatedBy:id,first_name,middle_name,last_name,mobile_number,fcm_id'])
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
        return  $this->userModelRepo->with(['roles','getCreatedBy:id,full_name,mobile_number,fcm_id'])
        ->whereHas('roles', function($q) use($input) {
            if(!empty($input['sRoleName'])){
                $q->where('name', $input['sRoleName']);
            }
        })
        //->where('id','!=',1)
		->where('is_active',1)
        ->orderBy('id', 'DESC')
        ->get();
    }
	
	public function getVerifyUserCount($role,$date){
		return  $this->userModelRepo->with(['roles','getCreatedBy:id,full_name,mobile_number,fcm_id'])
        ->whereHas('roles', function($q) use($role) {
            if(!empty($role)){
                $q->where('name', $role);
            }
        })
		->where('created_at', '>',$date)
		->where('is_active',1)
        ->count();
	}
	
	public function getUsersFcmIds(array $input = [])
    {     
        return  $this->userModelRepo->with(['roles'])
		->select('fcm_id')
        ->whereHas('roles', function($q) use($input) {
            if(!empty($input['sRoleName'])){
                $q->where('name', $input['sRoleName']);
            }
        })
        //->where('id','!=',1)
		->where('is_active',1)
		->where('fcm_id','!=','')
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
			$cnt = Animals::where('animals.animal_owner',$user->id)->count();
		  
            return $cnt;
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
		 ->editColumn('added_date', function ($user) { 
		  $date ='-';
		 if($user->subscriptionStartDate!=''){
			 $date = date('d-M-Y',strtotime($user->subscriptionStartDate));
		 }
            return $date;
        })
		->editColumn('mypets', function ($user) { 
			$cnt = Animals::where('animals.animal_owner',$user->id)->count();
		  
            return $cnt;
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
		 ->editColumn('added_date', function ($user) { 
		  $date ='-';
		 if($user->subscriptionStartDate!=''){
			 $date = date('d-M-Y',strtotime($user->subscriptionStartDate));
		 }
            return $date;
        })
		->editColumn('mypets', function ($user) { 
			$cnt = Animals::where('animals.animal_owner',$user->id)->count();
		  
            return $cnt;
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
		$user=User::select('pm_code')->where('pm_code','!=','')->orderBy('pm_code', 'DESC')->limit(1)->first();
		
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
		$user=User::select('rv_code')->where('rv_code','!=','')->orderBy('rv_code', 'DESC')->limit(1)->first();
		
		if($user){ 
			$existingRvcode = $user->rv_code;
			if($existingRvcode!=''){
				$existingRvcodeArr = explode('RV',$existingRvcode); 
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
		 $profileArray['verified'] = (string)$verified; 
		
		return $profileArray;
	}
	
	public function getNearestPashumitraData($requestData)
	{
		$response =[];
		/*return  $this->userModelRepo->whereHas('roles', function($q) use($input) {
            if(!empty($input['role'])){
                $q->where('name', 'Pashumitra');
            }
        })
		->select('id','full_name','email','mobile_number','profile_photo','address_line_1','city_town','district','taluka','pincode','latitude','longitude',DB::raw('(select AVG(star_ratings) from review_ratings where rateable_id  =   users.id ) as star_rating_count'))
		->where('is_verified',1)
		->orderBy('id', 'DESC')
		->get();*/
		
		$haversine = $this->getDistanceUsingLatLong($requestData);
		
		$query = $this->userModelRepo->whereHas('roles', function($q) use($requestData) {
            //if(!empty($input['role'])){
                $q->where('name', 'Pashumitra');
           // }
        })
		->select('id','full_name','email','mobile_number','profile_photo','address_line_1','city_town','district','taluka','pincode','latitude','longitude',DB::raw('(select AVG(star_ratings) from review_ratings where rateable_id  =   users.id ) as star_rating_count'));
		
				if(isset($requestData['search_input']) && $requestData['search_input']!=''){
			  $words = preg_split("/[\s,]+/", $requestData['search_input'], -1);
			  $query  =$query->where(function($query) use($words){
				foreach($words as $word) {
					$query->where(function($q) use($word){
						$q->where('users.full_name', 'LIKE', '%'.$word.'%')
							->orWhere('users.address_line_1', 'LIKE', '%'.$word.'%')
							 ->orWhere('users.mobile_number', 'LIKE', '%'.$word.'%')
							->orWhere('users.state', 'LIKE', '%'.$word.'%')
							->orWhere('users.city_town', 'LIKE', '%'.$word.'%')
							->orWhere('users.taluka', 'LIKE', '%'.$word.'%')
							->orWhere('users.district', 'LIKE', '%'.$word.'%')
							->orWhere('users.pincode', 'LIKE', '%'.$word.'%')
							->orWhere('users.education', 'LIKE', '%'.$word.'%');
					});
				}
			});
		  }
		 
		  if($haversine!=''){
			$query  = $query->selectRaw("$haversine AS distance");
		  }
		  
		  $query  =   $query->where('is_verified',1)->where('is_active',1);
		  
		  if($haversine!=''){
			 $query  = $query->orderby("distance", "ASC");
		  }else{
			 $query  = $query->orderby("id", "DESC"); 
		  }
		  
		  $response['total_count'] = $query->count();
		  if(isset($requestData['offset']) && $requestData['offset']!='' && 
		  isset($requestData['limit']) && $requestData['limit']!='')
		  {
			  $query  = $query->offset($requestData['offset'])->limit($requestData['limit']);
		  }
		  $query  = $query->get();
		  $response['results'] =$query;
		
		return $response;
	}
	
	public function getNearestRegisteredVetData($requestData)
	{
		$response = [];
		$haversine = $this->getDistanceUsingLatLong($requestData);
		
		$query = $this->userModelRepo->whereHas('roles', function($q) use($requestData) {
           // if(!empty($input['role'])){
                $q->where('name', 'Registered-vet');
            //}
        })
		->leftJoin('user_details', 'user_details.user_id', '=', 'users.id')
		->select('users.id','user_details.rv_speciality','users.full_name','users.email','users.mobile_number','users.profile_photo','users.address_line_1','users.city_town','users.district','users.taluka','users.pincode','users.latitude','users.longitude',DB::raw('(select AVG(star_ratings) from review_ratings where rateable_id  =   users.id ) as star_rating_count'))
		->where('is_verified',1)->where('is_active',1);
		
		if(isset($requestData['search_input']) && $requestData['search_input']!=''){
			  $words = preg_split("/[\s,]+/", $requestData['search_input'], -1);
			  $query  =$query->where(function($query) use($words){
				foreach($words as $word) {
					$query->where(function($q) use($word){
						$q->where('user_details.rv_speciality', 'LIKE', '%'.$word.'%')
						   ->orWhere('users.full_name', 'LIKE', '%'.$word.'%')
						    ->orWhere('users.mobile_number', 'LIKE', '%'.$word.'%')
							->orWhere('users.address_line_1', 'LIKE', '%'.$word.'%')
							->orWhere('users.state', 'LIKE', '%'.$word.'%')
							->orWhere('users.city_town', 'LIKE', '%'.$word.'%')
							->orWhere('users.taluka', 'LIKE', '%'.$word.'%')
							->orWhere('users.district', 'LIKE', '%'.$word.'%')
							->orWhere('users.pincode', 'LIKE', '%'.$word.'%')
							->orWhere('users.education', 'LIKE', '%'.$word.'%')
							->orWhere('user_details.job_type', 'LIKE', '%'.$word.'%');
					});
				}
			});
		  }
		
		if($haversine!=''){
			$query  = $query->selectRaw("$haversine AS distance");
		  }
		  
		 
		  
		  if($haversine!=''){
			 $query  = $query->orderby("distance", "ASC");
		  }else{
			 $query  = $query->orderby("users.id", "DESC"); 
		  }
		  
		  $response['total_count'] = $query->count();
		  if(isset($requestData['offset']) && $requestData['offset']!='' && 
		  isset($requestData['limit']) && $requestData['limit']!='')
		  {
			  $query  = $query->offset($requestData['offset'])->limit($requestData['limit']);
		  }
		  $query  = $query->get();
		  $response['results'] =$query;
		
		return $response;
	}
	
	//get latitude longitude geolocation
	public function getLatitudeLongitudes($input)
	{
		$address1='';$address2='';$address3='';$address4='';
		$getAddress = '';
		if(isset($input['address_line_1']))
		{
			$address1 = $input['address_line_1'];
		}
		
		if(isset($input['address']))
		{
			$address1 = $input['address'];
		}
		
		if(isset($input['city_town']))
		{
			$address2 = $input['city_town'];
			if(isset($input['taluka']))
			{
				if($input['taluka']!=''){
					$address2 = $input['city_town'].' '.$input['taluka'];
					
					if(isset($input['district']))
					{
						if($input['district']!=''){
							$address2 = $input['city_town'].' '.$input['taluka'].' '.$input['district'];
						}
					}
				}
			}
		}
		if(isset($input['pincode']))
		{
			$address3 = $input['pincode'];
		}
		
		if(isset($input['state']))
		{
			$address4 = $input['state'];
		}
		
		if($address1!=''){
			$getAddress =$address1;
		}
		if($address2!=''){
			$getAddress.=" ".$address2;
		}
		
		if($address3!=''){
			$getAddress.=" ".$address3;
		}
		
		if($address4!=''){
			$getAddress.=" ".$address4;
		}
		
		// Google Maps API Key 
		$GOOGLE_API_KEY = 'AIzaSyBMNKT7xu6QAhJckofnXO_hFFB2OMs4u-s'; 
		 
		// Address from which the latitude and longitude will be retrieved 
		//$formatted_address =$address;
		
		$formatted_address = str_replace(' ', '+', $getAddress);
		
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
	
	//get breeder subscriptions date 
	public function getSubscriptionDates(array $input)
	{
		$subscriptionStartDate = date("Y-m-d");
		$subscriptionEndDate = '';
		$feeDetails = Fee::where('id',$input['type'])->first();
		if($feeDetails){
			$months =$feeDetails->valid_months;
			$subscriptionEndDate = date('Y-m-d', strtotime($subscriptionStartDate. ' + '.$months.' months'));
		}
		
		$dateArray =array(
			'subscriptionStartDate'=>$subscriptionStartDate,
			'subscriptionEndDate'=>$subscriptionEndDate,
		);
		
		return $dateArray;
		
	}
	
	public function searchRegisteredVetDetails($input)
	{
		$speciality = $input['search_input'];
		/*$details =  $this->userModelRepo->with(['roles','user_details'])
        ->whereHas('roles', function($q) {
           
                $q->where('name','=','Registered-vet');
            
        })
        ->where('user_details.rv_speciality',$speciality)
		->where('is_verified',1)
        ->orderBy('id', 'DESC')
        ->get();*/
		
		$details = $this->userModelRepo::select('users.*', 'user_details.rv_speciality',DB::raw('(select AVG(star_ratings) from review_ratings where rateable_id  =   users.id ) as star_rating_count'))
				->join('user_details', 'user_details.user_id', '=', 'users.id')
				->join('model_has_roles', function ($join) {
				$join->on('users.id', '=', 'model_has_roles.model_id')
					 ->where('model_has_roles.model_type', User::class);
				})
				->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
				//->where('user_details.rv_speciality',$speciality)
				->where('user_details.rv_speciality','LIKE',"%{$speciality}%")
				->where('is_verified',1)
				->orderBy('id', 'DESC')
				->get();
		
		return $details;
	}
	
	public function addPaymentToNotifications(array $input){
	
		$todayDate = date("Y-m-d");	
		$insertArray = array(
				'message' =>$input['scheduled_message'],
				'scheduled_date' => $input['scheduled_date'],
				'sender_user_id' => $input['sender_user_id'],
				'rx_reminder_id' => $input['rx_reminder_id'],
				'title' => $input['title'],
				'type' => $input['type'],
				'link'=> $input['link'],
			);
			
		$response = Notifications::create($insertArray);
		
		
		$notifications = Notifications::leftJoin('users', 'users.id', '=', 'notifications.sender_user_id')
								->select('notifications.*','users.id','users.fcm_id','notifications.id as notification_id')
								->where( 'notifications.id', $response->id)
								->first();
								
		if($notifications)
		{
				$userFcmToken = $notifications->fcm_id;
				$message = $notifications->message;
				$title = $notifications->title;
				$notificationId = $notifications->notification_id;
				$link = $notifications->link;
				
				$sendArray = array(
					'fcm_token'=> $userFcmToken,
					'message' =>$message,
					'title'=>$title
				);
				
				//send notifications
				sendNotifications($sendArray);
				
				//update send info in notifications
				$updateArray = array(
					'send_flag'=>1,
					'send_date'=>$todayDate,
					
				);
				
				$update = Notifications::where('id',$notifications->notification_id)->update($updateArray);
		}
	}
	
	//get all subscriptions date 
	public function getAllSubscriptionDates(array $input)
	{ 
		$subscriptionStartDate = date("Y-m-d");
		$subscriptionEndDate = '';
		$feeDetails = Fee::where('id',$input['type'])->first();
		if($feeDetails){
			 $months =$feeDetails->valid_months;
			$subscriptionEndDate = date('Y-m-d', strtotime($subscriptionStartDate. ' + '.$months.' months'));
		}
		
		$dateArray =array(
			'subscriptionStartDate'=>$subscriptionStartDate,
			'subscriptionEndDate'=>$subscriptionEndDate,
		);
		
		return $dateArray;
		
	}
	
	public function addAllPaymentToNotifications(array $input){
	
		$todayDate = date("Y-m-d");	
		$insertArray = array(
				'message' =>$input['scheduled_message'],
				'scheduled_date' => $input['scheduled_date'],
				'sender_user_id' => $input['sender_user_id'],
				'rx_reminder_id' => $input['rx_reminder_id'],
				'title' => $input['title'],
				'type' => $input['type'],
				'link'=> $input['link'],
			);
			
		$response = Notifications::create($insertArray);
		
		$notifications = Notifications::leftJoin('users', 'users.id', '=', 'notifications.sender_user_id')
								->select('notifications.*','users.id','users.fcm_id','notifications.id as notification_id')
								->where( 'notifications.id', $response->id)
								->first();
								
		if($notifications)
		{
				$userFcmToken = $notifications->fcm_id;
				$message = $notifications->message;
				$title = $notifications->title;
				$notificationId = $notifications->notification_id;
				$link = $notifications->link;
				
				$sendArray = array(
					'fcm_token'=> $userFcmToken,
					'message' =>$message,
					'title'=>$title
				);
				
				//send notifications
				sendNotifications($sendArray);
				
				//update send info in notifications
				$updateArray = array(
					'send_flag'=>1,
					'send_date'=>$todayDate,
					
				);
				
				$update = Notifications::where('id',$notifications->notification_id)->update($updateArray);
		}
	}
	
	public function getCategoryName($category)
	{
		$name = '';
		$details = Subcategories::where('id',$category)->first();
		if($details){
			$name=$details->name;
		}
		return $name;
	}
	
	public function getDistanceUsingLatLong($input)
	{
		$haversine = '';
		$latitude = '';$longitude='';
		if(isset($input['latitude']) && isset($input['longitude'])){
			$latitude = $input['latitude'];
			$longitude = $input['longitude'];
		}
		
		if($latitude!='' && $longitude!=''){
			$haversine = "(
					6371 * acos(
						cos(radians(" .$latitude. "))
						* cos(radians(`latitude`))
						* cos(radians(`longitude`) - radians(" .$longitude. "))
						+ sin(radians(" .$latitude. ")) * sin(radians(`latitude`))
					)
				)";
		}
		
		return $haversine;
	}
	
	//update user seen module Count 
	/*public function updateModuleCount(array $input)
	{ 
		$moduleArr=[];
		$user_id = $input['user_id'];
		$moduleName = str_replace('_', ' ', $input['module_name']);
		$chkarr = UserModuleCounts::where('user_id',$user_id)->first();
		if($chkarr->module!=''){
			$moduleArr = json_decode($chkarr->module, true);
		}
		
		$createArray = [];
		if(count($moduleArr) > 0){
			foreach($moduleArr as $key => $val){ 
				$name = $key;
				$cnt = $val;
				if($name == $moduleName)
				{
					if($input['flag']==1){
						$cnt = $cnt+1;
					}else{
						$cnt = 0;
					}	
				}
				$createArray[$name]	= $cnt;
			}
		}
		
		$module = json_encode($createArray);
		$chkarr->module = $module;
		$chkarr->save();
	}*/
	
	public function deleteUserAccount(array $input){
		$user_id = $input['user_id'];
		//$user_code = $input['user_code'];
		$replaceId = 90;
		$replaceCode = 'PM0000000001';
		
		$userArr = array('user_id'=>$replaceId,'deleted_user_id'=>$user_id,'user_code'=>$replaceCode);
		$res = $this->infoDeleteFromModules($userArr);
		
	}
	
	public function infoDeleteFromModules(array $input)
	{
		$replaceArr = array('user_id'=>$input['user_id'],'user_code'=>$input['user_code']);
		
		$easyCares = Easycares::where('user_id', $input['deleted_user_id'])->count();
		if($easyCares){
			Easycares::where('user_id', $input['deleted_user_id'])->update($replaceArr);
		}
		
		$ngo = Ngo::where('user_id', $input['deleted_user_id'])->count();
		if($ngo){
			Ngo::where('user_id', $input['deleted_user_id'])->update($replaceArr);
		}
		
		$labs = Labs::where('user_id', $input['deleted_user_id'])->count();
		if($labs){
			Labs::where('user_id', $input['deleted_user_id'])->update($replaceArr);
		}
		
		$institutions = Institutions::where('user_id', $input['deleted_user_id'])->count();
		if($institutions){
			Institutions::where('user_id', $input['deleted_user_id'])->update($replaceArr);
		}
		
		$poultry = PoultryHatchery::where('user_id', $input['deleted_user_id'])->count();
		if($poultry){
			PoultryHatchery::where('user_id', $input['deleted_user_id'])->update($replaceArr);
		}
		
		$milkCollections = MilkCollections::where('user_id', $input['deleted_user_id'])->count();
		if($milkCollections){
			MilkCollections::where('user_id', $input['deleted_user_id'])->update($replaceArr);
		}
		
		$panjarpol = Panjarpol::where('user_id', $input['deleted_user_id'])->count();
		if($panjarpol){
			Panjarpol::where('user_id', $input['deleted_user_id'])->update($replaceArr);
		}
		
		$csrActivities = CsrActivities::where('user_id', $input['deleted_user_id'])->count();
		if($csrActivities){
			CsrActivities::where('user_id', $input['deleted_user_id'])->update($replaceArr);
		}
		
		$animalForSale = AnimalForSale::where('user_id', $input['deleted_user_id'])->count();
		if($animalForSale){
			AnimalForSale::where('user_id', $input['deleted_user_id'])->update($replaceArr);
		}
		
		$veterinaryhospitals = Veterinaryhospitals::where('user_id', $input['deleted_user_id'])->count();
		if($veterinaryhospitals){
			Veterinaryhospitals::where('user_id', $input['deleted_user_id'])->update($replaceArr);
		}
		
		$transporters = Transporters::where('user_id', $input['deleted_user_id'])->count();
		if($transporters){
			Transporters::where('user_id', $input['deleted_user_id'])->update($replaceArr);
		}
		
		$trainingCenters = TrainingCenters::where('user_id', $input['deleted_user_id'])->count();
		if($trainingCenters){
			TrainingCenters::where('user_id', $input['deleted_user_id'])->update($replaceArr);
		}
		
		$suppliers = Suppliers::where('user_id', $input['deleted_user_id'])->count();
		if($suppliers){
			Suppliers::where('user_id', $input['deleted_user_id'])->update($replaceArr);
		}
		
		$shops = Shops::where('user_id', $input['deleted_user_id'])->count();
		if($shops){
			Shops::where('user_id', $input['deleted_user_id'])->update($replaceArr);
		}
		
		$productForSale = ProductForSale::where('user_id', $input['deleted_user_id'])->count();
		if($productForSale){
			ProductForSale::where('user_id', $input['deleted_user_id'])->update($replaceArr);
		}
		
		$farms = Farms::where('user_id', $input['deleted_user_id'])->count();
		if($farms){
			Farms::where('user_id', $input['deleted_user_id'])->update($replaceArr);
		}
		
		$dogShelters = DogShelters::where('user_id', $input['deleted_user_id'])->count();
		if($dogShelters){
			DogShelters::where('user_id', $input['deleted_user_id'])->update($replaceArr);
		}
		
		$chemist = Chemist::where('user_id', $input['deleted_user_id'])->count();
		if($chemist){
			Chemist::where('user_id', $input['deleted_user_id'])->update($replaceArr);
		}
		
		$breeder = Breeder::where('user_id', $input['deleted_user_id'])->count();
		if($breeder){
			Breeder::where('user_id', $input['deleted_user_id'])->update($replaceArr);
		}
		
		$animals = Animals::where('user_id', $input['deleted_user_id'])->count();
		if($animals){
			$arr = array('animal_owner'=>$replaceArr['user_id'],'user_id'=>$replaceArr['user_id']);
			Animals::where('user_id', $input['deleted_user_id'])->update($arr);
		}
		
		$users = User::where('id', $input['deleted_user_id'])->count();
		if($users){
			$arr = array('is_active'=>0);
			User::where('id', $input['deleted_user_id'])->update($arr);
		}
		
		//add to delete history 
		$addHistory = new UserDeleteHistory();
		$addHistory->deleted_user_id = $input['deleted_user_id'];
		$addHistory->replace_user_id = $replaceArr['user_id'];
		$addHistory->save();
	}
	
	public function checkUniqueMobile($reuestData){
		$flag = 0;
		$mobileNumber = $reuestData['mobile_number'];
		$exist = User::where('mobile_number',$mobileNumber)->where('is_active',1)->count();
		if($exist > 0){
			$flag = 1;
		}
		return $flag;
		                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    
	}
	
	public function checkUniqueEmail($reuestData){
		$flag = 0;
		$email = $reuestData['email'];
		$exist = User::where('email',$email)->where('is_active',1)->count();
		if($exist > 0){
			$flag = 1;
		}
		return $flag;
		                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    
	}
	
	public function getLatestModuleCount($user_id)
	{
		$moduleData = UserModuleCounts::where('user_id',$user_id)->first();
		$countArray = array();
			$countArray['breeder'] =0; $countArray['easycares'] = 0; $countArray['ngo'] = 0;
			$countArray['labs'] = 0; $countArray['institutions'] = 0; $countArray['poultryHatchery'] = 0;
			$countArray['milkCollections'] = 0; $countArray['veterinaryhospitals'] =0; $countArray['animalForSale'] = 0;
			$countArray['transporters'] = 0; $countArray['trainingCenters'] = 0; $countArray['suppliers'] = 0;
			$countArray['shops'] = 0; $countArray['productForSale'] = 0; $countArray['farms'] =0; $countArray['dogShelters'] =0;
			$countArray['chemist'] = 0; $countArray['panjarpol'] =0; $countArray['registered-vet'] = 0; $countArray['pashumitra'] = 0;
		
		if($moduleData){
			
			$breeders = date("Y-m-d H:i:s",strtotime($moduleData->breeders));
			$ngo = date("Y-m-d H:i:s",strtotime($moduleData->ngo));
			$veterinary_hospitals = date("Y-m-d H:i:s",strtotime($moduleData->veterinary_hospitals));
			$chemists = date("Y-m-d H:i:s",strtotime($moduleData->chemists));
			$dog_shelters = date("Y-m-d H:i:s",strtotime($moduleData->dog_shelters));
			$easy_cares = date("Y-m-d H:i:s",strtotime($moduleData->easy_cares));
			$farms = date("Y-m-d H:i:s",strtotime($moduleData->farms));
			$institutions = date("Y-m-d H:i:s",strtotime($moduleData->institutions));
			$labs = date("Y-m-d H:i:s",strtotime($moduleData->labs));
			$milkcollection_centers = date("Y-m-d H:i:s",strtotime($moduleData->milkcollection_centers));
			$panjarpol = date("Y-m-d H:i:s",strtotime($moduleData->panjarpol));
			$poultryhatchery_centers = date("Y-m-d H:i:s",strtotime($moduleData->poultryhatchery_centers));
			$product_for_sales = date("Y-m-d H:i:s",strtotime($moduleData->product_for_sales));
			$shops = date("Y-m-d H:i:s",strtotime($moduleData->shops));
			$suppliers = date("Y-m-d H:i:s",strtotime($moduleData->suppliers));
			$training_centers = date("Y-m-d H:i:s",strtotime($moduleData->training_centers));
			$transporters = date("Y-m-d H:i:s",strtotime($moduleData->transporters));
			$animal_for_sales = date("Y-m-d H:i:s",strtotime($moduleData->animal_for_sales));
			$pashumitra_registrations = date("Y-m-d H:i:s",strtotime($moduleData->pashumitra_registrations));
			$registered_vet_registrations = date("Y-m-d H:i:s",strtotime($moduleData->registered_vet_registrations));
			
			$countArray['breeder'] = Breeder::where('created_at', '>',$breeders) ->count();
			$countArray['easycares'] = Easycares::where('created_at', '>',$easy_cares) ->count();
			$countArray['ngo'] = Ngo::where('created_at', '>',$ngo) ->count();
			$countArray['labs'] = Labs::where('created_at', '>',$labs) ->count();
			$countArray['institutions'] = Institutions::where('created_at', '>',$institutions) ->count();
			$countArray['poultryHatchery'] = PoultryHatchery::where('created_at', '>',$poultryhatchery_centers) ->count();
			$countArray['milkCollections'] = MilkCollections::where('created_at', '>',$milkcollection_centers) ->count();
			$countArray['veterinaryhospitals'] = Veterinaryhospitals::where('created_at', '>',$veterinary_hospitals) ->count();
			$countArray['animalForSale'] = AnimalForSale::where('created_at', '>',$animal_for_sales) ->count();
			$countArray['transporters'] = Transporters::where('created_at', '>',$transporters) ->count();
			$countArray['trainingCenters'] = TrainingCenters::where('created_at', '>',$training_centers) ->count();
			$countArray['suppliers'] = Suppliers::where('created_at', '>',$suppliers) ->count();
			$countArray['shops'] = Shops::where('created_at', '>',$shops) ->count();
			$countArray['productForSale'] = ProductForSale::where('created_at', '>',$product_for_sales) ->count();
			$countArray['farms'] = Farms::where('created_at', '>',$farms) ->count();
			$countArray['dogShelters'] = DogShelters::where('created_at', '>',$dog_shelters) ->count();
			$countArray['chemist'] = Chemist::where('created_at', '>',$chemists)->count();
			$countArray['panjarpol'] = Panjarpol::where('created_at', '>',$panjarpol)->count();
			$countArray['registered-vet'] =$this->getVerifyUserCount('Registered-vet',$registered_vet_registrations);
			$countArray['pashumitra'] = $this->getVerifyUserCount('Pashumitra',$pashumitra_registrations); 
		}else{
			$insertArray =array(
					'user_id'=>$user_id,
					'breeders'=>date('Y-m-d H:i:s'),
					'ngo'=>date('Y-m-d H:i:s'),
					'veterinary_hospitals'=>date('Y-m-d H:i:s'),
					'chemists'=>date('Y-m-d H:i:s'),
					'dog_shelters'=>date('Y-m-d H:i:s'),
					'easy_cares'=>date('Y-m-d H:i:s'),
					'farms'=>date('Y-m-d H:i:s'),
					'institutions'=>date('Y-m-d H:i:s'),
					'labs'=>date('Y-m-d H:i:s'),
					'milkcollection_centers'=>date('Y-m-d H:i:s'),
					'panjarpol'=>date('Y-m-d H:i:s'),
					'poultryhatchery_centers'=>date('Y-m-d H:i:s'),
					'product_for_sales'=>date('Y-m-d H:i:s'),
					'shops'=>date('Y-m-d H:i:s'),
					'suppliers'=>date('Y-m-d H:i:s'),
					'training_centers'=>date('Y-m-d H:i:s'),
					'transporters'=>date('Y-m-d H:i:s'),
					'animal_for_sales'=>date('Y-m-d H:i:s'),
					'pashumitra_registrations'=>date('Y-m-d H:i:s'),
					'registered_vet_registrations'=>date('Y-m-d H:i:s'),
					
				);
				
				
				$insert = UserModuleCounts::create($insertArray);
				
		}
		
		return $countArray;
	}
	
	public function updateModuleCount(array $input)
	{ 
		$moduleArr=[];
		$user_id = $input['user_id'];
		$moduleName = str_replace('_', ' ', $input['module_name']);
		$chkarr = UserModuleCounts::where('user_id',$user_id)->first();
		
		switch($moduleName){
            case 'Pashumitra Registration':
            $column = 'pashumitra_registrations';
            break;
			case 'Add Animal for sale':
            $column = 'animal_for_sales';
            break;
			case 'Add Breeder':
            $column = 'breeders';
            break;
			case 'Add Transporter':
            $column = 'transporters';
            break;
			case 'Add chemist':
            $column = 'chemists';
            break;
			case 'Registered-vet Registration':
            $column = 'registered_vet_registrations';
            break;
			case 'Add Veterinary Hospitals':
            $column = 'veterinary_hospitals';
            break;	
			case 'Add Product For Sale':
            $column = 'product_for_sales';
            break;
			case 'Add Supplier':
            $column = 'suppliers';
            break;	
			case 'Add Farm':
            $column = 'farms';
            break;
			case 'Add Training Centre':
            $column = 'training_centers';
            break;
			case 'Add Shop':
            $column = 'shops';
            break;
			case 'Go Shala / Panjarpol':
            $column = 'panjarpol';
            break;
			case 'Poultry Hatchery':
            $column = 'poultryhatchery_centers';
            break;
			case 'Dog Shelter':
            $column = 'dog_shelters';
            break;
			case 'Institutions':
            $column = 'institutions';
            break;
			case 'Milk Collection':
            $column = 'milkcollection_centers';
            break;
			case 'Add Lab':
            $column = 'labs';
            break;
			case 'Add NGO':
            $column = 'ngo';
            break;
			case 'Knowledge Sharing':
            $column = 'easy_cares';
            break;			
            default:
            $column = '';    
        }
        
		if($column!=''){
			$updateArray = array( $column =>date("Y-m-d H:i:s"));
			$module = UserModuleCounts :: where('user_id',$user_id)->update($updateArray);
		}
	}
}
