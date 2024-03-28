<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\Ngo;
use App\Models\NgoImages;
use App\Models\State;
use App\Repositories\Interfaces\Ngo\NgoRepositoryInterface;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use DB;
use App\Traits\FileUpload;
use Validator;
use App\Models\Payments;
use Carbon\Carbon;

class NgoController extends BaseController
{
    use FileUpload;
    protected $ngoRepo;
	private $userRepo;
	
    /**
     * ngoRepo Construct 
     * @return url 
     */
    public function __construct(NgoRepositoryInterface $ngoRepo, UserRepositoryInterface $userRepository){

        $this->ngoRepo = $ngoRepo;
		$this->userRepo = $userRepository;
    } 

    /**
     * ngoRepo Add
   
     */
	 public function addNgo(Request $request){
        
		$postData = request()->all();
		/*$validator = Validator::make($postData, [
				'ngo_name' => 'required',
				//'manager_name' => 'required',
				//'mobile_number' => "required|numeric",
				'address' => 'required|string',
				'state' => 'required|string',
				'city_town' => 'required|string',
				'pincode' => 'required|numeric|digits:6',
				'state_id' => 'required',
				//'user_code'=>'required',
				//'payment_id'=>'required',
			]);
			
		if ($validator->fails())
		{
			return $this->sendError([],implode(',',$validator->errors()->all()),400);
		}*/
		
        $response = [];
		
        DB::beginTransaction();
        try{            
            $aInsertData = $request->all();
            $results = $this->ngoRepo->create($aInsertData);

            if($request->ngo_photo)
            {
                foreach($request->ngo_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'ngo');
                    if($fileName)
                    {
                        NgoImages::create(['ngo_id'=>$results->id,'image_name' => $fileName]);
                    }
                }
            }
			
			$subscriptionStartDate='0000-00-00';
			$subscriptionEndDate='0000-00-00';
			
			$coordinateArr = $this->userRepo->getLatitudeLongitudes($results);
			$results->latitude=$coordinateArr['latitude'];
			$results->longitude=$coordinateArr['longitude'];
			$results->update();
			
            DB::commit();
			## Store log
            $message = trans('messages.ngo_create',['name' => $request->ngo_name]);
            storeActicityLog(trans('messages.ngo_create'),$message);
			return $this->sendResponse($response,$message,200);
      }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,$request->user_id);
			return  $this->sendError($response,trans('messages.something'),500);			
        }
    }
	
	public function getNgoList(Request $request)
	{
		$requestData = request()->all();
		$module_id = 0;
		if(isset($requestData['module_id']) && $requestData['module_id']!=''){
				$module_id = $requestData['module_id'];
			}
		$haversine = $this->userRepo->getDistanceUsingLatLong($requestData);
		$query  =  Ngo::select('ngo.id','registration_number','ngo_name','manager_name','mobile_number',
		'taluka','address','city_town','district','state','pincode','latitude','longitude',
            DB::raw('(select image_name from ngo_images where ngo_id  = ngo.id order by id asc limit 1) as image_name'),
			DB::raw('(select AVG(star_ratings) from review_ratings where 
rateable_id  =   ngo.id AND module_id ='.$module_id.' ) as star_rating_count'))
			->where('ngo.status', 1);
						 
		if($haversine!=''){
			$query  = $query->selectRaw("$haversine AS distance");
		  }
		  
		  if(isset($requestData['search_input']) && $requestData['search_input']!=''){
			  $words = preg_split("/[\s,]+/", $requestData['search_input'], -1);
			  $query  =$query->where(function($query) use($words){
				foreach($words as $word) {
					$query->where(function($q) use($word){
						$q->where('ngo_name', 'LIKE', '%'.$word.'%')
						->orWhere('city_town', 'LIKE', '%'.$word.'%')
							 ->orWhere('taluka', 'LIKE', '%'.$word.'%')
							 ->orWhere('district', 'LIKE', '%'.$word.'%')
							 ->orWhere('pincode', 'LIKE', '%'.$word.'%');
					});
				}
			});
		  }
		  
		   if($haversine!=''){
			$query  = $query->orderby("distance", "ASC");
		  }else{
			 $query  = $query->orderby("ngo.id", "DESC"); 
		  }
		  
		   $response['total_count'] = $query->count();
		  if(isset($requestData['offset']) && $requestData['offset']!='' && 
		  isset($requestData['limit']) && $requestData['limit']!='')
		  {
			  $query  = $query->offset($requestData['offset'])->limit($requestData['limit']);
		  }
		  $query  = $query->get();
		  
		  $response['results'] =$query;
		  $response['image_base_path'] =  url("/upload/ngo")."/";
		/*  
		$response['results']  = Ngo::select('ngo.id','registration_number','ngo_name','manager_name','mobile_number',
		'taluka','address','city_town','district','state','pincode','latitude','longitude',
            DB::raw('(select image_name from ngo_images where ngo_id  = ngo.id order by id asc limit 1) as image_name'))
			->where('ngo.status', 1)
		    ->orderBy('ngo.id','DESC')->get();
		   $response['image_base_path'] =  url("/upload/ngo")."/";*/
			
		return $this->sendResponse($response,"",200);
	}
	
	public function ngoDetail(Request $request)
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
		
		$results =$this->ngoRepo->getNgo($id);
		if($results){
			$images_arr = NgoImages::where('ngo_id',$results->id)->get();
			
			$response = array('results'=>$results,'module_images' =>$images_arr);
			$response['image_base_path'] =  url("/upload/ngo")."/";
			
			return $this->sendResponse($response,trans('messages.records_found'));
        }else{
            return  $this->sendError([],trans('messages.records_not_found'),404); 
        }
    }

}