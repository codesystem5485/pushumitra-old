<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;
use Session;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use App\Repositories\Interfaces\State\StateRepositoryInterface;
use App\Repositories\Interfaces\City\CityRepositoryInterface;
use App\Http\Requests\OtheruserProcessRequest;
use Auth;
use App\Traits\FileUpload;

use App\Http\Controllers\BaseController as BaseController;
class OtheruserController extends BaseController
{
	use FileUpload;
    protected $url = '';
    protected $userRepo;
    protected $stateRepo;
    protected $cityRepo;
    protected $roleRepo;
    public function __construct(UserRepositoryInterface $userRepo,Role $role,StateRepositoryInterface $stateRepo, CityRepositoryInterface $cityRepo){

      /*  $this->middleware('permission:otheruser-list|otheruser-create|otheruser-edit|otheruser-delete', ['only' => ['index','show']]);
        $this->middleware('permission:otheruser-create', ['only' => ['create','store']]);
        $this->middleware('permission:otheruser-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:otheruser-delete', ['only' => ['delete']]);
*/
        $this->url = [
            'listUrl' => route('otheruser.index'),
            'createUrl' => route('otheruser.create'),
        ];
        $this->userRepo = $userRepo;
        $this->stateRepo = $stateRepo;
        $this->cityRepo = $cityRepo;
        $this->roleRepo = $role;
    }

    public function index() {
        
        $roles = $this->roleRepo::get();        
        return view('backend.otheruser.index',['url' => $this->url,'roles' => $roles]);
    }

    public function getRoles(){
        return $this->roleRepo::orderBy('id','ASC')->get();
    }
    public function create(){
        $roles = $this->getRoles();
        $states = $this->stateRepo->getStates();
        return view('backend.otheruser.create',['roles' => $roles,'url' => $this->url,'states'=>$states]);
    }

    public function store(OtheruserProcessRequest $request){
        DB::beginTransaction();
        try{//set create by 
            $this->userRepo->setCreateBy(Auth::user()->id);  
            //store user data
            $aInsertData = $request->all();
			
			if($request->profile_photo!='')
			{
				$profile_photoName = $this->uploadFile($request->profile_photo,'profile_photo');
				if(!empty($profile_photoName))
				{
					 $aInsertData['profile_photo'] = $profile_photoName;
				}
			}
			
            $aInsertData['is_phone_verify'] = 1;
            $aInsertData['is_active'] = 1;
            $aInsertData['country_code'] = 'IN';
            $aInsertData['dial_code'] = '+91';

			$aInsertData['date_of_birth'] = '';
			if($request->date_of_birth!=''){
				$aInsertData['date_of_birth'] = date('Y-m-d',strtotime($request->date_of_birth));
			}
			
			//get latitude , longitude
			$coordinateArr = $this->userRepo->getLatitudeLongitudes($aInsertData);
			
			$aInsertData['latitude'] = $coordinateArr['latitude'];
			$aInsertData['longitude'] = $coordinateArr['longitude'];
			
            $user = $this->userRepo->create($aInsertData); 
            
            //asign role
            $roleData = $this->roleRepo->where('id',13)->first();

            if($roleData){
                $user->assignRole($roleData->name);  
            }
            DB::commit();
            Session::flash('success', trans('messages.user_register'));
            return redirect()->route('otheruser.index');
        }catch(\Exception $e){
            DB::rollback();
            Session::flash('error', trans('messages.something'));
            return redirect()->route('otheruser.create');
        }    
    }

    public function edit(Request $request, $id = ''){
        $user = $this->userRepo->getbyId($id,['roles:id']);
        $states = $this->stateRepo->getStates();
        $cities = $this->cityRepo->getCities(['state_id'=>$user->state_id]);
        $roles = $this->getRoles();
        return view('backend.otheruser.create',['cities'=>$cities,'states'=>$states,'user' => $user,'roles' => $roles,'url' => $this->url]);
    }   

    public function update(OtheruserProcessRequest $request, $id) 
    {
        DB::beginTransaction();
        try{
            //set create by 
            $this->userRepo->setCreateBy(Auth::user()->id);  
           
		   //store user data
		    $aInsertData = $request->all();
			
			if($request->profile_photo!='')
			{ 
				$profile_photoName = $this->uploadFile($request->profile_photo,'profile_photo');
				if(!empty($profile_photoName))
				{
					 $aInsertData['profile_photo'] = $profile_photoName;
				}
			}
			
			$aInsertData['date_of_birth'] = '';
			if($request->date_of_birth!=''){
				$aInsertData['date_of_birth'] = date('Y-m-d',strtotime($request->date_of_birth));
			}
			
			//get latitude , longitude
			$coordinateArr = $this->userRepo->getLatitudeLongitudes($aInsertData);
			
			$aInsertData['latitude'] = $coordinateArr['latitude'];
			$aInsertData['longitude'] = $coordinateArr['longitude'];
			
           
			$user = $this->userRepo->update($id,$aInsertData); 
            
            DB::commit(); 
            Session::flash('success', trans('messages.update_records'));
            ## Store log
            $message = trans('messages.update_records'); 
            storeActicityLog(trans('messages.update'),$message,Auth::user(),$user);
            return redirect()->route('otheruser.index');  
        }catch(\Exception $e){
            DB::rollback();
            Session::flash('error', trans('messages.something'));
            return redirect()->route('otheruser.edit',['id' => $id]);
        }
    }

    public function delete($id){
        DB::beginTransaction();
        try{   
            $this->userRepo->delete($id);
            DB::commit(); 
            Session::flash('success', trans('messages.delete_records'));
            return redirect()->route('otheruser.index');
        }catch(\Exception $e){
            DB::rollback();
            Session::flash('error', trans('messages.something'));
            return redirect()->route('otheruser.index');
        } 
    }

    public function userDetail($id){
        $user = $this->userRepo->getbyId($id,['roles:id']);
        return view('backend.otheruser.detail',['user'=>$user,'url' => $this->url]);
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
        $users = $this->userRepo->getOtherusersData($request->role);
        // dd($users);
        return  $users;
    }
}
