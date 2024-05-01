<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Advertisements;
use Spatie\Permission\Models\Permission;
use Auth;
use Response;
use Carbon\Carbon;
use Validator;

class AdvertisementController extends BaseController
{
    
    protected $url = '';
   
    /**
     * Advertisements Type Construct 
     * @return url 
     */
    public function __construct(){} 

    /**
     * Advertisements List
     * @return View
     */
    public function getAdvertisementList(){
        $response['results']  = Advertisements::where('status',1)
								->whereDate('advertisement_enddate', '>=', Carbon::now())
								->orderBy('id','DESC')->get();
        $response['image_base_path']=url("/upload/adevertisements")."/";
		return $this->sendResponse($response,"",200);
    }

    
    public function advertisementDetail(Request $request){
		$postData = request()->all(); 
        $validator = Validator::make($postData, [
            'advertisement_id' => 'required',
        ]);
        $response = [];
        if ($validator->fails())
        {
            return $this->sendError($response,implode(',',$validator->errors()->all()),400);
        }
		$id = $postData['advertisement_id'];
        $response['results']= Advertisements::find($id);
        $response['image_base_path']=url("/upload/adevertisements")."/";
		return $this->sendResponse($response,"",200); 
    }
}
