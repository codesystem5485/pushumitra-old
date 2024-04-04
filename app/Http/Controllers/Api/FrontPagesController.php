<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use DB;
use Response;
use App\Models\CsrActivities;
use App\Models\CsrActivityImages;
use App\Models\Testimonials;

class FrontPagesController extends BaseController
{
   
    public function __construct(){

        
    } 
	
	public function testimonials(){
		
		$response['results'] = Testimonials::where('status',1)->orderBy('id','DESC')->get();
		$response['image_base_path']=url("/upload/testimonials")."/";
		return $this->sendResponse($response,"",200);
    }

	public function csrActivities(){
		
		$response['results'] = CsrActivities::orderBy('id','DESC')->get();
		$response['image_base_path']=url("/upload/csractivities")."/";
		return $this->sendResponse($response,"",200);
    }
	
	public function csrActivityDetails(Request $request){
		$id = $request->detail_id;
		$results = CsrActivities::find($id);
		if($results){
			
			$images_arr = CsrActivityImages::where('csr_activity_id',$results->id)->get();
			$response = array('results'=>$results,'module_images' =>$images_arr);
			$response['image_base_path'] =  url("/upload/csractivities")."/";
			
			return $this->sendResponse($response,trans('messages.records_found'));
        }else{
            return  $this->sendError([],trans('messages.records_not_found'),404); 
        }
    }
}
