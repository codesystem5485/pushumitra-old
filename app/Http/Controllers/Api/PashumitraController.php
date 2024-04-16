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
use App\Models\Ratings;

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
		
		$requestData = request()->all();
		$response = $this->userRepo->getNearestPashumitraData($requestData);
		return $this->sendResponse($response,"",200);
	}
	
	public function pashumitraDetail(Request $request){
		
		$id = $request->pashumitra_id;
		$filter = ['id'=>$id];
        $select = ['*'];//['id','full_name','email','mobile_number','profile_photo','address_line_1','city_town','district','taluka','pincode','education','pm_code','latitude','longitude'];
        $with = ['getUserDetail']; 
        $user = $this->userRepo->getSingleRecords($filter,$select,$with); 
		$moduleId = 0;
		if(isset($request->module_id) && $request->module_id!=''){
				$moduleId = $request->module_id;
			}
		$user['star_rating_count']  = Ratings::where('rateable_id',$id)->where('module_id',$moduleId)->where('status',1)->avg('star_ratings');
		
		$user['review_exist'] = 0;
		if(isset($request->user_id)){
			$user['review_exist']  = Ratings::where('rateable_id',$id)->where('user_id',$request->user_id)->where('module_id',$moduleId)->count();
		}
        
		if($user){
			return $this->sendResponse($user,trans('messages.records_found'),200);
        }else{
            return  $this->sendError([],trans('messages.records_not_found'),404); 
        }
    }
}
