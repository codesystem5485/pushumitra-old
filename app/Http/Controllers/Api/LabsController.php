<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\Labs;
use App\Models\LabsImages;
use App\Models\State;
use App\Repositories\Interfaces\Labs\LabsRepositoryInterface;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use DB;
use App\Traits\FileUpload;
use Validator;
use App\Models\Payments;
use Carbon\Carbon;

class LabsController extends BaseController
{
    use FileUpload;
    protected $url = '';
    protected $labsRepo;
	private $userRepo;
	
    /**
     * Lab Construct 
     * @return url 
     */
    public function __construct(LabsRepositoryInterface $labsRepo, UserRepositoryInterface $userRepository){

        $this->labsRepo = $labsRepo;
		$this->userRepo = $userRepository;
    } 

    /**
     * Lab Add
   
     */
	 public function addLab(Request $request){
        
		$postData = request()->all();
		/*$validator = Validator::make($postData, [
				'lab_name' => 'required',
				//'owner_name' => 'required',
				'sub_category'=>'required',
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
		}
		*/
        $response = [];
		
       DB::beginTransaction();
       try{            
            $aInsertData = $request->all();
            $labs = $this->labsRepo->create($aInsertData);
			$category = $labs->sub_category;
			
			$categoryName = $this->userRepo->getCategoryName($category);

            if($request->labs_photo)
            {
                foreach($request->labs_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'labs');
                    if($fileName)
                    {
                        LabsImages::create(['lab_id'=>$labs->id,'image_name' => $fileName]);
                    }
                }
            }
			$subscriptionStartDate='';
			$subscriptionEndDate='';
			
			if(isset($postData['payment_id']) && $postData['payment_id']!='' && $postData['payment_id']!=0)
			{
			
				$payment = Payments::find($postData['payment_id']);
				$payment->module_type_id = $labs->id;
				$payment->save();
				
				
				$paymentArr = array( 'type'=>$payment->type,'lab_id'=>$labs->id);
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
				$aInsertData['title'] = "Payment Receipt for '".$categoryName."'";
				$notifications = $this->userRepo->addAllPaymentToNotifications($aInsertData);
			}
			
			$coordinateArr = $this->userRepo->getLatitudeLongitudes($labs);
			$labs->latitude=$coordinateArr['latitude'];
			$labs->longitude=$coordinateArr['longitude'];
			$labs->subscriptionStartDate=$subscriptionStartDate;
			$labs->subscriptionEndDate=$subscriptionEndDate;
			$labs->update();
			
		
			DB::commit();
			## Store log
            $message = trans('messages.lab_create',['name' => $request->lab_name]);
            storeActicityLog(trans('messages.lab_create'),$message);
			return $this->sendResponse($response,$message,200);
      }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,$request->user_id);
			return  $this->sendError($response,trans('messages.something'),500);			
        }
    }
	
	public function getLabList(Request $request)
	{
		$requestData = request()->all();
		$haversine = $this->userRepo->getDistanceUsingLatLong($requestData);
		$query  =   Labs::leftJoin('subcategories', 'subcategories.id', '=', 'labs.sub_category')
			->select('labs.id','lab_name','owner_name','mobile_number','education','type','svc_registration_number',
		'taluka','address','city_town','district','state','pincode','latitude','longitude','subcategories.name as subcategory_name',
            DB::raw('(select image_name from  labs_images where lab_id  = labs.id order by id asc limit 1) as image_name'))
			->where(function($query){
                            $query->where(function($query){
                                 $query->where('type','Private')->whereDate('labs.subscriptionEndDate', '>=', Carbon::now());
                             })
							 ->orWhere(function($query){
                                 $query->where('type','Government')->where('labs.subscriptionEndDate', '0000-00-00');
                             });
                         })
				->where('labs.status', 1);
						 
		   if(isset($requestData['search_input']) && $requestData['search_input']!=''){
			  $words = preg_split("/[\s,]+/", $requestData['search_input'], -1);
			  $query  =$query->where(function($query) use($words){
				foreach($words as $word) {
					$query->where(function($q) use($word){
						$q->where('lab_name', 'LIKE', '%'.$word.'%')
						 ->orWhere('subcategories.name', 'LIKE', '%'.$word.'%')
						    ->orWhere('city_town', 'LIKE', '%'.$word.'%')
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
		  
		 
		  if($haversine!=''){
			$query  = $query->orderby("distance", "ASC");
		  }else{
			 $query  = $query->orderBy('labs.id','DESC');
		  }
		 
		  $total_results = $query->count();
		  if(isset($requestData['offset']) && $requestData['offset']!='' && 
		  isset($requestData['limit']) && $requestData['limit']!='')
		  {
			  $query  = $query->offset($requestData['offset'])->limit($requestData['limit']);
		  }
		  $query  = $query->get();
		  $response['total_count'] = $total_results;
		  $response['results'] =$query;
		  $response['image_base_path'] =  url("/upload/labs")."/";
		  
		/*$query = Labs::leftJoin('subcategories', 'subcategories.id', '=', 'labs.sub_category')
			->select('labs.id','lab_name','owner_name','mobile_number','education','type','svc_registration_number',
		'taluka','address','city_town','district','state','pincode','latitude','longitude','subcategories.name as subcategory_name',
            DB::raw('(select image_name from  labs_images where lab_id  = labs.id order by id asc limit 1) as image_name'))
			->where(function($query){
                            $query->where(function($query){
                                 $query->where('type','Private')->whereDate('labs.subscriptionEndDate', '>=', Carbon::now());
                             })
							 ->orWhere(function($query){
                                 $query->where('type','Government')->where('labs.subscriptionEndDate', '0000-00-00');
                             });
                         })
				->where('labs.status', 1)
						->orderBy('labs.id','DESC')->get();
			$response['results']= $query;
			$response['image_base_path'] =  url("/upload/labs")."/";*/
			
		return $this->sendResponse($response,"",200);
	}
	
	public function labDetail(Request $request)
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
		$affectedRows = Labs::where('id', $id)->increment('views_count');
		
		$results =$this->labsRepo->getLab($id);
		if($results){
			$images_arr = LabsImages::where('lab_id',$results->id)->get();
			
			$response = array('results'=>$results,'module_images' =>$images_arr);
			$response['image_base_path'] =  url("/upload/labs")."/";
			
			return $this->sendResponse($response,trans('messages.records_found'));
        }else{
            return  $this->sendError([],trans('messages.records_not_found'),404); 
        }
    }

}