<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\Easycares;
use App\Repositories\Interfaces\Easycares\EasycaresRepositoryInterface;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use DB;
use App\Traits\FileUpload;
use Validator;

use Carbon\Carbon;

class EasycareController extends BaseController
{
    use FileUpload;
    protected $easycareRepo;
	private $userRepo;
	
    /**
     * Easycares Construct 
     * @return url 
     */
    public function __construct(EasycaresRepositoryInterface $easycareRepo, UserRepositoryInterface $userRepository){

        $this->easycareRepo = $easycareRepo;
		$this->userRepo = $userRepository;
    } 

    /**
     * Easycares Add
   
     */
	 public function addEasycare(Request $request){
        
		$postData = request()->all();
		$validator = Validator::make($postData, [
				'title' => 'required',
				'solutions' => 'required',
				'user_code'=>'required',
				
			]);
			
		if ($validator->fails())
		{
			return $this->sendError([],implode(',',$validator->errors()->all()),400);
		}
		
        $response = [];
		
        DB::beginTransaction();
        try{            
            $aInsertData = $request->all();
            $results = $this->easycareRepo->create($aInsertData);
            DB::commit();
			## Store log
            $message = trans('messages.easycare_create',['name' => $request->title]);
            storeActicityLog(trans('messages.easycare_create'),$message);
			return $this->sendResponse($response,$message,200);
      }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,$request->user_id);
			return  $this->sendError($response,trans('messages.something'),500);			
        }
    }
	
	public function getEasycareList(Request $request)
	{
		$response['results']  = Easycares::where('status', 1)
		    ->orderBy('id','DESC')->get();
		   //$response['image_base_path'] =  url("/upload/easycare")."/";
			
		return $this->sendResponse($response,"",200);
	}
	
	public function easycaresDetail(Request $request)
	{
		$postData = request()->all();
		$validator = Validator::make($postData, [
				'detail_id' => 'required',
			]);
			
		if ($validator->fails())
		{
			return $this->sendError([],implode(',',$validator->errors()->all()),400);
		}
		$response = [];
		$id = $request->detail_id;
		
		$results =$this->easycareRepo->getEasycare($id);
		if($results){
			$response = array('results'=>$results);
			//$response['image_base_path'] =  url("/upload/easycare")."/";
			
			return $this->sendResponse($response,trans('messages.records_found'));
        }else{
            return  $this->sendError([],trans('messages.records_not_found'),404); 
        }
    }

}