<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ratings;
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
     * @return View
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
        
		$response['results']  = Ratings::where('rateable_id',$postData['rateable_id'])->where('status',1)->avg('star_ratings');
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
		
        $response['results']= Ratings::leftJoin('users', 'users.id', '=', 'review_ratings.user_id')
							->select('review_ratings.*','users.full_name','users.city_town')
							->where('rateable_id',$postData['rateable_id'])->where('status',1)->orderBy('id','DESC')->get();
        return $this->sendResponse($response,"",200); 
    }
}
