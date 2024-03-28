<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ratings;
use App\Models\EasycareRatings;
use Response;
use Validator;
use DB;

class RatingsController extends BaseController
{
    
    protected $url = '';
   
    /**
     * ratings Type Construct 
     * @return url 
     */
    public function __construct(){} 

    /**
     * ratings List
     */
    public function getAverageRatings(Request $request)
	{
		$postData = request()->all();
		$validator = Validator::make($postData, [
				'rateable_id' => 'required',
			]);
			
		if ($validator->fails())
		{
			return $this->sendError([],implode(',',$validator->errors()->all()),400);
		}
		$module_id = 0;
		if(isset($postData['module_id']) && $postData['module_id']!=''){
				$module_id = $postData['module_id'];
			}
        
		$query  = Ratings::where('rateable_id',$postData['rateable_id'])->where('status',1);
		if($module_id!=0){
			$query  =$query->where('module_id',$postData['module_id']);
		}
		$query  = $query->avg('star_ratings');
		
		$response['results'] = $query;
		if($response['results']==null){
			$response['results'] = 0;
		}
        return $this->sendResponse($response,"",200);
    }
	
	public function addRatings(Request $request)
	{
		$postData = request()->all();
		$validator = Validator::make($postData, [
				'rateable_id' => 'required',
				'user_id' => 'required',
				//'review' => 'required',
				//'star_ratings' => 'required',
			]);
			
		if ($validator->fails())
		{
			return $this->sendError([],implode(',',$validator->errors()->all()),400);
		}
		$response = [];
		if ($postData['star_ratings']=='' && $postData['review']=='' )
		{
			return  $this->sendError($response,trans('messages.satrrating_or_review'),400);
		}
        
		DB::beginTransaction();
		
        try{   
			$ratings = new Ratings();
			$ratings->user_id = $postData['user_id'];
			$ratings->rateable_id = $postData['rateable_id'];
			$ratings->review_comments = $postData['review'];
			$ratings->star_ratings = $postData['star_ratings'];
			if($postData['module_id']!=''){
				$ratings->module_id = $postData['module_id'];
			}
			$ratings->save();
			
            DB::commit();
            ## Store log
            $message = trans('messages.rating_create');
            storeActicityLog(trans('messages.rating_create'),$message,$request->user_id,$ratings);
			
           return $this->sendResponse($response,trans('messages.rating_create'),200);
       }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,$request->user_id);
           
            return  $this->sendError($response,trans('messages.something'),500);
        }
    }
	
	public function showAllRatings(Request $request){
		$postData = request()->all(); 
        $validator = Validator::make($postData, [
            'rateable_id' => 'required',
        ]);
        $response = [];
        if ($validator->fails())
        {
            return $this->sendError($response,implode(',',$validator->errors()->all()),400);
        }
		$id = $postData['rateable_id'];
		$url = url('/upload/profile_photo').'/';
		
        $query = Ratings::leftJoin('users', 'users.id', '=', 'review_ratings.user_id')
		->select('review_ratings.*','full_name','city_town')
							->selectRaw(DB::raw("CONCAT('".$url."', profile_photo) as profile_image"))
							->where('rateable_id',$postData['rateable_id']);
		if($postData['module_id']!=''){
			$query = $query->where('module_id',$postData['module_id']);
		}					
							
		$query = $query->where('status',1)->orderBy('id','DESC')->get();
		
		$response['results']=$query;
        return $this->sendResponse($response,"",200); 
    }
	
	public function addEasycareRatings(Request $request)
	{
		$postData = request()->all();
		$validator = Validator::make($postData, [
				'rateable_id' => 'required',
				//'user_id' => 'required',
				//'review' => 'required',
				//'star_ratings' => 'required',
			]);
			
		if ($validator->fails())
		{
			return $this->sendError([],implode(',',$validator->errors()->all()),400);
		}
		$response = [];
		if ($postData['star_ratings']=='' && $postData['review']=='' )
		{
			return  $this->sendError($response,trans('messages.satrrating_or_review'),400);
		}
        
		DB::beginTransaction();
		
        try{   
			$ratings = new EasycareRatings();
			$ratings->user_id = $postData['user_id'];
			$ratings->rateable_id = $postData['rateable_id'];
			$ratings->review_comments = $postData['review'];
			$ratings->star_ratings = $postData['star_ratings'];
			$ratings->save();
			
            DB::commit();
            ## Store log
            $message = trans('messages.rating_create');
            storeActicityLog(trans('messages.rating_create'),$message,$request->user_id,$ratings);
			
           return $this->sendResponse($response,trans('messages.rating_create'),200);
       }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,$request->user_id);
           
            return  $this->sendError($response,trans('messages.something'),500);
        }
    }
	
    public function showEasycareAllRatings(Request $request){
		$postData = request()->all(); 
        $validator = Validator::make($postData, [
            'rateable_id' => 'required',
        ]);
        $response = [];
        if ($validator->fails())
        {
            return $this->sendError($response,implode(',',$validator->errors()->all()),400);
        }
		$id = $postData['rateable_id'];
		$url = url('/upload/profile_photo').'/';
		
        $response['results']= EasycareRatings::leftJoin('users', 'users.id', '=', 'easycare_ratings.user_id')
		->select('easycare_ratings.*','full_name','city_town')
							->selectRaw(DB::raw("CONCAT('".$url."', profile_photo) as profile_image"))
							->where('rateable_id',$postData['rateable_id'])->where('status',1)->orderBy('id','DESC')->get();
        return $this->sendResponse($response,"",200); 
    }
	
	
	public function getEasycareAverageRatings(Request $request)
	{
		$postData = request()->all();
		$validator = Validator::make($postData, [
				'rateable_id' => 'required',
			]);
			
		if ($validator->fails())
		{
			return $this->sendError([],implode(',',$validator->errors()->all()),400);
		}
        
		$response['results']  = EasycareRatings::where('rateable_id',$postData['rateable_id'])->where('status',1)->avg('star_ratings');
		if($response['results']==null){
			$response['results'] = 0;
		}
        return $this->sendResponse($response,"",200);
    }
}
