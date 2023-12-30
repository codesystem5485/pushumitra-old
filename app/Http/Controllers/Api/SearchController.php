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
use App\Http\Requests\RegisteredvetProcessRequest;
use Auth;
use App\Http\Controllers\BaseController as BaseController;
use App\Traits\PassportToken;
use App\Repositories\Interfaces\Animalsale\AnimalsaleRepositoryInterface;
use Validator;
use App\Models\AnimalForSale;
use Carbon\Carbon;
use App\Models\Breeder;
use App\Models\Books;

class SearchController extends BaseController
{
   
    protected $userRepo;
    protected $roleRepo;
	protected $animalsaleRepo;
   
    protected $userDetailRepo;
    public function __construct(AnimalsaleRepositoryInterface $animalsaleRepo,UserRepositoryInterface $userRepo,Role $role,UserDetailRepositoryInterface $userDetailRepo){

        
        $this->userRepo = $userRepo;
        $this->roleRepo = $role;
        $this->userDetailRepo = $userDetailRepo;
    }
	
	public function searchRegisteredVetDetails(Request $request){
		
		$postData = request()->all();
		
		$validator = Validator::make($postData, [
				'search_input' => 'required',
			]);
			
		if ($validator->fails())
		{
			return $this->sendError([],implode(',',$validator->errors()->all()),400);
		}
		
		$response['results']  = $this->userRepo->searchRegisteredVetDetails($postData);
		return $this->sendResponse($response,"",200);
	}
	
	public function searchAnimalForSalesDetails(Request $request){
		
		$postData = request()->all();
		$validator = Validator::make($postData, [
				'search_input' => 'required',
			]);
			
		if ($validator->fails())
		{
			return $this->sendError([],implode(',',$validator->errors()->all()),400);
		}
		
		$search_input = $postData['search_input'];
		$response['results']  =   Animalforsale::select( 'animal_for_sales.*',
            DB::raw('(select image_name from animal_images where animal_sale_id  =   animal_for_sales.id order by id asc limit 1) as image_name')  )
           ->where('breed','LIKE',"%{$search_input}%")
		   ->whereDate('animal_for_sales.subscriptionEndDate', '>=', Carbon::now())
		   ->orderBy('animal_for_sales.id','DESC')->get();
		   $response['image_base_path'] =  url("/upload/animalsale/");
		
		//$details = $this->animalsaleRepo->searchAnimalForSalesDetails($postData);
		return $this->sendResponse($response,"",200);
	}
	
	public function searchBreederDetails(Request $request){
		
		$postData = request()->all();
		$validator = Validator::make($postData, [
				'search_input' => 'required',
			]);
			
		if ($validator->fails())
		{
			return $this->sendError([],implode(',',$validator->errors()->all()),400);
		}
		
		$search_input = $postData['search_input'];
		$response['results']  =  Breeder::select('breeders.*',
            DB::raw('(select image_name from  breeder_images where breeder_id  = breeders.id order by id asc limit 1) as image_name'))
			->where('animal_breed','LIKE',"%{$search_input}%")
            ->whereDate('breeders.subscriptionEndDate', '>=', Carbon::now())
		    ->orderBy('breeders.id','DESC')->get();
		   $response['image_base_path'] =  url("/upload/breederanimals/");
		   return $this->sendResponse($response,"",200);
	}
	
	public function searchLibraryDetails(Request $request){
		
		$postData = request()->all();
		$validator = Validator::make($postData, [
				'search_input' => 'required',
			]);
			
		if ($validator->fails())
		{
			return $this->sendError([],implode(',',$validator->errors()->all()),400);
		}
		
		$search_input = $postData['search_input'];
		$response['results'] = Books::where('book_name','LIKE',"%{$search_input}%")
								->orderBy('id','ASC')->get();
		$response['file_base_path']=url("/upload/book/");
		return $this->sendResponse($response,"",200);
		   
	}
}
