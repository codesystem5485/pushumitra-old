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
use App\Http\Requests\PashumitraProcessRequest;
use Auth;
use App\Traits\FileUpload;

use App\Http\Controllers\BaseController as BaseController;
class PashumitraController extends BaseController
{
    use FileUpload;
    protected $url = '';
    protected $userRepo;
    protected $stateRepo;
    protected $cityRepo;
    protected $roleRepo;
    private $userDetailRepo;
    public function __construct(UserRepositoryInterface $userRepo,Role $role,StateRepositoryInterface $stateRepo, CityRepositoryInterface $cityRepo,UserDetailRepositoryInterface $userDetailRepository){

        $this->middleware('permission:pashumitra-list|pashumitra-create|pashumitra-edit|pashumitra-delete', ['only' => ['index','show']]);
        $this->middleware('permission:pashumitra-create', ['only' => ['create','store']]);
        $this->middleware('permission:pashumitra-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:pashumitra-delete', ['only' => ['delete']]);

        $this->url = [
            'listUrl' => route('pashumitra.index'),
            'createUrl' => route('pashumitra.create'),
        ];
        $this->userRepo = $userRepo;
        $this->stateRepo = $stateRepo;
        $this->cityRepo = $cityRepo;
        $this->roleRepo = $role;
        $this->userDetailRepo = $userDetailRepository;
    }

    public function index() {
        
        $roles = $this->roleRepo::get();        
        return view('backend.pashumitra.index',['url' => $this->url,'roles' => $roles]);
    }

    public function getRoles(){
        return $this->roleRepo::orderBy('id','ASC')->get();
    }
    public function create(){
        $roles = $this->getRoles();
        $states = $this->stateRepo->getStates();
        return view('backend.pashumitra.create',['roles' => $roles,'url' => $this->url,'states'=>$states]);
    }

    public function store(PashumitraProcessRequest $request){
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
			
			if(!empty($request->pm_cheque_photo))
			{
				$pm_cheque_photoName = $this->uploadFile($request->pm_cheque_photo,'cheque_photo');
				if(!empty($pm_cheque_photoName))
				{
					$paramDetail['pm_cheque_photo'] = $pm_cheque_photoName;
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
			$paramDetail['pm_name_of_org'] = $request->pm_name_of_org;
			$paramDetail['pm_nominee_name'] = $request->pm_nominee_name;
			$paramDetail['pm_nominee_dob'] = date('Y-m-d',strtotime($request->pm_nominee_dob));
			$paramDetail['pm_nominee_relationship'] = $request->pm_nominee_relationship;
			$paramDetail['pm_bank_name'] = $request->pm_bank_name;
			$paramDetail['pm_account_no'] = $request->pm_account_no;
			$paramDetail['pm_ifsc_code'] = $request->pm_ifsc_code;
			$paramDetail['pm_account_holdername'] = $request->pm_account_holdername;
			
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
            $roleData = $this->roleRepo->where('id',8)->first();

            if($roleData){
                $user->assignRole($roleData->name);  
            }
            
            DB::commit();
            Session::flash('success', trans('messages.user_register'));
            return redirect()->route('pashumitra.index');
        }catch(\Exception $e){
            DB::rollback();
            Session::flash('error', trans('messages.something'));
            return redirect()->route('pashumitra.create');
        }    
    }

    public function edit(Request $request, $id = ''){
        $filter = ['id'=>$id];
        $select = ['full_name,email,mobile_number,address_line_1,address_line_2,village,city_id,state_id,city_town,state,pincode,nationality,sex,marital_status,date_of_birth,age,education,education_certificate'];
        $with = ['getUserDetail','roles']; 
        $user = $this->userRepo->getSingleRecords($filter,[],$with); 
        // echo '<pre>';print_r($user);echo '</pre>';exit;
        $states = $this->stateRepo->getStates();
        $cities = $this->cityRepo->getCities(['state_id'=>$user->state_id]);
        $roles = $this->getRoles();
        return view('backend.pashumitra.create',['cities'=>$cities,'states'=>$states,'user' => $user,'roles' => $roles,'url' => $this->url]);
    }   

    public function update(PashumitraProcessRequest $request, $id) 
    {
        DB::beginTransaction();
        try{
            //set create by 
            $this->userRepo->setCreateBy(Auth::user()->id);  

            $filter = ['id'=>$id];
            $select = ['id'];
            $with = ['getUserDetail']; 
            $userData = $this->userRepo->getSingleRecords($filter,$select,$with);
           
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
			
			if(!empty($request->pm_cheque_photo))
			{
				$pm_cheque_photoName = $this->uploadFile($request->pm_cheque_photo,'cheque_photo');
				if(!empty($pm_cheque_photoName))
				{
					$paramDetail['pm_cheque_photo'] = $pm_cheque_photoName;
				}
			}
            
			$birthDate = '';
			if($request->date_of_birth!=''){
				$birthDate = date('Y-m-d',strtotime($request->date_of_birth));
			}
			
			$nomineeBirthDate = '';
			if($request->pm_nominee_dob!=''){
				$nomineeBirthDate = date('Y-m-d',strtotime($request->pm_nominee_dob));
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
			$paramDetail['pm_name_of_org'] = $request->pm_name_of_org;
			$paramDetail['pm_nominee_name'] = $request->pm_nominee_name;
			$paramDetail['pm_nominee_dob'] = $nomineeBirthDate;
			$paramDetail['pm_nominee_relationship'] = $request->pm_nominee_relationship;
			$paramDetail['pm_bank_name'] = $request->pm_bank_name;
			$paramDetail['pm_account_no'] = $request->pm_account_no;
			$paramDetail['pm_ifsc_code'] = $request->pm_ifsc_code;
			$paramDetail['pm_account_holdername'] = $request->pm_account_holdername;
			
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
            return redirect()->route('pashumitra.index');  
        }catch(\Exception $e){
            DB::rollback();
            Session::flash('error', trans('messages.something'));
            return redirect()->route('pashumitra.edit',['id' => $id]);
        } 
    }

    public function delete($id){
        DB::beginTransaction();
        try{   
            $this->userRepo->delete($id);
            DB::commit(); 
            Session::flash('success', trans('messages.delete_records'));
            return redirect()->route('pashumitra.index');
        }catch(\Exception $e){
            DB::rollback();
            Session::flash('error', trans('messages.something'));
            return redirect()->route('pashumitra.index');
        } 
    }

    public function userDetail($id){
        // $user = $this->userRepo->getbyId($id,['roles:id']);
        $filter = ['id'=>$id];
        $select = [];
        $with = ['getUserDetail','roles']; 
        $userDetail = $this->userRepo->getSingleRecords($filter,$select,$with); 
        return view('backend.pashumitra.detail',['user'=>$userDetail,'url' => $this->url]);
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
        $users = $this->userRepo->getPashumitrasData($request->role);
        // dd($users);
        return  $users;
    }
	
	 public function view(Request $request){
        $filter = ['id'=>$id];
        $select = ['first_name,middle_name,last_name,email,mobile_number,address_line_1,address_line_2,village,city_id,state_id,city_town,state,pincode,nationality,sex,marital_status,date_of_birth,age,education,education_certificate'];
        $with = ['getUserDetail','roles']; 
        $user = $this->userRepo->getSingleRecords($filter,[],$with); 
        // echo '<pre>';print_r($user);echo '</pre>';exit;
        $states = $this->stateRepo->getStates();
        $cities = $this->cityRepo->getCities(['state_id'=>$user->state_id]);
        $roles = $this->getRoles();
        return view('backend.pashumitra.view',['cities'=>$cities,'states'=>$states,'user' => $user,'roles' => $roles,'url' => $this->url]);
    }
	
	public function pashumitraVerify($id)
	{
		$inputDetail['is_verified'] = 1;
        $user = $this->userRepo->update($id,$inputDetail);
		Session::flash('success', trans('messages.verify_success'));
		## Store log
		$message = trans('messages.verify_success'); 
		storeActicityLog(trans('messages.verify'),$message,Auth::user(),$user);
		return redirect()->route('pashumitra.index');
	}
}
