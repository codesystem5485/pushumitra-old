<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\Transporters;
use App\Models\VehicleImages;
use App\Models\TransporterRcbookImages;
use App\Models\State;
use App\Repositories\Interfaces\Transporter\TransporterRepositoryInterface;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use DB;
use App\Traits\FileUpload;
use Validator;
use App\Models\Payments;
use Carbon\Carbon;

class TransporterController extends BaseController
{
    use FileUpload;
    protected $url = '';
    protected $transporterRepo;
	private $userRepo;
	
    /**
     * Transporter Construct 
     * @return url 
     */
    public function __construct(TransporterRepositoryInterface $transporterRepo, UserRepositoryInterface $userRepository){

        $this->transporterRepo = $transporterRepo;
		$this->userRepo = $userRepository;
    } 

    /**
     * Transporter Add
   
     */
	 public function addTransporter(Request $request){
        
		$postData = request()->all();
		/*$validator = Validator::make($postData, [
				'transporter_name' => 'required',
				'vehicle_name' => 'required',
				'mobile_number' => "required|numeric",
				'address' => 'required|string',
				'state' => 'required|string',
				'city_town' => 'required|string',
				'pincode' => 'required|numeric|digits:6',
				'state_id' => 'required',
				'user_code'=>'required',
				'payment_id'=>'required',
			]);
			
		if ($validator->fails())
		{
			return $this->sendError([],implode(',',$validator->errors()->all()),400);
		}*/
		
        $response = [];
		
        DB::beginTransaction();
        try{            
            $aInsertData = $request->all();
            $transporter = $this->transporterRepo->create($aInsertData);

            if($request->vehicle_photo)
            {
                foreach($request->vehicle_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'vehicle');
                    if($fileName)
                    {
                        VehicleImages::create(['transporter_id'=>$transporter->id,'image_name' => $fileName]);
                    }
                }
            }
			
			if($request->rcbook_photo)
            {
                foreach($request->rcbook_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'rcbooks');
                    if($fileName)
                    {
                        TransporterRcbookImages::create(['transporter_id'=>$transporter->id,'image_name' => $fileName]);
                    }
                }
            }
			
			$payment = Payments::find($postData['payment_id']);
			$payment->module_type_id = $transporter->id;
			$payment->save();
			
			$coordinateArr = $this->userRepo->getLatitudeLongitudes($transporter);
			$paymentArr = array( 'type'=>$payment->type,'transporter_id'=>$transporter->id);
			$subscriptionArr = $this->userRepo->getAllSubscriptionDates($paymentArr);
			
			$transporter->latitude=$coordinateArr['latitude'];
			$transporter->longitude=$coordinateArr['longitude'];
			$transporter->subscriptionStartDate=$subscriptionArr['subscriptionStartDate'];
			$transporter->subscriptionEndDate=$subscriptionArr['subscriptionEndDate'];
			$transporter->update();
			
			// Add payment notifications
			//add to notifications
			//$link = url().'receipt/download/'.$aInsertData['user_id'].'/'.$postData['payment_id'];
			
			$link = url("/invoice/download/".$aInsertData['user_id'].'/'.$postData['payment_id']);
			
			$aInsertData['sender_user_id'] = $aInsertData['user_id'];
			$aInsertData['rx_reminder_id'] = 0;
			$aInsertData['type'] = 2;
			$aInsertData['link'] = $link;
			$aInsertData['scheduled_date'] = date("Y-m-d");
			$aInsertData['scheduled_message'] ="Thank you.Your payment has been confirmed.Please download your bill receipt.";
			$aInsertData['title'] = "Payment Receipt for Transporter";
			$notifications = $this->userRepo->addAllPaymentToNotifications($aInsertData);
			
			
            
            DB::commit();
			 ## Store log
             $message = trans('messages.transporter_create',['name' => $request->input('transporter_name')]);
            storeActicityLog(trans('messages.transporter_create'),$message);
			return $this->sendResponse($response,$message,200);
       }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,$request->user_id);
			return  $this->sendError($response,trans('messages.something'),500);			
        }
    }
	
	public function updateTransporter(Request $request){
        
		$postData = request()->all();
		$response = [];
		$validator = Validator::make($postData, [
				'edit_id' => 'required',
			]);
			
		if($validator->fails())
		{
			return $this->sendError([],implode(',',$validator->errors()->all()),400);
		}
        DB::beginTransaction();
        try{            
            $aInsertData = $request->all();
            $transporter = $this->transporterRepo->update($postData['edit_id'],$aInsertData);
			
			$existing_arr = [];
			if(isset($postData['existing_vehicle_images'])){
				$existing_arr = $postData['existing_vehicle_images'];
			}
			$images = VehicleImages::where('transporter_id',$transporter->id)->get();
			if(count($images)>0)
			{
				foreach($images as $image)
				{
					if(!in_array($image['image_name'],$existing_arr)){
						$this->removeFile($image->image_name,'vehicle');
						$image->delete();
					}
				}
			}
			
			$existing_rcbook_arr = [];
			if(isset($postData['existing_rcbook_images'])){
				$existing_rcbook_arr = $postData['existing_rcbook_images'];
			}
			$rcbookimages = TransporterRcbookImages::where('transporter_id',$transporter->id)->get();
			if(count($rcbookimages)>0)
			{
				foreach($rcbookimages as $image)
				{
					if(!in_array($image['image_name'],$existing_rcbook_arr)){
						$this->removeFile($image->image_name,'rcbooks');
						$image->delete();
					}
				}
			}

            if($request->vehicle_photo)
            {
                foreach($request->vehicle_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'vehicle');
                    if($fileName)
                    {
                        VehicleImages::create(['transporter_id'=>$transporter->id,'image_name' => $fileName]);
                    }
                }
            }
			
			if($request->rcbook_photo)
            {
                foreach($request->rcbook_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'rcbooks');
                    if($fileName)
                    {
                        TransporterRcbookImages::create(['transporter_id'=>$transporter->id,'image_name' => $fileName]);
                    }
                }
            }
			
			$coordinateArr = $this->userRepo->getLatitudeLongitudes($transporter);
			$transporter->latitude=$coordinateArr['latitude'];
			$transporter->longitude=$coordinateArr['longitude'];
			$transporter->update();
            
            DB::commit();
			 ## Store log
             $message = trans('messages.transporter_update',['name' => $request->input('transporter_name')]);
            storeActicityLog(trans('messages.transporter_update'),$message);
			return $this->sendResponse($response,$message,200);
       }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,$request->user_id);
			return  $this->sendError($response,trans('messages.something'),500);			
        }
    }
	
	public function getTransporterList(Request $request)
	{
		$requestData = request()->all();
		$module_id = 0;
		if(isset($requestData['module_id']) && $requestData['module_id']!=''){
				$module_id = $requestData['module_id'];
			}
		$haversine = $this->userRepo->getDistanceUsingLatLong($requestData);
		$query  =   Transporters::select('transporters.user_code','transporters.id','transporters.transporter_name','transporters.vehicle_name',
            'transporters.mobile_number','transporters.city_town','transporters.latitude','transporters.longitude',DB::raw('(select image_name from  vehicle_images where transporter_id  = transporters.id order by id asc limit 1) as image_name'),
			DB::raw('(select AVG(star_ratings) from review_ratings where 
rateable_id  =   transporters.id AND module_id ='.$module_id.' ) as star_rating_count'));
			
		   if(isset($requestData['search_input']) && $requestData['search_input']!=''){
			  $words = preg_split("/[\s,]+/", $requestData['search_input'], -1);
			  $query  =$query->where(function($query) use($words){
				foreach($words as $word) {
					$query->where(function($q) use($word){
						$q->where('transporter_name', 'LIKE', '%'.$word.'%')
						 ->orWhere('mobile_number', 'LIKE', '%'.$word.'%')
						    ->orWhere('vehicle_name', 'LIKE', '%'.$word.'%')
							->orWhere('state', 'LIKE', '%'.$word.'%')
							->orWhere('city_town', 'LIKE', '%'.$word.'%')
								 ->orWhere('taluka', 'LIKE', '%'.$word.'%')
								  ->orWhere('district', 'LIKE', '%'.$word.'%')
								  ->orWhere('pincode', 'LIKE', '%'.$word.'%');
								
					});
				}
			});
		  } 
		  if($haversine!=''){
			$query  = $query->selectRaw("$haversine AS distance");
		  }
		  
		  $query  = $query->whereDate('transporters.subscriptionEndDate', '>=', Carbon::now())
					->where('transporters.status', 1);
		  
		  if($haversine!=''){
			$query  = $query->orderby("distance", "ASC");
		  }else{
			 $query  = $query->orderBy('transporters.id','ASC');
		  }
		 
		  $total_results = $query->count();
		  if(isset($requestData['offset']) && $requestData['offset']!='' && 
		  isset($requestData['limit']) && $requestData['limit']!='')
		  {
			  $offset = 0;
			  if($requestData['offset']!=0){
				  $offset = $requestData['offset'] * $requestData['limit'];
			  }
			  $query  = $query->offset($offset)->limit($requestData['limit']);
		  }
		  $query  = $query->get();
		  $response['total_count'] = $total_results;
		  $response['results'] =$query;
		  $response['image_base_path'] =  url("/upload/vehicle")."/";
		  
		 /* $response['results']  =   Transporters::select( 'transporters.*',
            DB::raw('(select image_name from  vehicle_images where transporter_id  = transporters.id order by id asc limit 1) as image_name'))
			->whereDate('transporters.subscriptionEndDate', '>=', Carbon::now())
			->where('transporters.status', 1)
		    ->orderBy('transporters.id','DESC')->get();
		   $response['image_base_path'] =  url("/upload/vehicle")."/";*/
			
		return $this->sendResponse($response,"",200);
	}
	
	public function transporterDetail(Request $request)
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
		$module_id = 0;
		if(isset($postData['module_id']) && $postData['module_id']!=''){
				$module_id = $postData['module_id'];
			}
		$affectedRows = Transporters::where('id', $id)->increment('views_count');
		$results = Transporters::where('id',$id)->first();
		if($results){
			$images_arr = VehicleImages::where('transporter_id',$results->id)->get();
			$images_rcbook_arr = TransporterRcbookImages::where('transporter_id',$results->id)->get();
			
			$ratings = $this->userRepo->getRatingUsingModuleId($id,$module_id,$postData);
			$results['star_rating_count'] = $ratings['star_rating_count'];
			$results['review_exist'] = $ratings['review_exist'];
			$response = array('results'=>$results,'module_images' =>$images_arr,'rcbooksImages'=>$images_rcbook_arr);
			$response['image_base_path'] =  url("/upload/vehicle")."/";
			$response['rcbooks_image_path'] =  url("/upload/rcbooks")."/";
			
			return $this->sendResponse($response,trans('messages.records_found'));
        }else{
            return  $this->sendError([],trans('messages.records_not_found'),404); 
        }
    }
	
	public function deleteTransporter(Request $request)
	{
		$postData = request()->all();		
		$validator = Validator::make($postData, [
			'id' => 'required',
		]);
			
		if ($validator->fails())
		{
			return $this->sendError([],implode(',',$validator->errors()->all()),400);
		}
	
		$id = $request->id;
        $result = Transporters::where('id',$id)->first();
		$name = '';
		if($result){
			$name = $result->transporter_name;
			$images = VehicleImages::where('transporter_id',$result->id)->get();
			if(count($images)>0)
			{
				foreach($images as $image)
				{
					$this->removeFile($image->image_name,'vehicle');
					$image->delete();
				}
			}
			$images_rcbook_arr = TransporterRcbookImages::where('transporter_id',$result->id)->get();
			if(count($images_rcbook_arr)>0)
			{
				foreach($images_rcbook_arr as $image)
				{
					$this->removeFile($image->image_name,'rcbooks');
					$image->delete();
				}
			}
			$arr = array('status'=>0);
			$transporter = $this->transporterRepo->update($id,$arr);
		}
		
        $response=[];
        ## Store log
        $message = trans('messages.transporter_delete',['name' => $name]);
        storeActicityLog(trans('messages.transporter_delete'),$message,$postData['user_id'],$result);
		return $this->sendResponse($response,$message,200);
    }

}