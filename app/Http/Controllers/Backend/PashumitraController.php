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
            // $aInsertData = $request->all();

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
            $roleData = $this->roleRepo->where('id',8)->first();

            if($roleData){
                $user->assignRole($roleData->name);  
            }

            $inputDetail['pm_collage_name'] = $request->pm_collage_name;
            $inputDetail['pm_collage_address'] = $request->pm_collage_address;
            
            $inputDetail['pm_nominee_name'] = $request->pm_nominee_name;
            $inputDetail['pm_nominee_dob'] = date('Y-m-d',strtotime($request->pm_nominee_dob));
            $inputDetail['pm_nominee_relationship'] = $request->pm_nominee_relationship;
            $inputDetail['pm_aadhar_no'] = $request->pm_aadhar_no;
            
            $pm_aadhar_photo_frontName = $this->uploadFile($request->pm_aadhar_photo_front,'aadhar_photo_front');
            $inputDetail['pm_aadhar_photo_front'] = $pm_aadhar_photo_frontName;

            $pm_aadhar_photo_backName = $this->uploadFile($request->pm_aadhar_photo_back,'aadhar_photo_back');
            $inputDetail['pm_aadhar_photo_back'] = $pm_aadhar_photo_backName;

            $inputDetail['job_type'] = $request->job_type;
            $inputDetail['pm_pan_no'] = $request->pm_pan_no;

            $pm_pan_photoName = $this->uploadFile($request->pm_pan_photo,'pan_photo');
            $inputDetail['pm_pan_photo'] = $pm_pan_photoName;

            $inputDetail['pm_bank_name'] = $request->pm_bank_name;
            $inputDetail['pm_account_no'] = $request->pm_account_no;
            $inputDetail['pm_ifsc_code'] = $request->pm_ifsc_code;

            $pm_cheque_photoName = $this->uploadFile($request->pm_cheque_photo,'cheque_photo');
            $inputDetail['pm_cheque_photo'] = $pm_cheque_photoName;
            
            $inputDetail['pm_name_of_org'] = $request->pm_name_of_org;
            $inputDetail['user_id'] = $user->id;
            $oUser = $this->userDetailRepo->create($inputDetail);
        
            
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
        $select = ['first_name,middle_name,last_name,email,mobile_number,address_line_1,address_line_2,village,city_id,state_id,city_town,state,pincode,nationality,sex,marital_status,date_of_birth,age,education,education_certificate'];
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
            // dd($userDetail);
            $userDetailId = isset($userData->getUserDetail->id) ? $userData->getUserDetail->id : null;
            
            //store user data
            $aInsertData['first_name'] = $request->first_name;
            $aInsertData['middle_name'] = $request->middle_name;
            $aInsertData['last_name'] = $request->last_name;
            $aInsertData['email'] = $request->email;
            $aInsertData['mobile_number'] = $request->mobile_number;
            $aInsertData['password'] = $request->password;
            $aInsertData['address_line_1'] = $request->address_line_1;
            $aInsertData['address_line_2'] = $request->address_line_2;
            $aInsertData['village'] = $request->village;
            $aInsertData['city_town'] = $request->city_town;
            $aInsertData['state'] = $request->state;
            $aInsertData['state_id'] = $request->state_id;
            $aInsertData['city_id'] = $request->city_id;
            $aInsertData['pincode'] = $request->pincode;
            $aInsertData['education'] = $request->education;
            if(!empty($request->education_certificate))
            {
            $education_certificateName = $this->uploadFile($request->education_certificate,'education_certificate');
            if(!empty(($education_certificateName)))
            {
            $aInsertData['education_certificate'] = $education_certificateName;
            }
            }
            $aInsertData['date_of_birth']=date('Y-m-d',strtotime($request->date_of_birth));
            $aInsertData['age']=$request->age;
            $aInsertData['nationality']=$request->nationality;
            $aInsertData['sex']=$request->sex;
            $aInsertData['marital_status']=$request->marital_status;
            $user = $this->userRepo->update($id,$aInsertData); 
            
            $inputDetail['pm_collage_name'] = $request->pm_collage_name;
            $inputDetail['pm_collage_address'] = $request->pm_collage_address;
            
            $inputDetail['pm_nominee_name'] = $request->pm_nominee_name;
            $inputDetail['pm_nominee_dob'] = date('Y-m-d',strtotime($request->pm_nominee_dob));
            $inputDetail['pm_nominee_relationship'] = $request->pm_nominee_relationship;
            $inputDetail['pm_aadhar_no'] = $request->pm_aadhar_no;
            if(!empty($request->pm_aadhar_photo_front))
            {
                $pm_aadhar_photo_frontName = $this->uploadFile($request->pm_aadhar_photo_front,'aadhar_photo_front');
                if(!empty($pm_aadhar_photo_frontName))
                {
                    $inputDetail['pm_aadhar_photo_front'] = $pm_aadhar_photo_frontName;
                }
            }
            if(!empty($request->pm_aadhar_photo_back))
            {
                $pm_aadhar_photo_backName = $this->uploadFile($request->pm_aadhar_photo_back,'aadhar_photo_back');
                if(!empty($pm_aadhar_photo_backName))
                {
                    $inputDetail['pm_aadhar_photo_back'] = $pm_aadhar_photo_backName;
                }
            }

            $inputDetail['job_type'] = $request->job_type;
            $inputDetail['pm_pan_no'] = $request->pm_pan_no;

            if(!empty($request->pm_pan_photo))
            {
                $pm_pan_photoName = $this->uploadFile($request->pm_pan_photo,'pan_photo');
                if(!empty($pm_pan_photoName))
                {
                    $inputDetail['pm_pan_photo'] = $pm_pan_photoName;
                }
            }

            $inputDetail['pm_bank_name'] = $request->pm_bank_name;
            $inputDetail['pm_account_no'] = $request->pm_account_no;
            $inputDetail['pm_ifsc_code'] = $request->pm_ifsc_code;

            if(!empty($request->pm_cheque_photo))
            {
                $pm_cheque_photoName = $this->uploadFile($request->pm_cheque_photo,'cheque_photo');
                if(!empty($pm_cheque_photoName))
                {
                    $inputDetail['pm_cheque_photo'] = $pm_cheque_photoName;
                }
            }
            
            $inputDetail['pm_name_of_org'] = $request->pm_name_of_org;
            $oUser = $this->userDetailRepo->update($userDetailId,$inputDetail);
            
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
		Session::flash('success', trans('messages.update_records'));
		## Store log
		$message = trans('messages.verify_success'); 
		storeActicityLog(trans('messages.verify'),$message,Auth::user(),$user);
		return redirect()->route('pashumitra.index');
	}
}
