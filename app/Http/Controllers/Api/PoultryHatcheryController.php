<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\PoultryHatchery;
use App\Models\PoultryHatcheryImages;
use App\Models\State;
use App\Repositories\Interfaces\Poultryhatchery\PoultryhatcheryRepositoryInterface;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use DB;
use App\Traits\FileUpload;
use Validator;
use App\Models\Payments;
use Carbon\Carbon;

class PoultryHatcheryController extends BaseController
{
    use FileUpload;
    protected $poultryhatcheryRepo;
	private $userRepo;
	
    /**
     * Transporter Construct 
     * @return url 
     */
    public function __construct(PoultryhatcheryRepositoryInterface $poultryhatcheryRepo, UserRepositoryInterface $userRepository){

        $this->poultryhatcheryRepo = $poultryhatcheryRepo;
		$this->userRepo = $userRepository;
    } 

    /**
     * poultryhatchery Add
   
     */
	 public function addPoultryhatchery(Request $request){
        
		$postData = request()->all();
		/*$validator = Validator::make($postData, [
				'poultryhatchery_center_name' => 'required',
				//'incharge_name' => 'required',
				//'mobile_number' => "required|numeric",
				'type' => "required",
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
            $results = $this->poultryhatcheryRepo->create($aInsertData);
			$category = $results->sub_category;
			

            if($request->poultryhatchery_photo)
            {
                foreach($request->poultryhatchery_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'poultryhatchery');
                    if($fileName)
                    {
                        PoultryHatcheryImages::create(['poultryhatchery_center_id'=>$results->id,'image_name' => $fileName]);
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
				
				$paymentArr = array( 'type'=>$payment->type,'poultry_id'=>$results->id);
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
				$aInsertData['title'] = "Payment Receipt for Poultry Hatchery";
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
            $message = trans('messages.poultryhatchery_create',['name' => $request->poultryhatchery_center_name]);
            storeActicityLog(trans('messages.poultryhatchery_create'),$message);
			return $this->sendResponse($response,$message,200);
      }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,$request->user_id);
			return  $this->sendError($response,trans('messages.something'),500);			
        }
    }
	
	public function updatePoultryhatchery(Request $request){
        
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
            $results = $this->poultryhatcheryRepo->update($postData['edit_id'],$aInsertData);
			/*$existing_arr = [];
			if(isset($postData['existing_images'])){
				$existing_arr = $postData['existing_images'];
			}
			$images = PoultryHatcheryImages::where('poultryhatchery_center_id',$results->id)->get();
			if(count($images)>0)
			{
				foreach($images as $image)
				{
					if(!in_array($image['image_name'],$existing_arr)){
						$this->removeFile($image->image_name,'poultryhatchery');
						$image->delete();
					}
				}
			}*/

            if($request->poultryhatchery_photo)
            {
                foreach($request->poultryhatchery_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'poultryhatchery');
                    if($fileName)
                    {
                        PoultryHatcheryImages::create(['poultryhatchery_center_id'=>$results->id,'image_name' => $fileName]);
                    }
                }
            }
			
			$coordinateArr = $this->userRepo->getLatitudeLongitudes($results);
			$results->latitude=$coordinateArr['latitude'];
			$results->longitude=$coordinateArr['longitude'];
			$results->update();
			
			
            DB::commit();
			## Store log
            $message = trans('messages.poultryhatchery_update',['name' => $request->poultryhatchery_center_name]);
            storeActicityLog(trans('messages.poultryhatchery_update'),$message);
			return $this->sendResponse($response,$message,200);
      }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,$request->user_id);
			return  $this->sendError($response,trans('messages.something'),500);			
        }
    }
	
	public function getPoultryHatcheryList(Request $request)
	{
		$requestData = request()->all();
		\Log::info('Poultry Hatchery List Request', $requestData);
		$module_id = 0;
		if(isset($requestData['module_id']) && $requestData['module_id']!=''){
				$module_id = $requestData['module_id'];
			}
		$haversine = $this->userRepo->getDistanceUsingLatLong($requestData);
		$query  = PoultryHatchery::select('id','user_code','poultryhatchery_center_name','incharge_name','mobile_number','type',
		'taluka','address','city_town','district','state','pincode','latitude','longitude',
            DB::raw('(select image_name from  poultryhatchery_center_images where poultryhatchery_center_id  = poultryhatchery_centers.id order by id asc limit 1) as image_name'),
			DB::raw('(select AVG(star_ratings) from review_ratings where 
rateable_id  =   poultryhatchery_centers.id AND module_id ='.$module_id.' ) as star_rating_count'))
			->where(function($query){
                            $query->where(function($query){
                                 $query->where('type','Private')->whereDate('poultryhatchery_centers.subscriptionEndDate', '>=', Carbon::now());
                             })
							 ->orWhere(function($query){
                                 $query->where('type','Government')->where('poultryhatchery_centers.subscriptionEndDate', '0000-00-00');
                             });
                         })
				->where('poultryhatchery_centers.status', 1);
						 
		if($haversine!=''){
			$query  = $query->selectRaw("$haversine AS distance");
		  }
		  
		  if(isset($requestData['search_input']) && $requestData['search_input']!=''){
			  $words = preg_split("/[\s,]+/", $requestData['search_input'], -1);
			  $query  =$query->where(function($query) use($words){
				foreach($words as $word) {
					$query->where(function($q) use($word){
						$q->where('poultryhatchery_center_name', 'LIKE', '%'.$word.'%')
						->orWhere('breed', 'LIKE', '%'.$word.'%')
						->orWhere('city_town', 'LIKE', '%'.$word.'%')
							 ->orWhere('taluka', 'LIKE', '%'.$word.'%')
							 ->orWhere('district', 'LIKE', '%'.$word.'%')
							  ->orWhere('type', 'LIKE', '%'.$word.'%')
							 ->orWhere('pincode', 'LIKE', '%'.$word.'%');
					});
				}
			});
		  }
		  
		   if($haversine!=''){
			$query  = $query->orderby("distance", "ASC");
		  }else{
			 $query  = $query->orderby("poultryhatchery_centers.id", "DESC"); 
		  }
		  
		   $response['total_count'] = $query->count();
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
		  
		  $response['results'] =$query;
		  $response['image_base_path'] =  url("/upload/poultryhatchery")."/";
		  
		/*$query = PoultryHatchery::select('id','poultryhatchery_center_name','incharge_name','mobile_number','type',
		'taluka','address','city_town','district','state','pincode','latitude','longitude',
            DB::raw('(select image_name from  poultryhatchery_center_images where poultryhatchery_center_id  = poultryhatchery_centers.id order by id asc limit 1) as image_name'))
			->where(function($query){
                            $query->where(function($query){
                                 $query->where('type','Private')->whereDate('poultryhatchery_centers.subscriptionEndDate', '>=', Carbon::now());
                             })
							 ->orWhere(function($query){
                                 $query->where('type','Government')->where('poultryhatchery_centers.subscriptionEndDate', '0000-00-00');
                             });
                         })
				->where('poultryhatchery_centers.status', 1)
						->orderBy('poultryhatchery_centers.id','DESC')->get();
			$response['results']= $query;
			$response['image_base_path'] =  url("/upload/poultryhatchery")."/";*/
			
		return $this->sendResponse($response,"",200);
	}
	
	public function poultryHatcheryDetail(Request $request)
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
		if(isset($requestData['module_id']) && $requestData['module_id']!=''){
				$module_id = $requestData['module_id'];
			}
		$affectedRows = PoultryHatchery::where('id', $id)->increment('views_count');
		$results =$this->poultryhatcheryRepo->getPoultryHatchery($id);
		if($results){
			$images_arr = PoultryHatcheryImages::where('poultryhatchery_center_id',$results->id)->get();
			$ratings = $this->userRepo->getRatingUsingModuleId($id,$module_id,$postData);
			$results['star_rating_count'] = $ratings['star_rating_count'];
			$results['review_exist'] = $ratings['review_exist'];
			$response = array('results'=>$results,'module_images' =>$images_arr);
			$response['image_base_path'] =  url("/upload/poultryhatchery")."/";
			
			return $this->sendResponse($response,trans('messages.records_found'));
        }else{
            return  $this->sendError([],trans('messages.records_not_found'),404); 
        }
    }
	
	public function deletePoultryhatchery(Request $request){
		$postData = request()->all();		
		$validator = Validator::make($postData, [
			'id' => 'required',
		]);
			
		if ($validator->fails())
		{
			return $this->sendError([],implode(',',$validator->errors()->all()),400);
		}
	
		$id = $request->id;
        $result = PoultryHatchery::where('id',$id)->first();
		$name = '';
		if($result){
			$name = $result->poultryhatchery_center_name;
			$images = PoultryHatcheryImages::where('poultryhatchery_center_id',$result->id)->get();
			if(count($images)>0)
			{
				foreach($images as $image)
				{
					$this->removeFile($image->image_name,'poultryhatchery');
					$image->delete();
				}
			}
			$arr = array('status'=>0);
			$poultryhatchery = $this->poultryhatcheryRepo->update($id,$arr);
		}
		
        $response=[];
        ## Store log
        $message = trans('messages.poultryhatchery_delete',['name' => $name]);
        storeActicityLog(trans('messages.poultryhatchery_delete'),$message,$postData['user_id'],$result);
		return $this->sendResponse($response,$message,200);
    }

}