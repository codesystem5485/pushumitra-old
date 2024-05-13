<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\AnimalForSale;
use App\Models\AnimalImages;
use App\Models\Breeds;
use App\Models\Species; 
use App\Models\AnimalType;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\AnimalsaleProcessRequest;
use App\Repositories\Interfaces\Animalsale\AnimalsaleRepositoryInterface;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use DB;
use Validator;
use App\Models\Payments;
use App\Traits\FileUpload;
use Razorpay\Api\Api;
use Carbon\Carbon;

class AnimalsaleController extends BaseController
{
    use FileUpload;
    protected $url = '';
    protected $animalsaleRepo;
	private $userRepo;
    /**
     * Animal Sale Construct 
     * @return url 
     */
    public function __construct(AnimalsaleRepositoryInterface $animalsaleRepo, UserRepositoryInterface $userRepository){
		$this->animalsaleRepo = $animalsaleRepo;
		$this->userRepo = $userRepository;
    } 

    public function addAnimalForSale(Request $request){
        
		$postData = request()->all();
		/*$validator = Validator::make($postData, [
				//'UID_number' => 'nullable|numeric|digits:12',
				//'species' => 'required',
				'breed' => "required",
				//'type' => "required",
				'age' => 'required|numeric',
				'sex' => 'required|string',
				'price' => 'required|numeric',
				//'description' => 'required',
				'address' => 'required',
				'state' => 'required|string',
				'city_town' => 'required|string',
				//'district' => 'string',
                //'taluka' => 'string',
                'pincode' => 'required|numeric|digits:6',
				'state_id' => 'required',
				'contact_number_of_owner' => 'required|numeric|digits:10',
				'contact_name_of_owner' => 'required',
				//'pm_code'=>'required',
				'payment_id'=>'required',
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
            $animalsale = $this->animalsaleRepo->create($aInsertData);
            if($request->animal_photo)
            {
                foreach($request->animal_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'animalsale');
                    if($fileName)
                    {
                        $animalImage = AnimalImages::create(['animal_sale_id'=>$animalsale->id,'image_name' => $fileName]);
                    }
                } 
            }
			
			$paymentArr = array( 'type'=>2,'animalsale_id'=>$animalsale->id);
			$subscriptionArr = $this->animalsaleRepo->getSubscriptionDates($paymentArr);
				
			if(isset($postData['payment_id']) && $postData['payment_id']!='' && $postData['payment_id']!=0)
			{
				$payment = Payments::find($postData['payment_id']);
				$payment->module_type_id = $animalsale->id;
				$payment->save();
				
				$paymentArr = array( 'type'=>$payment->type,'animalsale_id'=>$animalsale->id);
				
				$subscriptionStartDate=$subscriptionArr['subscriptionStartDate'];
				$subscriptionEndDate=$subscriptionArr['subscriptionEndDate'];
				
				$link = url("/invoice/download/".$aInsertData['user_id'].'/'.$postData['payment_id']);
				
				$aInsertData['sender_user_id'] = $aInsertData['user_id'];
				$aInsertData['rx_reminder_id'] = 0;
				$aInsertData['type'] = 2;
				$aInsertData['link'] = $link;
				$aInsertData['scheduled_date'] = date("Y-m-d");
				$aInsertData['scheduled_message'] ="Thank you.Your payment has been confirmed.Please download your bill receipt.";
				$aInsertData['title'] = "Payment Receipt for Animal Sale";
				$notifications = $this->animalsaleRepo->addPaymentToNotifications($aInsertData);
			
            }
			
			$coordinateArr = $this->userRepo->getLatitudeLongitudes($animalsale);
			$animalsale->latitude=$coordinateArr['latitude'];
			$animalsale->longitude=$coordinateArr['longitude'];
			$animalsale->subscriptionStartDate=$subscriptionArr['subscriptionStartDate'];
			$animalsale->subscriptionEndDate=$subscriptionArr['subscriptionEndDate'];
			$animalsale->update();
			
            DB::commit();
			 ## Store log
            $message = trans('messages.animalsale_create',['name' => $request->UID_number]);
            storeActicityLog(trans('messages.animalsale_create'),$message);
			return $this->sendResponse($response,trans('messages.animalsale_create'),200);
       }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,$request->user_id);
			return  $this->sendError($response,trans('messages.something'),500);			
        }
    }
	
	public function updateAnimalForSale(Request $request){
        
		$postData = request()->all();
		$response = [];
		
       DB::beginTransaction();
      try{            
            $aInsertData = $request->all();
			if(isset($aInsertData['user_code']))
			{
				unset($aInsertData['user_code']);
			}
            $animalsale = $this->animalsaleRepo->update($postData['edit_id'],$aInsertData);
			
			$existing_arr = [];
			if(isset($postData['existing_images'])){
				$existing_arr = $postData['existing_images'];
			}
			$animalImage = AnimalImages::where('animal_sale_id',$animalsale->id)->get();
			
			if(count($animalImage)>0)
			{
				foreach($animalImage as $image)
				{
					if(!in_array($image['image_name'],$existing_arr)){
						$this->removeFile($image->image_name,'animalsale');
						$image->delete();
						 
					}
				}
			}
			
            if($request->animal_photo)
            {
                foreach($request->animal_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'animalsale');
                    if($fileName)
                    {
                        $animalImage = AnimalImages::create(['animal_sale_id'=>$animalsale->id,'image_name' => $fileName]);
                    }
                } 
            }
			
			$coordinateArr = $this->userRepo->getLatitudeLongitudes($animalsale);
			$animalsale->latitude=$coordinateArr['latitude'];
			$animalsale->longitude=$coordinateArr['longitude'];
			$animalsale->update();
            
            DB::commit();
			## Store log
            $message = trans('messages.animalsale_update',['name' => $request->UID_number]);
            storeActicityLog(trans('messages.animalsale_update'),$message);
			return $this->sendResponse($response,trans('messages.animalsale_update'),200);
       }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,$request->user_id);
			return  $this->sendError($response,trans('messages.something'),500);			
	   }
    }
	
	/*updated on 28-03-24 for rating
	updated on 28-02-24 for search*/
	public function getAnimalSaleList(Request $request)
	{
		$requestData = request()->all();
		
		$module_id = 0;
		if(isset($requestData['module_id']) && $requestData['module_id']!=''){
				$module_id = $requestData['module_id'];
			}
		
		//get distance
		$haversine = $this->userRepo->getDistanceUsingLatLong($requestData);
		
		$query  = Animalforsale::leftJoin('species', 'species.id', '=', 'animal_for_sales.species')
		->select('animal_for_sales.*','species.specie as species_name',
		DB::raw('(select image_name from animal_images where animal_sale_id  =   animal_for_sales.id order by id asc limit 1) as image_name'),
			DB::raw('(select AVG(star_ratings) from review_ratings where 
rateable_id  =   animal_for_sales.id AND module_id ='.$module_id.' ) as star_rating_count'));
	  
		  if(isset($requestData['search_input']) && $requestData['search_input']!=''){
			  $words = preg_split("/[\s,]+/", $requestData['search_input'], -1);
			  $query  =$query->where(function($query) use($words){
				foreach($words as $word) {
					$query->where(function($q) use($word){
						$q->where('breed', 'LIKE', '%'.$word.'%')
						->orWhere('sex', 'LIKE', '%'.$word.'%')
						    ->orWhere('UID_number', 'LIKE', '%'.$word.'%')
							 ->orWhere('price', 'LIKE', '%'.$word.'%')
							 ->orWhere('species.specie', 'LIKE', '%'.$word.'%')
							   //->orWhere('contact_number_of_owner', 'LIKE', '%'.$word.'%')
							   ->orWhere('address', 'LIKE', '%'.$word.'%')
							  // ->orWhere('state', 'LIKE', '%'.$word.'%')
							    ->orWhere('city_town', 'LIKE', '%'.$word.'%')
								 ->orWhere('taluka', 'LIKE', '%'.$word.'%')
								  ->orWhere('district', 'LIKE', '%'.$word.'%')
								  ->orWhere('pincode', 'LIKE', '%'.$word.'%');
								  //->orWhere('email_id', 'LIKE', '%'.$word.'%');
					});
				}
			});
		  } 
		  if($haversine!=''){
			$query  = $query->selectRaw("$haversine AS distance");
		  }
		  $query  = $query->where('animal_for_sales.status', 1)->whereDate('animal_for_sales.subscriptionEndDate', '>=', Carbon::now());
		    
		   if($haversine!=''){
			$query  = $query->orderby("distance", "ASC");
		  }else{
			 $query  = $query->orderby("id", "DESC"); 
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
		  $response['image_base_path'] =  url("/upload/animalsale")."/";
		 
		return $this->sendResponse($response,"",200);
	}
	
	public function animalSaleDetail(Request $request){
		$postData = request()->all();
		$id = $request->animalsale_id;
		$affectedRows = Animalforsale::where('id', $id)->increment('views_count');
		$module_id = 0;
		if(isset($postData['module_id']) && $postData['module_id']!=''){
				$module_id = $postData['module_id'];
			}
		
		$animalsale = Animalforsale::leftJoin('species', 'species.id', '=', 'animal_for_sales.species')
		->select('animal_for_sales.*','species.specie as species_name')
		->where('animal_for_sales.id',$id)
		->first();
		
		$animalimages=array();
		$response = [];
		if($animalsale){
			$animalimages = AnimalImages::where('animal_sale_id',$animalsale->id)->get();
		}
		
		if($animalsale){
			
			$ratings = $this->userRepo->getRatingUsingModuleId($id,$module_id,$postData);
			$animalsale['star_rating_count'] = $ratings['star_rating_count'];
			$animalsale['review_exist'] = $ratings['review_exist'];
			$response = array('results'=>$animalsale,'module_images' =>$animalimages);
			$response['image_base_path'] =  url("/upload/animalsale")."/";
			
			return $this->sendResponse($response,trans('messages.records_found'));
        }else{
            return  $this->sendError([],trans('messages.records_not_found'),404); 
        }
    }
	
	public function deleteAnimalForSale(Request $request){
		$postData = request()->all();		
		$validator = Validator::make($postData, [
			'id' => 'required',
			'delete_reason' => 'required',
			'delete_note' => 'required',
		]);
			
		if ($validator->fails())
		{
			return $this->sendError([],implode(',',$validator->errors()->all()),400);
		}
	
		$id = $request->id;
        $result = Animalforsale::where('id',$id)->first();
		$name = '';
		if($result){
			
			$images = AnimalImages::where('animal_sale_id',$result->id)->get();
			
			if(count($images)>0)
			{
				foreach($images as $image)
				{
					$this->removeFile($image->image_name,'animalsale');
					$image->delete();
				}
			}
			//$result->delete();
			
			$arr = array(
				'status'=>0,
				'delete_reason'=>$request->delete_reason,
				'delete_note'=>$request->delete_note,
			);
			$animals = $this->animalsaleRepo->update($id,$arr);
		}
		
        $response=[];
        ## Store log
        $message = trans('messages.animalsale_delete',['name' => 'Animal for sale']);
        storeActicityLog(trans('messages.animalsale_delete'),$message,$postData['user_id'],$result);
		return $this->sendResponse($response,$message,200);
    }


}
