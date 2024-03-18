<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;
use Session;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use App\Repositories\Interfaces\User\UserDetailRepositoryInterface;
use App\Repositories\Interfaces\State\StateRepositoryInterface;
use App\Repositories\Interfaces\City\CityRepositoryInterface;
use App\Http\Requests\RegisteredvetProcessRequest;
use Auth;
use App\Http\Controllers\BaseController as BaseController;
use App\Traits\FileUpload;
use App\Traits\PassportToken;

class RegisteredvetController extends BaseController
{
    use FileUpload,PassportToken;
    protected $url = '';
    protected $userRepo;
    protected $roleRepo;
    protected $stateRepo;
    protected $cityRepo;
    protected $userDetailRepo;
    public function __construct(UserRepositoryInterface $userRepo,Role $role,StateRepositoryInterface $stateRepo,CityRepositoryInterface $cityRepo,UserDetailRepositoryInterface $userDetailRepo){

        $this->middleware('permission:registeredvet-list|registeredvet-create|registeredvet-edit|registeredvet-delete', ['only' => ['index','show']]);
        $this->middleware('permission:registeredvet-create', ['only' => ['create','store']]);
        $this->middleware('permission:registeredvet-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:registeredvet-delete', ['only' => ['delete']]);

        $this->url = [
            'listUrl' => route('registered-vet.index'),
            'createUrl' => route('registered-vet.create'),
        ];
        $this->userRepo = $userRepo;
        $this->roleRepo = $role;
        $this->stateRepo = $stateRepo;
        $this->cityRepo = $cityRepo;
        $this->userDetailRepo = $userDetailRepo;
    }

    public function index() {
        $roles = $this->roleRepo::get();
        return view('backend.registered-vet.index',['url' => $this->url,'roles' => $roles]);
    }

    public function getRoles(){
        return $this->roleRepo::orderBy('id','ASC')->get();
    }
    public function create(){
        $roles = $this->getRoles();
        $states = $this->stateRepo->getStates();
        return view('backend.registered-vet.create',['states'=>$states,'roles' => $roles,'url' => $this->url]);
    }
    public function store(RegisteredvetProcessRequest $request){
        DB::beginTransaction();
        try{//set create by 
            $this->userRepo->setCreateBy(Auth::user()->id);  
			
			
            //store user data
			
			if($request->profile_photo!='')
			{
				$profile_photoName = $this->uploadFile($request->profile_photo,'profile_photo');
				if(!empty($profile_photoName))
				{
					 $param['profile_photo'] = $profile_photoName;
				}
			}
				
            if(!empty($request->education_certificate))
            {
				$education_certificateName = $this->uploadFile($request->education_certificate,'education_certificate');
				if(!empty(($education_certificateName)))
				{
					$param['education_certificate'] = $education_certificateName;
				}
            }
            
			$birthDate = '';
			if($request->date_of_birth!=''){
				$birthDate = date('Y-m-d',strtotime($request->date_of_birth));
			}
			
			$param['full_name'] = $request->full_name;
			$param['email'] = $request->email;
			$param['mobile_number'] = $request->mobile_number;
			$param['address_line_1'] = $request->address_line_1;
			$param['taluka'] = $request->taluka;
			$param['district'] = $request->district;
			$param['city_town'] = $request->city_town;
			$param['state'] = $request->state;
			$param['state_id'] = $request->state_id;
			$param['pincode'] = $request->pincode; 
			$param['sex'] = $request->sex;
			$param['date_of_birth'] = $birthDate;
			$param['education']= $request->education;
			$param['password'] = $request->password;
			
			$paramDetail['pm_aadhar_no'] = $request->pm_aadhar_no;
			$paramDetail['pm_pan_no'] = $request->pm_pan_no;
			$paramDetail['job_type'] = $request->job_type;
			$paramDetail['rv_state_verternity_council_no'] = $request->rv_state_verternity_council_no;
            $paramDetail['rv_speciality'] = $request->rv_speciality;
            $paramDetail['rv_name_of_working_org'] = $request->rv_name_of_working_org;
			
			$param['is_phone_verify'] = 1;
            $param['is_active'] = 1;
            $param['country_code'] = 'IN';
            $param['dial_code'] = '+91'; 
			
			
			//get latitude , longitude
			$coordinateArr = $this->userRepo->getLatitudeLongitudes($param);
			
			$param['latitude'] = $coordinateArr['latitude'];
			$param['longitude'] = $coordinateArr['longitude'];
			
			$param['is_phone_verify'] = 1;
            $param['is_active'] = 1;
            $param['country_code'] = 'IN';
            $param['dial_code'] = '+91';
            $user = $this->userRepo->create($param);
			
			$paramDetail['user_id'] = $user->id;
			$oUser = $this->userDetailRepo->create($paramDetail);
            
            //asign role
            $roleData = $this->roleRepo->where('id','7')->first();

            if($roleData){
                $user->assignRole($roleData->name);  
            }

            DB::commit();
            Session::flash('success', trans('messages.user_register'));
            return redirect()->route('registered-vet.index');
        }catch(\Exception $e){
            DB::rollback();
            Session::flash('error', trans('messages.something'));
            return redirect()->route('registered-vet.create');
        }    
    }
    public function edit(Request $request, $id = ''){
        $filter = ['id'=>$id];
        $select = ['first_name,middle_name,last_name,email,mobile_number,address_line_1,address_line_2,village,city_id,state_id,city_town,state,pincode,nationality,sex,marital_status,date_of_birth,age,education,education_certificate'];
        $with = ['getUserDetail','roles']; 
        $user = $this->userRepo->getSingleRecords($filter,[],$with); 
        
        $states = $this->stateRepo->getStates();
        $cities = $this->cityRepo->getCities(['state_id'=>$user->state_id]);
        $rv_cities = $this->cityRepo->getCities(['state_id'=>$user->state_id]);
        $roles = $this->getRoles();
        return view('backend.registered-vet.create',['rv_cities'=>$rv_cities,'cities'=>$cities,'states'=>$states,'user' => $user,'roles' => $roles,'url' => $this->url]);
    }   

    public function update(RegisteredvetProcessRequest $request, $id) 
    { 
        DB::beginTransaction();
        $filter = ['id'=>$id];
        $select = ['id'];
        $with = ['getUserDetail']; 
        $userData = $this->userRepo->getSingleRecords($filter,$select,$with); 
        // dd($userDetail);
        $userDetailId = isset($userData->getUserDetail->id) ? $userData->getUserDetail->id : null;

        // try{
            //set create by 
            $this->userRepo->setCreateBy(Auth::user()->id);  
            
			if($request->profile_photo!='')
			{
				$profile_photoName = $this->uploadFile($request->profile_photo,'profile_photo');
				if(!empty($profile_photoName))
				{
					 $param['profile_photo'] = $profile_photoName;
				}
			}
				
            if(!empty($request->education_certificate))
            {
				$education_certificateName = $this->uploadFile($request->education_certificate,'education_certificate');
				if(!empty(($education_certificateName)))
				{
					$param['education_certificate'] = $education_certificateName;
				}
            }
            
			$birthDate = '';
			if($request->date_of_birth!=''){
				$birthDate = date('Y-m-d',strtotime($request->date_of_birth));
			}
			
			$param['full_name'] = $request->full_name;
			$param['email'] = $request->email;
			$param['address_line_1'] = $request->address_line_1;
			$param['taluka'] = $request->taluka;
			$param['district'] = $request->district;
			$param['city_town'] = $request->city_town;
			$param['state'] = $request->state;
			$param['state_id'] = $request->state_id;
			$param['pincode'] = $request->pincode; 
			$param['sex'] = $request->sex;
			$param['date_of_birth'] = $birthDate;
			$param['education']= $request->education;
			$param['password'] = $request->password;
			
			$paramDetail['pm_aadhar_no'] = $request->pm_aadhar_no;
			$paramDetail['pm_pan_no'] = $request->pm_pan_no;
			$paramDetail['job_type'] = $request->job_type;
			
			$paramDetail['rv_state_verternity_council_no'] = $request->rv_state_verternity_council_no;
            $paramDetail['rv_speciality'] = $request->rv_speciality;
            $paramDetail['rv_name_of_working_org'] = $request->rv_name_of_working_org;
			
			
			//get latitude , longitude
			$coordinateArr = $this->userRepo->getLatitudeLongitudes($param);
			
			$param['latitude'] = $coordinateArr['latitude'];
			$param['longitude'] = $coordinateArr['longitude'];
				
				
            $user = $this->userRepo->update($id,$param); 
			
			$userDetailId = isset($userData->getUserDetail->id) ? $userData->getUserDetail->id : null;
				
			if($userDetailId){
				$oUser = $this->userDetailRepo->update($userDetailId,$paramDetail); 
			}else{
				$paramDetail['user_id'] = $userData->id;
				$oUser = $this->userDetailRepo->create($paramDetail);
			}
			
            DB::commit(); 
            Session::flash('success', trans('messages.update_records'));
            ## Store log
            $message = trans('messages.update_records'); 
            storeActicityLog(trans('messages.update'),$message,Auth::user(),$user);
            return redirect()->route('registered-vet.index');  
        // }catch(\Exception $e){
            DB::rollback();
            Session::flash('error', trans('messages.something'));
            return redirect()->route('registered-vet.edit',['id' => $id]);
        // } 
    }

    public function delete($id){
        DB::beginTransaction();
        try{   
            $filter = ['id'=>$id];
            $select = ['id'];
            $with = ['getUserDetail']; 
            $userData = $this->userRepo->getSingleRecords($filter,$select,$with); 
            // dd($userDetail);
            $userDetailId = isset($userData->getUserDetail->id) ? $userData->getUserDetail->id : null;
            
            $this->userRepo->delete($id);
           // $this->userDetailRepo->delete($userDetailId);
			if($userDetailId!=''){
                $this->userDetailRepo->delete($userDetailId);
            }
			
            DB::commit(); 
            Session::flash('success', trans('messages.delete_records'));
            return redirect()->route('registered-vet.index');
        }catch(\Exception $e){
            DB::rollback();
            Session::flash('error', trans('messages.something'));
            return redirect()->route('registered-vet.index');
        } 
    }

    public function userDetail($id){
        $filter = ['id'=>$id];
        $select = [];
        $with = ['getUserDetail']; 
        $userDetail = $this->userRepo->getSingleRecords($filter,$select,$with); 
        return view('backend.registered-vet.detail',['user'=>$userDetail,'url' => $this->url]);
    }
    public function getRoleWiseUser(Request $request){
        $roleName = $request->role;
        $userData = $this->roleRepo->with('users')->where('name','!=','Super-Admin')
        ->where(function($query) use ($roleName){
            $query->where('name',$roleName);
        })
        ->first(); 
       $html = view('backend.users.ajax_table',['roles' => $userData])->render();
        return response()->json(['status' => true,'html' => $html]);
    }

    public function getAjaxUser(Request $request){
        return $this->userRepo->getRegisteredvetData($request->role);
    }
	
	public function registeredvetVerify($id)
	{
		$inputDetail['is_verified'] = 1;
        $user = $this->userRepo->update($id,$inputDetail);
		/*$dashboardCntArr =array('user_id'=>$id,'module_name'=>'Registered-vet_Registration','flag'=>1);
		$update = $this->userRepo->updateModuleCount($dashboardCntArr);*/
			
		Session::flash('success', trans('messages.verify_success'));
		## Store log
		$message = trans('messages.verify_success'); 
		storeActicityLog(trans('messages.verify'),$message,Auth::user(),$user);
		return redirect()->route('registered-vet.index');
	}
}
