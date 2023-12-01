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
use App\Http\Requests\PashumitraProcessRequest;
use Auth;
use App\Traits\FileUpload;

use App\Http\Controllers\BaseController as BaseController;
class PashumitraController extends BaseController
{
    
    protected $userRepo;
    protected $roleRepo;
    private $userDetailRepo;
    public function __construct(UserRepositoryInterface $userRepo,Role $role,UserDetailRepositoryInterface $userDetailRepository){

       
        $this->userRepo = $userRepo;
        
        $this->roleRepo = $role;
        $this->userDetailRepo = $userDetailRepository;
    }

    public function nearestPashumitraList(Request $request){
		
		$postData = request()->all();
		$users = $this->userRepo->getNearestPashumitraData($postData);
		return $this->sendResponse($users,"",200);
	}
	
	public function pashumitraDetail(Request $request){
		
		$id = $request->pashumitra_id;
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
