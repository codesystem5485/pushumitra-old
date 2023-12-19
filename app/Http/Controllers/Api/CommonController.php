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
use App\Models\MobileVerification;

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
}