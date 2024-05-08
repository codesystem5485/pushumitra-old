<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\Breeder;
use App\Models\Species;
use Spatie\Permission\Models\Permission;
use App\Repositories\Interfaces\Breeder\BreederRepositoryInterface;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use DB;
use Validator;
use App\Traits\FileUpload;
use App\Models\BreederImages;
use App\Models\Payments;
use Carbon\Carbon;

class BreederController extends BaseController
{
    use FileUpload;
    protected $url = '';
    protected $breederRepo;
	
    /**Breeder Construct 
     * @return url 
     */
    public function __construct(BreederRepositoryInterface $breederRepo, UserRepositoryInterface $userRepository){
		$this->breederRepo = $breederRepo;
		$this->userRepo = $userRepository;
    }

    public function addBreeder(Request $request){
        
		$postData = request()->all();
		/*$validator = Validator::make($postData, [
				'breeder_name' => 'required',
				//'species' => 'required',
				//'firm_registration_number' => 'required',
				'mobile_number' => "required|numeric|digits:10",
				'animal_breed' => 'required',
				//'animal_description' => 'required',
				'age' => 'required|numeric',
				'vaccination_done' => 'required',
				'expected_price' => 'required',
				'address' => 'required',
				'state' => 'required',
				'state_id'=>'required',
				'city_town'=>'required',
				'taluka'=>'nullable|string',
				'district'=>'nullable|string',
				'pincode'=>'required|numeric|digits:6',
				//'pm_code'=>'required',
			]);
			
		if ($validator->fails())
		{
			return $this->sendError([],implode(',',$validator->errors()->all()),400);
		}*/
		
        $response = [];
		
        DB::beginTransaction();
        try{           
            $aInsertData = $request->all();
            $breeder = $this->breederRepo->create($aInsertData);
			
			if($request->animal_photo)
            {
                foreach($request->animal_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'breederanimals');
                    if($fileName)
                    {
                        $animalImage = BreederImages::create(['breeder_id'=>$breeder->id,'image_name' => $fileName]);
                    }
                } 
            }
			
			$payment = Payments::find($postData['payment_id']);
			$payment->module_type_id = $breeder->id;
			$payment->save();
			
			$coordinateArr = $this->userRepo->getLatitudeLongitudes($breeder);
			$paymentArr = array( 'type'=>$payment->type,'breeder_id'=>$breeder->id);
			$subscriptionArr = $this->breederRepo->getSubscriptionDates($paymentArr);
			
			$breeder->latitude=$coordinateArr['latitude'];
			$breeder->longitude=$coordinateArr['longitude'];
			$breeder->subscriptionStartDate=$subscriptionArr['subscriptionStartDate'];
			$breeder->subscriptionEndDate=$subscriptionArr['subscriptionEndDate'];
			
			$breeder->update();
			
			// Add payment notifications
			//add to notifications
			
			
			$link = url("/invoice/download/".$aInsertData['user_id'].'/'.$postData['payment_id']);
			
			$aInsertData['sender_user_id'] = $aInsertData['user_id'];
			$aInsertData['rx_reminder_id'] = 0;
			$aInsertData['type'] = 2;
			$aInsertData['link'] = $link;
			$aInsertData['scheduled_date'] = date("Y-m-d");
			$aInsertData['scheduled_message'] ="Thank you.Your payment has been confirmed.Please download your bill receipt.";
			$aInsertData['title'] = "Payment Receipt for Breeder";
			$notifications = $this->breederRepo->addPaymentToNotifications($aInsertData);
			
            
            DB::commit();
			 ## Store log
            $message = trans('messages.breeder_create',['name' => $breeder->breeder_name]);
            storeActicityLog(trans('messages.breeder_create'),$message);
			return $this->sendResponse($response,$message,200);
        }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,$request->user_id);
			return  $this->sendError($response,trans('messages.something'),500);			
        }
    }
	
	 public function updateBreeder(Request $request){
        
		$postData = request()->all();
		$response = [];
		
        DB::beginTransaction();
        try{           
            $aInsertData = $request->all();
			
			if(isset($aInsertData['user_code']))
			{
				unset($aInsertData['user_code']);
			}
			
            $breeder = $this->breederRepo->update($postData['edit_id'],$aInsertData);
			$existing_arr = [];
			if(isset($postData['existing_images'])){
				$existing_arr = $postData['existing_images'];
			}
			$animalImage = BreederImages::where('breeder_id',$breeder->id)->get();
			
			if(count($animalImage)>0)
			{
				foreach($animalImage as $image)
				{
					if(!in_array($image['image_name'],$existing_arr)){
						$this->removeFile($image->image_name,'breederanimals');
						$image->delete();
					}
				}
			}
			
			if($request->animal_photo)
            {
                foreach($request->animal_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'breederanimals');
                    if($fileName)
                    {
                        $animalImage = BreederImages::create(['breeder_id'=>$breeder->id,'image_name' => $fileName]);
                    }
                } 
            }
			
			$coordinateArr = $this->userRepo->getLatitudeLongitudes($breeder);
			$breeder->latitude=$coordinateArr['latitude'];
			$breeder->longitude=$coordinateArr['longitude'];
			$breeder->update();
			DB::commit();
			 ## Store log
            $message = trans('messages.breeder_update',['name' => $breeder->breeder_name]);
            storeActicityLog(trans('messages.breeder_update'),$message);
			return $this->sendResponse($response,$message,200);
        }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,$request->user_id);
			return  $this->sendError($response,trans('messages.something'),500);			
        }
    }
	
	public function getBreederList(Request $request)
	{
		$requestData = request()->all();
		$module_id = 0;
		if(isset($requestData['module_id']) && $requestData['module_id']!=''){
				$module_id = $requestData['module_id'];
			}
			
		$haversine = $this->userRepo->getDistanceUsingLatLong($requestData);
		$query  = Breeder::leftJoin('species', 'species.id', '=', 'breeders.species')
				->select('breeders.user_code','breeders.id','breeders.breeder_name','breeders.animal_breed','breeders.age','breeders.expected_price','breeders.mobile_number','breeders.latitude','breeders.longitude',
				'species.specie as species_name',DB::raw('(select image_name from  breeder_images where breeder_id  = breeders.id order by id asc limit 1) as image_name'),
			DB::raw('(select AVG(star_ratings) from review_ratings where rateable_id = breeders.id AND module_id ='.$module_id.' ) as star_rating_count'))
			;
           
		  if(isset($requestData['search_input']) && $requestData['search_input']!=''){
			  $words = preg_split("/[\s,]+/", $requestData['search_input'], -1);
			  $query  =$query->where(function($query) use($words){
				foreach($words as $word) {
					$query->where(function($q) use($word){
						$q->where('breeder_name', 'LIKE', '%'.$word.'%')
						 ->orWhere('mobile_number', 'LIKE', '%'.$word.'%')
						    ->orWhere('animal_breed', 'LIKE', '%'.$word.'%')
							 ->orWhere('species.specie', 'LIKE', '%'.$word.'%')
							 ->orWhere('expected_price', 'LIKE', '%'.$word.'%')
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
		  $query  = $query->where('breeders.status', 1)->whereDate('breeders.subscriptionEndDate', '>=', Carbon::now());
		    
		   if($haversine!=''){
			$query  = $query->orderby("distance", "ASC");
		  }else{
			 $query  = $query->orderby("breeders.id", "DESC"); 
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
		  $response['image_base_path'] =  url("/upload/breederanimals")."/";
		 /* $response['results']  =   Breeder::leftJoin('species', 'species.id', '=', 'breeders.species')
				->select( 'breeders.*','species.specie as species_name',
            DB::raw('(select image_name from  breeder_images where breeder_id  = breeders.id order by id asc limit 1) as image_name'))
           ->whereDate('breeders.subscriptionEndDate', '>=', Carbon::now())
		   ->orderBy('breeders.id','ASC')->get();
		   $response['image_base_path'] =  url("/upload/breederanimals")."/";*/
			
		return $this->sendResponse($response,"",200);
	}
	
	public function breederDetail(Request $request)
	{
		$postData = request()->all();
		$id = $request->breeder_id;
		$affectedRows = Breeder::where('id', $id)->increment('views_count');
		$module_id = 0;
		if(isset($postData['module_id']) && $postData['module_id']!=''){
				$module_id = $postData['module_id'];
			}
			
		$breeder = Breeder::leftJoin('species', 'species.id', '=', 'breeders.species')
		->select('breeders.*','species.specie as species_name')
		->where('breeders.id',$id)
		->first();
		$response = [];
		$breederimages=array();
		
		if($breeder){
			$breederimages = BreederImages::where('breeder_id',$breeder->id)->get();
			
			$ratings = $this->userRepo->getRatingUsingModuleId($id,$module_id,$postData);
			$breeder['star_rating_count'] = $ratings['star_rating_count'];
			$breeder['review_exist'] = $ratings['review_exist'];
			$response = array('results'=>$breeder,'module_images' =>$breederimages);
			$response['image_base_path'] =  url("/upload/breederanimals")."/";
			
			return $this->sendResponse($response,trans('messages.records_found'));
        }else{
            return  $this->sendError([],trans('messages.records_not_found'),404); 
        }
    }
	
	public function deleteBreeder(Request $request){
		$postData = request()->all();		
		$validator = Validator::make($postData, [
			'id' => 'required',
		]);
			
		if ($validator->fails())
		{
			return $this->sendError([],implode(',',$validator->errors()->all()),400);
		}
	
		$id = $request->id;
        $result = Breeder::where('id',$id)->first();
		$name = '';
		if($result){
			$name = $result->breeder_name;
			$images = BreederImages::where('breeder_id',$result->id)->get();
			
			if(count($images)>0)
			{
				foreach($images as $image)
				{
					$this->removeFile($image->image_name,'breederanimals');
					$image->delete();
				}
			}
			$arr = array('status'=>0);
			$breeder = $this->breederRepo->update($id,$arr);
		}
		
        $response=[];
        ## Store log
        $message = trans('messages.breeder_delete',['name' => $name]);
        storeActicityLog(trans('messages.breeder_delete'),$message,$postData['user_id'],$result);
		return $this->sendResponse($response,$message,200);
    }
}
