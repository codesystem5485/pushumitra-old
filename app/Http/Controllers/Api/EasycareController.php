<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\Easycares;
use App\Models\EasycaresImages;
use App\Repositories\Interfaces\Easycares\EasycaresRepositoryInterface;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use DB;
use App\Traits\FileUpload;
use Validator;
use App\Models\EasycareRatings;

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
			if($request->easy_care_photo)
            {
                foreach($request->easy_care_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'easycares');
                    if($fileName)
                    {
                        EasycaresImages::create(['easycare_id'=>$results->id,'image_name' => $fileName]);
                    }
                }
            }
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
		$requestData = request()->all();
		$module_id = 0;
		if(isset($requestData['module_id']) && $requestData['module_id']!=''){
				$module_id = $requestData['module_id'];
			}
		$query  = Easycares::leftJoin('users', 'users.id', '=', 'easy_cares.user_id')
		   ->select('easy_cares.*','users.full_name',
            DB::raw('(select image_name from easycares_images where easycare_id  = easy_cares.id order by id asc limit 1) as image_name'),
			DB::raw('(select AVG(star_ratings) from review_ratings where 
rateable_id  =   easy_cares.id AND module_id ='.$module_id.' ) as star_rating_count'))
			->where('easy_cares.status', 1)
			->where('easy_cares.is_verified', 1);
			
		  if(isset($requestData['search_input']) && $requestData['search_input']!=''){
			  $words = preg_split("/[\s,]+/", $requestData['search_input'], -1);
			  $query  =$query->where(function($query) use($words){
				foreach($words as $word) {
					$query->where(function($q) use($word){
						$q->where('title', 'LIKE', '%'.$word.'%');
					});
				}
			});
		  }
		  
		  
		 
		  $total_results = $query->count();
		  if(isset($requestData['offset']) && $requestData['offset']!='' && 
		  isset($requestData['limit']) && $requestData['limit']!='')
		  {
			  $query  = $query->offset($requestData['offset'])->limit($requestData['limit']);
		  }
		  $query  = $query->orderBy('easy_cares.id','DESC')->get();
		  $response['total_count'] = $total_results;
		  $response['results'] =$query;
		  $response['image_base_path'] =  url("/upload/easycares")."/";
		  
		/*$response['results']  = Easycares::leftJoin('users', 'users.id', '=', 'easy_cares.user_id')
		   ->select('easy_cares.*','users.full_name',
            DB::raw('(select image_name from easycares_images where easycare_id  = easy_cares.id order by id asc limit 1) as image_name'),DB::raw('(select AVG(star_ratings) from easycare_ratings where rateable_id  =   easy_cares.id ) as star_rating_count'))
			->where('easy_cares.status', 1)
			->where('easy_cares.is_verified', 1)
		    ->orderBy('easy_cares.id','DESC')->get();
		   $response['image_base_path'] =  url("/upload/easycares")."/";*/
			
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
			$images_arr = EasycaresImages::where('easycare_id',$results->id)->get();
			
			$star_rating_count  = EasycareRatings::where('rateable_id',$id)->where('status',1)->avg('star_ratings');
		
			$review_exist = 0;
			if(isset($request->user_id)){
				$review_exist  = EasycareRatings::where('rateable_id',$id)->where('user_id',$request->user_id)->count();
			}
		
			$response = array('results'=>$results,'module_images' =>$images_arr,'review_exist' =>$review_exist,'star_rating_count' =>$star_rating_count);
			$response['image_base_path'] =  url("/upload/easycares")."/";
			
			return $this->sendResponse($response,trans('messages.records_found'));
        }else{
            return  $this->sendError([],trans('messages.records_not_found'),404); 
        }
    }

}