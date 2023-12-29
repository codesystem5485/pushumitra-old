<?php

namespace App\Http\Controllers\Api;

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

use App\Traits\PassportToken;

class RegisteredvetController extends BaseController
{
   
    protected $userRepo;
    protected $roleRepo;
   
    protected $userDetailRepo;
    public function __construct(UserRepositoryInterface $userRepo,Role $role,UserDetailRepositoryInterface $userDetailRepo){

        
        $this->userRepo = $userRepo;
        $this->roleRepo = $role;
        $this->userDetailRepo = $userDetailRepo;
    }
	
	 public function nearestRegisteredVetList(Request $request){
		
		$postData = request()->all();
		$response['results'] = $this->userRepo->getNearestRegisteredVetData($postData);
		return $this->sendResponse($response,"",200);
	}
	
	public function registeredVetDetail(Request $request){
		
		$id = $request->registeredvet_id;
		$filter = ['id'=>$id];
        $select = ['*'];//['id','full_name','email','mobile_number','profile_photo','address_line_1','city_town','district','taluka','pincode','education','pm_code','latitude','longitude'];
        $with = ['getUserDetail']; 
        $user = $this->userRepo->getSingleRecords($filter,$select,$with); 
        
		if($user){
			return $this->sendResponse($user,trans('messages.records_found'),200);
        }else{
            return  $this->sendError([],trans('messages.records_not_found'),404); 
        }
    }

   
}
