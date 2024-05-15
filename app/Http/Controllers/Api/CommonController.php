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
use App\Models\BreederImages;
use App\Models\AddAnimalImages;
use App\Models\VehicleImages;
use App\Models\TransporterRcbookImages;
use App\Models\VeterinaryhospitalsImages;
use App\Models\SupplierProductImages;
use App\Models\TrainingCenterImages;
use App\Models\ShopImages;
use App\Models\FarmsImages;
use App\Models\InstitutionImages;
use App\Models\DogshelterImages;
use App\Models\MilkCollectionImages;
use App\Models\PoultryHatcheryImages;
use App\Models\PanjarpolImages;
use App\Models\LabsImages;
use App\Models\NgoImages;
use App\Models\EasycaresImages;
use App\Models\AnimalImages; 
use App\Models\ProductImages;
use App\Traits\FileUpload;

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
		
		$arr = [];
		$arr1 = [];
		foreach($response['results'] as $val){
			$arr['id'] = $val['id'];
			$arr['name'] = trans('subcategories.'.$val['name']);
			$arr1[] = $arr;
		}
		
		$response['results'] = $arr1;
		return $this->sendResponse($response,"",200);
		
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
	
	public function deleteImagesUsingModuleId(Request $request){
		
		$postData = request()->all();
		$module_id = $postData['module_id'];
		$image_id = $postData['image_id'];
		$imagepath = '';
		$result='';
		
		
		switch($module_id){
            case 2:
			$result = AnimalImages::where('id',$image_id)->first();
			$imagepath = 'animalsale';
            break;
			case 3:
            $result = BreederImages::where('id',$image_id)->first();
			$imagepath = 'breederanimals';
            break;
			case 4:
            $result = BreederImages::where('id',$image_id)->first();
			$imagepath = 'breederanimals';
            break;
			case 'Add Transporter':
			$result = BreederImages::where('id',$image_id)->first();
			$imagepath = 'breederanimals';
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
		
		if($result){
			//$this->removeFile($result->image_name,$imagepath);
			$result->delete();
		}
       
		$message = 'Image deleted successfully';
		$response[]='';
        storeActicityLog(trans('messages.animalsale_update'),$message);
		return $this->sendResponse($response,$message,200);
        
		
		
	}
}