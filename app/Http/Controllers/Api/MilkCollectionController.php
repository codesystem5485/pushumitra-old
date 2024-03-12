<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\MilkCollections;
use App\Models\MilkCollectionImages;
use App\Models\State;
use App\Repositories\Interfaces\Milkcollections\MilkCollectionRepositoryInterface;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use DB;
use App\Traits\FileUpload;
use Validator;
use App\Models\Payments;
use Carbon\Carbon;

class MilkCollectionController extends BaseController
{
    use FileUpload;
    protected $milkcollectionRepo;
	private $userRepo;
	
    /**
     * Transporter Construct 
     * @return url 
     */
    public function __construct(MilkCollectionRepositoryInterface $milkcollectionRepo, UserRepositoryInterface $userRepository){

        $this->milkcollectionRepo = $milkcollectionRepo;
		$this->userRepo = $userRepository;
    } 

    /**
     * Transporter Add
   
     */
	 public function addMilkcollection(Request $request){
        
		$postData = request()->all();
		/*$validator = Validator::make($postData, [
				'milkcollection_center_name' => 'required',
				//'incharge_name' => 'required',
				//'mobile_number' => "required|numeric",
				'type' => "required",
				'address' => 'required|string',
				'state' => 'required|string',
				'city_town' => 'required|string',
				'pincode' => 'required|numeric|digits:6',
				'state_id' => 'required',
				'user_code'=>'required',
				//'payment_id'=>'required',
			]);
			
		if ($validator->fails())
		{
			return $this->sendError([],implode(',',$validator->errors()->all()),400);
		}
		*/
		
        $response = [];
		
        DB::beginTransaction();
        try{            
            $aInsertData = $request->all();
            $results = $this->milkcollectionRepo->create($aInsertData);
			$category = $results->sub_category;
			

            if($request->milkcollection_photo)
            {
                foreach($request->milkcollection_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'milkcollections');
                    if($fileName)
                    {
                        MilkCollectionImages::create(['milkcollection_center_id'=>$results->id,'image_name' => $fileName]);
                    }
                }
            }
			
			$subscriptionStartDate='';
			$subscriptionEndDate='';
			
			if(isset($postData['payment_id']) && $postData['payment_id']!='' && $postData['payment_id']!=0)
			{
				$payment = Payments::find($postData['payment_id']);
				$payment->module_type_id = $results->id;
				$payment->save();
				
				$paymentArr = array( 'type'=>$payment->type,'milkcollection_id'=>$results->id);
				$subscriptionArr = $this->userRepo->getAllSubscriptionDates($paymentArr);
				
				$subscriptionStartDate=$subscriptionArr['subscriptionStartDate'];
				$subscriptionEndDate=$subscriptionArr['subscriptionEndDate'];
				
				// Add payment notifications
				//add to notifications
				$link = url("/invoice/download/".$aInsertData['user_id'].'/'.$postData['payment_id']);
				
				$aInsertData['sender_user_id'] = $aInsertData['user_id'];
				$aInsertData['rx_reminder_id'] = 0;
				$aInsertData['type'] = 2;
				$aInsertData['link'] = $link;
				$aInsertData['scheduled_date'] = date("Y-m-d");
				$aInsertData['scheduled_message'] ="Thank you.Your payment has been confirmed.Please download your bill receipt.";
				$aInsertData['title'] = "Payment Receipt for Milk Collection";
				$notifications = $this->userRepo->addAllPaymentToNotifications($aInsertData);
			}
			
			$coordinateArr = $this->userRepo->getLatitudeLongitudes($results);
			$results->latitude=$coordinateArr['latitude'];
			$results->longitude=$coordinateArr['longitude'];
			$results->subscriptionStartDate=$subscriptionStartDate;
			$results->subscriptionEndDate=$subscriptionEndDate;
			$results->update();
            
            DB::commit();
			## Store log
            $message = trans('messages.milkcollection_create',['name' => $request->milkcollection_center_name]);
            storeActicityLog(trans('messages.milkcollection_create'),$message);
			return $this->sendResponse($response,$message,200);
      }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,$request->user_id);
			return  $this->sendError($response,trans('messages.something'),500);			
        }
    }
	
	public function getMilkcollectionList(Request $request)
	{
		$requestData = request()->all();
		$haversine = $this->userRepo->getDistanceUsingLatLong($requestData);
		$query  = MilkCollections::select('id','registration_number','milkcollection_center_name','incharge_name','mobile_number','type',
		'taluka','address','city_town','district','state','pincode','latitude','longitude',
            DB::raw('(select image_name from  milkcollection_center_images where milkcollection_center_id  = milkcollection_centers.id order by id asc limit 1) as image_name'))
			->where('milkcollection_centers.status', 1)
			->where(function($query){
                            $query->where(function($query){
                                 $query->where('type','Private')->whereDate('milkcollection_centers.subscriptionEndDate', '>=', Carbon::now());
                             })
							 ->orWhere(function($query){
                                 $query->where('type','Government')->where('milkcollection_centers.subscriptionEndDate', '0000-00-00');
                             });
                         });
						 
		if($haversine!=''){
			$query  = $query->selectRaw("$haversine AS distance");
		  }
		  
		  if(isset($requestData['search_input']) && $requestData['search_input']!=''){
			  $words = preg_split("/[\s,]+/", $requestData['search_input'], -1);
			  $query  =$query->where(function($query) use($words){
				foreach($words as $word) {
					$query->where(function($q) use($word){
						$q->where('milkcollection_center_name', 'LIKE', '%'.$word.'%')
						->orWhere('type', 'LIKE', '%'.$word.'%')
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
			 $query  = $query->orderby("milkcollection_centers.id", "DESC"); 
		  }
		  
		   $response['total_count'] = $query->count();
		  if(isset($requestData['offset']) && $requestData['offset']!='' && 
		  isset($requestData['limit']) && $requestData['limit']!='')
		  {
			  $query  = $query->offset($requestData['offset'])->limit($requestData['limit']);
		  }
		  $query  = $query->get();
		  
		  $response['results'] =$query;
		  $response['image_base_path'] =  url("/upload/milkcollections")."/";
		  
		/*$query = MilkCollections::select('id','registration_number','milkcollection_center_name','incharge_name','mobile_number','type',
		'taluka','address','city_town','district','state','pincode','latitude','longitude',
            DB::raw('(select image_name from  milkcollection_center_images where milkcollection_center_id  = milkcollection_centers.id order by id asc limit 1) as image_name'))
			->where(function($query){
                            $query->where(function($query){
                                 $query->where('type','Private')->whereDate('milkcollection_centers.subscriptionEndDate', '>=', Carbon::now());
                             })
							 ->orWhere(function($query){
                                 $query->where('type','Government')->where('milkcollection_centers.subscriptionEndDate', '0000-00-00');
                             });
                         })
				->where('milkcollection_centers.status', 1)
						->orderBy('milkcollection_centers.id','DESC')->get();
			$response['results']= $query;
			$response['image_base_path'] =  url("/upload/milkcollections")."/";*/
			
		return $this->sendResponse($response,"",200);
	}
	
	public function milkcollectionDetail(Request $request)
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
		
		$results =$this->milkcollectionRepo->getMilkcollection($id);
		if($results){
			$images_arr = MilkCollectionImages::where('milkcollection_center_id',$results->id)->get();
			
			$response = array('results'=>$results,'module_images' =>$images_arr);
			$response['image_base_path'] =  url("/upload/milkcollections")."/";
			
			return $this->sendResponse($response,trans('messages.records_found'));
        }else{
            return  $this->sendError([],trans('messages.records_not_found'),404); 
        }
    }

}