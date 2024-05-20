<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\Veterinaryhospitals;
use App\Models\VeterinaryhospitalsImages;
use App\Models\State;
use App\Repositories\Interfaces\Vethospitals\VethospitalsRepositoryInterface;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use DB;
use App\Traits\FileUpload;
use Validator;
use App\Models\Payments;
use Carbon\Carbon;

class VetHospitalsController extends BaseController
{
    use FileUpload;
    protected $url = '';
    protected $vethospitalsRepo;
	private $userRepo;
	
    /**
     * VetHospitals Construct 
     * @return url 
     */
    public function __construct(VethospitalsRepositoryInterface $vethospitalsRepo, UserRepositoryInterface $userRepository){

        $this->vethospitalsRepo = $vethospitalsRepo;
		$this->userRepo = $userRepository;
    } 

    /**
     * VetHospitals Add
   
     */
	 public function addHospital(Request $request){
        
		$postData = request()->all();
		/*$validator = Validator::make($postData, [
				'hospital_name' => 'required',
				//'veterinary_owner_name' => 'required',
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
            $hospitals = $this->vethospitalsRepo->create($aInsertData);
			$category = $hospitals->sub_category;
			
			$categoryName = $this->userRepo->getCategoryName($category);

            if($request->hospitals_photo)
            {
                foreach($request->hospitals_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'hospitals');
                    if($fileName)
                    {
                        VeterinaryhospitalsImages::create(['veterinary_hospitals_id'=>$hospitals->id,'image_name' => $fileName]);
                    }
                }
            }
			$subscriptionStartDate='';
			$subscriptionEndDate='';
			
			if(isset($postData['payment_id']) && $postData['payment_id']!='' && $postData['payment_id']!=0)
			{
			
				$payment = Payments::find($postData['payment_id']);
				$payment->module_type_id = $hospitals->id;
				$payment->save();
				
				
				$paymentArr = array( 'type'=>$payment->type,'hospitals_id'=>$hospitals->id);
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
			
			$coordinateArr = $this->userRepo->getLatitudeLongitudes($hospitals);
			$hospitals->latitude=$coordinateArr['latitude'];
			$hospitals->longitude=$coordinateArr['longitude'];
			$hospitals->subscriptionStartDate=$subscriptionStartDate;
			$hospitals->subscriptionEndDate=$subscriptionEndDate;
			$hospitals->update();
			
			
            DB::commit();
			## Store log
            $message = trans('messages.hospital_create',['name' => $request->hospital_name]);
            storeActicityLog(trans('messages.hospital_create'),$message);
			return $this->sendResponse($response,$message,200);
      }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,$request->user_id);
			return  $this->sendError($response,trans('messages.something'),500);			
        }
    }
	
	public function updateHospital(Request $request){
        $postData = request()->all();
		$validator = Validator::make($postData, [
				'edit_id' => 'required',
			]);
			
		if($validator->fails())
		{
			return $this->sendError([],implode(',',$validator->errors()->all()),400);
		}
		$response = [];
		DB::beginTransaction();
        try{            
            $aInsertData = $request->all();
			if(isset($aInsertData['user_code']))
			{
				unset($aInsertData['user_code']);
			}
            $hospitals = $this->vethospitalsRepo->update($postData['edit_id'],$aInsertData);
			
		/*	$existing_arr = [];
			if(isset($postData['existing_images'])){
				$existing_arr = $postData['existing_images'];
			}
			$images = VeterinaryhospitalsImages::where('veterinary_hospitals_id',$hospitals->id)->get();
			
			if(count($images)>0)
			{
				foreach($images as $image)
				{
					if(!in_array($image['image_name'],$existing_arr)){
						$this->removeFile($image->image_name,'hospitals');
						$image->delete();
						 
					}
				}
			}*/
			
            if($request->hospitals_photo)
            {
                foreach($request->hospitals_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'hospitals');
                    if($fileName)
                    {
                        VeterinaryhospitalsImages::create(['veterinary_hospitals_id'=>$hospitals->id,'image_name' => $fileName]);
                    }
                }
            }
			
			$coordinateArr = $this->userRepo->getLatitudeLongitudes($hospitals);
			$hospitals->latitude=$coordinateArr['latitude'];
			$hospitals->longitude=$coordinateArr['longitude'];
			$hospitals->update();
			
			
            DB::commit();
			## Store log
            $message = trans('messages.hospital_update',['name' => $request->hospital_name]);
            storeActicityLog(trans('messages.hospital_update'),$message);
			return $this->sendResponse($response,$message,200);
      }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,$request->user_id);
			return  $this->sendError($response,trans('messages.something'),500);			
        }
    }
	

	public function getHospitalList(Request $request)
	{
		$requestData = request()->all();
		$module_id = 0;
		if(isset($requestData['module_id']) && $requestData['module_id']!=''){
				$module_id = $requestData['module_id'];
			}
		$haversine = $this->userRepo->getDistanceUsingLatLong($requestData);
		$query  =   Veterinaryhospitals::leftJoin('subcategories', 'subcategories.id', '=', 'veterinary_hospitals.sub_category')
			->select('veterinary_hospitals.*','subcategories.name as subcategory_name',
            DB::raw('(select image_name from  veterinary_hospitals_images where veterinary_hospitals_id  = veterinary_hospitals.id order by id asc limit 1) as image_name'),
			DB::raw('(select AVG(star_ratings) from review_ratings where 
rateable_id  =   veterinary_hospitals.id AND module_id ='.$module_id.' ) as star_rating_count'))
			->where(function($query){
                            $query->where(function($query){
                                 $query->where('type','Private')->whereDate('veterinary_hospitals.subscriptionEndDate', '>=', Carbon::now());
                             })
							 ->orWhere(function($query){
                                 $query->where('type','Government')->where('veterinary_hospitals.subscriptionEndDate', '0000-00-00');
                             });
                         });
						 
		   if(isset($requestData['search_input']) && $requestData['search_input']!=''){
			  $words = preg_split("/[\s,]+/", $requestData['search_input'], -1);
			  $query  =$query->where(function($query) use($words){
				foreach($words as $word) {
					$query->where(function($q) use($word){
						$q->where('hospital_name', 'LIKE', '%'.$word.'%')
						 ->orWhere('subcategories.name', 'LIKE', '%'.$word.'%')
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
		  
		  $query  = $query->where('veterinary_hospitals.status', 1);
		  
		  if($haversine!=''){
			$query  = $query->orderby("distance", "ASC");
		  }else{
			 $query  = $query->orderBy('veterinary_hospitals.id','DESC');
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
		  $response['image_base_path'] =  url("/upload/hospitals")."/";
		  
		 /* $query = Veterinaryhospitals::leftJoin('subcategories', 'subcategories.id', '=', 'veterinary_hospitals.sub_category')
			->select('veterinary_hospitals.*','subcategories.name as subcategory_name',
            DB::raw('(select image_name from  veterinary_hospitals_images where veterinary_hospitals_id  = veterinary_hospitals.id order by id asc limit 1) as image_name'))
			->where(function($query){
                            $query->where(function($query){
                                 $query->where('type','Private')->whereDate('veterinary_hospitals.subscriptionEndDate', '>=', Carbon::now());
                             })
							 ->orWhere(function($query){
                                 $query->where('type','Government')->where('veterinary_hospitals.subscriptionEndDate', '0000-00-00');
                             });
                         })
				->where('veterinary_hospitals.status', 1)
						->orderBy('veterinary_hospitals.id','DESC')->get();
			$response['results']= $query;
			$response['image_base_path'] =  url("/upload/hospitals")."/";*/
			
		return $this->sendResponse($response,"",200);
	}
	
	public function hospitalDetail(Request $request)
	{
		$postData = request()->all();
		$module_id = 0;
		if(isset($requestData['module_id']) && $requestData['module_id']!=''){
				$module_id = $requestData['module_id'];
			}
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
		$affectedRows = Veterinaryhospitals::where('id', $id)->increment('views_count');
		
		$results =$this->vethospitalsRepo->getVethospital($id);
		if($results){
			$images_arr = VeterinaryhospitalsImages::where('veterinary_hospitals_id',$results->id)->get();
			$ratings = $this->userRepo->getRatingUsingModuleId($id,$module_id,$postData);
			$results['star_rating_count'] = $ratings['star_rating_count'];
			$results['review_exist'] = $ratings['review_exist'];
			
			$response = array('results'=>$results,'module_images' =>$images_arr);
			$response['image_base_path'] =  url("/upload/hospitals")."/";
			
			return $this->sendResponse($response,trans('messages.records_found'));
        }else{
            return  $this->sendError([],trans('messages.records_not_found'),404); 
        }
    }
	
	public function deleteHospital(Request $request){
		$postData = request()->all();		
		$validator = Validator::make($postData, [
			'id' => 'required',
		]);
			
		if ($validator->fails())
		{
			return $this->sendError([],implode(',',$validator->errors()->all()),400);
		}
	
		$id = $request->id;
        $result = Veterinaryhospitals::where('id',$id)->first();
		$name = '';
		if($result){
			$name = $result->hospital_name;
			$images = VeterinaryhospitalsImages::where('veterinary_hospitals_id',$result->id)->get();
			
			if(count($images)>0)
			{
				foreach($images as $image)
				{
					$this->removeFile($image->image_name,'hospitals');
					$image->delete();
				}
			}
			$arr = array('status'=>0);
			$vethospitals = $this->vethospitalsRepo->update($id,$arr);
		}
		
        $response=[];
        ## Store log
        $message = trans('messages.hospital_delete',['name' => $name]);
        storeActicityLog(trans('messages.hospital_delete'),$message,$postData['user_id'],$result);
		return $this->sendResponse($response,$message,200);
    }

}