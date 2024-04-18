<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\Institutions;
use App\Models\InstitutionImages;
use App\Models\State;
use App\Repositories\Interfaces\Institutions\InstitutionsRepositoryInterface;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use DB;
use App\Traits\FileUpload;
use Validator;
use App\Models\Payments;
use Carbon\Carbon;

class InstitutionsController extends BaseController
{
    use FileUpload;
    protected $institutionsRepo;
	private $userRepo;
	
    /**
     * Transporter Construct 
     * @return url 
     */
    public function __construct(InstitutionsRepositoryInterface $institutionsRepo, UserRepositoryInterface $userRepository){

        $this->institutionsRepo = $institutionsRepo;
		$this->userRepo = $userRepository;
    } 

    /**
     * Institution Add
    */
	 public function addInstitution(Request $request){
        
		$postData = request()->all();
		/*$validator = Validator::make($postData, [
				'institution_name' => 'required',
				'incharge_name' => 'required',
				'sub_category'=>'required',
				'mobile_number' => "required|numeric",
				'type' => "required",
				'address' => 'required|string',
				'state' => 'required|string',
				'city_town' => 'required|string',
				'pincode' => 'required|numeric|digits:6',
				'state_id' => 'required',
				'user_code'=>'required',
				'registration_number'=>'required',
				//'payment_id'=>'required',
			]);
			
		if ($validator->fails())
		{
			return $this->sendError([],implode(',',$validator->errors()->all()),400);
		}*/
		
        $response = [];
		
        DB::beginTransaction();
        try{            
            $aInsertData = $request->all();
            $results = $this->institutionsRepo->create($aInsertData);
			$category = $results->sub_category;
			
			$categoryName = $this->userRepo->getCategoryName($category);

            if($request->institution_photo)
            {
                foreach($request->institution_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'institutions');
                    if($fileName)
                    {
                        InstitutionImages::create(['institution_id'=>$results->id,'image_name' => $fileName]);
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
				
				$paymentArr = array( 'type'=>$payment->type,'institution_id'=>$results->id);
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
			
			$coordinateArr = $this->userRepo->getLatitudeLongitudes($results);
			$results->latitude=$coordinateArr['latitude'];
			$results->longitude=$coordinateArr['longitude'];
			$results->subscriptionStartDate=$subscriptionStartDate;
			$results->subscriptionEndDate=$subscriptionEndDate;
			$results->update();
			
			 
            DB::commit();
			## Store log
            $message = trans('messages.institution_create',['name' => $request->institution_name]);
            storeActicityLog(trans('messages.institution_create'),$message);
			return $this->sendResponse($response,$message,200);
      }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,$request->user_id);
			return  $this->sendError($response,trans('messages.something'),500);			
        }
    }
	
	public function updateInstitution(Request $request){
        
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
            $results = $this->institutionsRepo->update($postData['edit_id'],$aInsertData);
			$existing_arr = [];
			if(isset($postData['existing_images'])){
				$existing_arr = $postData['existing_images'];
			}
			$images = InstitutionImages::where('institution_id',$results->id)->get();
			if(count($images)>0)
			{
				foreach($images as $image)
				{
					if(!in_array($image['image_name'],$existing_arr)){
						$this->removeFile($image->image_name,'institutions');
						$image->delete();
					}
				}
			}
            if($request->institution_photo)
            {
                foreach($request->institution_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'institutions');
                    if($fileName)
                    {
                        InstitutionImages::create(['institution_id'=>$results->id,'image_name' => $fileName]);
                    }
                }
            }
			
			$coordinateArr = $this->userRepo->getLatitudeLongitudes($results);
			$results->latitude=$coordinateArr['latitude'];
			$results->longitude=$coordinateArr['longitude'];
			$results->update();
			 
            DB::commit();
			## Store log
            $message = trans('messages.institution_update',['name' => $request->institution_name]);
            storeActicityLog(trans('messages.institution_update'),$message);
			return $this->sendResponse($response,$message,200);
      }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,$request->user_id);
			return  $this->sendError($response,trans('messages.something'),500);			
        }
    }
	
	public function getInstitutionList(Request $request)
	{
		$requestData = request()->all();
		$module_id = 0;
		if(isset($requestData['module_id']) && $requestData['module_id']!=''){
				$module_id = $requestData['module_id'];
			}
		$haversine = $this->userRepo->getDistanceUsingLatLong($requestData);
		$query  = Institutions::leftJoin('subcategories', 'subcategories.id', '=', 'institutions.sub_category')
								->select('institutions.id','user_code','institution_name','incharge_name','mobile_number','type',
		'registration_number','taluka','address','city_town','district','state','pincode','latitude','longitude','subcategories.name as subcategory_name',
            DB::raw('(select image_name from  institutions_images where institution_id  = institutions.id order by id asc limit 1) as image_name'),
			DB::raw('(select AVG(star_ratings) from review_ratings where 
rateable_id  = institutions.id AND module_id ='.$module_id.' ) as star_rating_count'))
			->where('institutions.status', 1)
			->where(function($query){
                            $query->where(function($query){
                                 $query->where('type','Private')->whereDate('institutions.subscriptionEndDate', '>=', Carbon::now());
                             })
							 ->orWhere(function($query){
                                 $query->where('type','Government')->where('institutions.subscriptionEndDate', '0000-00-00');
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
						$q->where('institution_name', 'LIKE', '%'.$word.'%')
						 ->orWhere('subcategories.name', 'LIKE', '%'.$word.'%')
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
			 $query  = $query->orderby("institutions.id", "DESC"); 
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
		  $response['image_base_path'] =  url("/upload/institutions")."/";
		  
	/*	$response['results']  =   Institutions::leftJoin('subcategories', 'subcategories.id', '=', 'institutions.sub_category')
								->select('institutions.id','institution_name','incharge_name','mobile_number','type',
		'registration_number','taluka','address','city_town','district','state','pincode','latitude','longitude','subcategories.name as subcategory_name',
            DB::raw('(select image_name from  institutions_images where institution_id  = institutions.id order by id asc limit 1) as image_name'))
			->where(function($query){
                            $query->where(function($query){
                                 $query->where('type','Private')->whereDate('institutions.subscriptionEndDate', '>=', Carbon::now());
                             })
							 ->orWhere(function($query){
                                 $query->where('type','Government')->where('institutions.subscriptionEndDate', '0000-00-00');
                             });
                         })
						 
			->where('institutions.status', 1)
		    ->orderBy('institutions.id','DESC')->get();
		   $response['image_base_path'] =  url("/upload/institutions")."/";*/
			
		return $this->sendResponse($response,"",200);
	}
	
	public function institutionDetail(Request $request)
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
		$affectedRows = Institutions::where('id', $id)->increment('views_count');
		$module_id = 0;
		if(isset($postData['module_id']) && $postData['module_id']!=''){
				$module_id = $postData['module_id'];
			}
		
		$results =$this->institutionsRepo->getInstitution($id);
		if($results){
			$images_arr = InstitutionImages::where('institution_id',$results->id)->get();
			
			$ratings = $this->userRepo->getRatingUsingModuleId($id,$module_id,$postData);
			$results['star_rating_count'] = $ratings['star_rating_count'];
			$results['review_exist'] = $ratings['review_exist'];
			$response = array('results'=>$results,'module_images' =>$images_arr);
			$response['image_base_path'] =  url("/upload/institutions")."/";
			
			return $this->sendResponse($response,trans('messages.records_found'));
        }else{
            return  $this->sendError([],trans('messages.records_not_found'),404); 
        }
    }

}