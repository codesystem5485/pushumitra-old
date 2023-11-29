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
            $aInsertData = $request->all();

            $aInsertData['first_name'] = $request->first_name;
            $aInsertData['middle_name'] = $request->middle_name;
            $aInsertData['last_name'] = $request->last_name;
            $aInsertData['email'] = $request->email;
            $aInsertData['mobile_number'] = $request->mobile_number;
            $aInsertData['password'] = $request->password;
            $aInsertData['confirm_password'] = $request->confirm_password;
            $aInsertData['address_line_1'] = $request->address_line_1;
            $aInsertData['address_line_2'] = $request->address_line_2;
            $aInsertData['village'] = $request->village;
            $aInsertData['city_town'] = $request->city_town;
            $aInsertData['state'] = $request->state;
            $aInsertData['state_id'] = $request->state_id;
            $aInsertData['city_id'] = $request->city_id;
            $aInsertData['pincode'] = $request->pincode;
            $aInsertData['education'] = $request->education;
            
            $education_certificateName = $this->uploadFile($request->education_certificate,'education_certificate');
            $aInsertData['education_certificate'] = $education_certificateName;

            $aInsertData['date_of_birth']=date('Y-m-d',strtotime($request->date_of_birth));
            $aInsertData['age']=$request->age;
            $aInsertData['nationality']=$request->nationality;
            $aInsertData['sex']=$request->sex;
            $aInsertData['marital_status']=$request->marital_status;
            $aInsertData['is_phone_verify'] = 1;
            $aInsertData['is_active'] = 1;
            $aInsertData['country_code'] = 'IN';
            $aInsertData['dial_code'] = '+91'; 
            $user = $this->userRepo->create($aInsertData); 
            
            //asign role
            $roleData = $this->roleRepo->where('id','7')->first();

            if($roleData){
                $user->assignRole($roleData->name);  
            }

            $inputDetail['job_type'] = $request->job_type;
            $inputDetail['rv_state_verternity_council'] = $request->rv_state_verternity_council;
            $inputDetail['rv_state_verternity_council_no'] = $request->rv_state_verternity_council_no;
            $inputDetail['rv_speciality'] = $request->rv_speciality;
            $inputDetail['rv_name_of_working_org'] = $request->rv_name_of_working_org;
            $inputDetail['rv_working_state'] = $request->rv_working_state;
            $inputDetail['rv_working_state_id'] = $request->rv_working_state_id;
            $inputDetail['rv_working_city_town'] = $request->rv_working_city_town;
            $inputDetail['rv_working_city_id'] = $request->rv_working_city_id;
            $inputDetail['rv_working_village'] = $request->rv_working_village;
            $inputDetail['rv_working_pincode'] = $request->rv_working_pincode;
            $inputDetail['user_id'] = $user->id;
            $oUser = $this->userDetailRepo->create($inputDetail);

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
        $rv_cities = $this->cityRepo->getCities(['state_id'=>$user->getUserDetail->rv_working_state_id]);
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
            $aUpdateData['first_name'] = $request->first_name;
            $aUpdateData['middle_name'] = $request->middle_name;
            $aUpdateData['last_name'] = $request->last_name;
            if(!empty($request->email))
            {
                $aUpdateData['email'] = $request->email;
            }
            if(!empty($request->mobile_number))
            {
                $aUpdateData['mobile_number'] = $request->mobile_number;
            }
            if(!empty($request->password))
            {
                $aUpdateData['password'] = $request->password;
            }
            $aUpdateData['address_line_1'] = $request->address_line_1;
            $aUpdateData['address_line_2'] = $request->address_line_2;
            $aUpdateData['village'] = $request->village;
            $aUpdateData['city_town'] = $request->city_town;
            $aUpdateData['state'] = $request->state;
            $aUpdateData['state_id'] = $request->state_id;
            $aUpdateData['city_id'] = $request->city_id;
            $aUpdateData['pincode'] = $request->pincode;
            $aUpdateData['education'] = $request->education;
            if(!empty($request->education_certificate)){
            $education_certificateName = $this->uploadFile($request->education_certificate,'education_certificate');
            $aUpdateData['education_certificate'] = $education_certificateName;
            }
            $aUpdateData['date_of_birth']=date('Y-m-d',strtotime($request->date_of_birth));
            $aUpdateData['age']=$request->age;
            $aUpdateData['nationality']=$request->nationality;
            $aUpdateData['sex']=$request->sex;
            $aUpdateData['marital_status']=$request->marital_status;
            $user = $this->userRepo->update($id,$aUpdateData);
            
            if($userDetailId != null)
            {
            $inputDetail['job_type'] = $request->job_type;
            $inputDetail['rv_state_verternity_council'] = $request->rv_state_verternity_council;
            $inputDetail['rv_state_verternity_council_no'] = $request->rv_state_verternity_council_no;
            $inputDetail['rv_speciality'] = $request->rv_speciality;
            $inputDetail['rv_name_of_working_org'] = $request->rv_name_of_working_org;
            $inputDetail['rv_working_state'] = $request->rv_working_state;
            $inputDetail['rv_working_state_id'] = $request->rv_working_state_id;
            $inputDetail['rv_working_city_town'] = $request->rv_working_city_town;
            $inputDetail['rv_working_city_id'] = $request->rv_working_city_id;
            $inputDetail['rv_working_village'] = $request->rv_working_village;
            $inputDetail['rv_working_pincode'] = $request->rv_working_pincode;
            $oUser = $this->userDetailRepo->update($userDetailId,$inputDetail);            
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
            $this->userDetailRepo->delete($userDetailId);
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
		Session::flash('success', trans('messages.verify_success'));
		## Store log
		$message = trans('messages.verify_success'); 
		storeActicityLog(trans('messages.verify'),$message,Auth::user(),$user);
		return redirect()->route('registered-vet.index');
	}
}
