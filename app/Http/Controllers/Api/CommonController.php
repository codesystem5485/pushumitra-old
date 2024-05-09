<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;

use App\Repositories\Interfaces\State\StateRepositoryInterface;
use App\Repositories\Interfaces\City\CityRepositoryInterface;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use App\Models\Breeds;
use App\Models\Species;
use App\Models\Subcategories;
use App\Models\Categories;
use App\Models\MobileVerification;
use App\Models\Grfiles;

class CommonController extends BaseController
{
    protected $stateRepo;
	protected $userRepo;
   
    public function __construct(StateRepositoryInterface $stateRepo, CityRepositoryInterface $cityRepo,
	UserRepositoryInterface $userRepository)
    {
        $this->stateRepo = $stateRepo;
        $this->cityRepo = $cityRepo;
		$this->userRepo = $userRepository;
    }

    public function getStates()
    {
        $response['states'] = $this->stateRepo->getStates();
        return $this->sendResponse($response,"",200);
    }

    public function getCities(Request $request)
    {
        $response['cities'] = $this->cityRepo->getCities(['state_id'=>$request->state_id]);
        return $this->sendResponse($response,"",200);
    }
	
	 public function getSpecies(){
	   
	    $response['species'] = Species::where('is_active','1')->get();
		$arr = [];
		$arr1 = [];
		foreach($response['species'] as $val){
			$arr['id'] = $val['id'];
			$arr['specie'] = trans('species.'.$val['specie']);
			$arr1[] = $arr;
		}
		
		$response['species'] = $arr1;
		return $this->sendResponse($response,"",200);
    }
   
    public function getBreeds(){
		$postData = request()->all(); 
		$breeds = Breeds::where('is_active','1');
		if(isset($postData['species_id'])){
		   $breeds = $breeds->where('species',$postData['species_id']);
		}
        $breeds =$breeds->get();
		$response['breeds'] = $breeds;
		return $this->sendResponse($response,"",200);
    }
	
	public function addOtpMobileVerification(Request $request)
	{
		$postData = request()->all(); 
        $validator = Validator::make($postData, [
            'mobile_number' => 'required|max:10',
            'role'=> 'required',
        ]);

        $response = [];
        if ($validator->fails())
        {
            return $this->sendError($response,implode(',',$validator->errors()->all()),400);
        }
       
		$response = $this->userRepo->generateOtpForMobileVerify($postData);
		return $this->sendResponse($response,"",200);
	}
	
	public function getSubCategories(){
		$postData = request()->all(); 
		$categories = Subcategories::where('status',1);
		if(isset($postData['parent_category'])){
		   $categories = $categories->where('parent_category',$postData['parent_category']);
		}
        $categories =$categories->orderBy('name')->get();
		$response['results'] = $categories;
		return $this->sendResponse($response,"",200);
    }
	
	public function getParentCategories(){
		$postData = request()->all(); 
		$categories = Categories::where('status',1);
		
        $categories =$categories->get();
		$response['results'] = $categories;
		return $this->sendResponse($response,"",200);
    }
	
	public function getGrFiles(Request $request)
	{ 	
		 $postData = request()->all(); 
		 $state_id= 0;
		 if(isset($postData['state_id'])){
			  $state_id = $postData['state_id'];
		 }
		
		 $query = Grfiles::where('is_active',1);
		 if($state_id!=0){
			 $query = $query->where('state_id',$state_id);
		 }
		 
		 $response['results'] = $query->get();
		 $response['image_base_path']=url("/upload/grfiles")."/";
		 return $this->sendResponse($response,"",200);
	}
}