<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\Shops;
use App\Models\ShopImages;
use App\Models\State;
use App\Repositories\Interfaces\Shops\ShopsRepositoryInterface;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use DB;
use App\Traits\FileUpload;
use Validator;
use App\Models\Payments;
use Carbon\Carbon;

class ShopsController extends BaseController
{
    use FileUpload;
    protected $shopsRepo;
	private $userRepo;
	
    /**
     * Shop Construct 
     * @return url 
     */
    public function __construct(ShopsRepositoryInterface $shopsRepo, UserRepositoryInterface $userRepository){

        $this->shopsRepo = $shopsRepo;
		$this->userRepo = $userRepository;
    } 

    /**
     * Shop Add
   
     */
	 public function addShop(Request $request){
        
		$postData = request()->all();
		/*$validator = Validator::make($postData, [
				'shop_name' => 'required',
				//'shop_owner_name' => 'required',
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
		}*/
		
        $response = [];
		
        DB::beginTransaction();
        try{            
            $aInsertData = $request->all();
            $results = $this->shopsRepo->create($aInsertData);
			$category = $results->sub_category;
			
			$categoryName = $this->userRepo->getCategoryName($category);

            if($request->shop_photo)
            {
                foreach($request->shop_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'shops');
                    if($fileName)
                    {
                        ShopImages::create(['shop_id'=>$results->id,'image_name' => $fileName]);
                    }
                }
            }
			
			$subscriptionStartDate='';
			$subscriptionEndDate='';
			
			if(isset($postData['payment_id']) && ($postData['payment_id']!='' || $postData['payment_id']!=0))
			{
				$payment = Payments::find($postData['payment_id']);
				$payment->module_type_id = $results->id;
				$payment->save();
				
				$paymentArr = array( 'type'=>$payment->type,'shop_id'=>$results->id);
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
            $message = trans('messages.shop_create',['name' => $request->shop_name]);
            storeActicityLog(trans('messages.shop_create'),$message);
			return $this->sendResponse($response,$message,200);
      }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,$request->user_id);
			return  $this->sendError($response,trans('messages.something'),500);			
        }
    }
	
	public function getShopsList(Request $request)
	{
		$requestData = request()->all();
		$module_id = 0;
		if(isset($requestData['module_id']) && $requestData['module_id']!=''){
				$module_id = $requestData['module_id'];
			}
	
		$haversine = $this->userRepo->getDistanceUsingLatLong($requestData);
		$query  =  Shops::leftJoin('subcategories', 'subcategories.id', '=', 'shops.sub_category')
			->select('shops.id','shop_name','shop_owner_name','mobile_number',
		'taluka','address','city_town','district','state','pincode','latitude','longitude','subcategories.name as subcategory_name',
            DB::raw('(select image_name from  shop_images where shop_id  = shops.id order by id asc limit 1) as image_name'),
			DB::raw('(select AVG(star_ratings) from review_ratings where 
rateable_id  =   shops.id AND module_id ='.$module_id.' ) as star_rating_count'))
			->whereDate('shops.subscriptionEndDate', '>=', Carbon::now())
			->where('shops.status', 1);
						 
		   if(isset($requestData['search_input']) && $requestData['search_input']!=''){
			  $words = preg_split("/[\s,]+/", $requestData['search_input'], -1);
			  $query  =$query->where(function($query) use($words){
				foreach($words as $word) {
					$query->where(function($q) use($word){
						$q->where('shop_name', 'LIKE', '%'.$word.'%')
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
		  
		 
		  if($haversine!=''){
			$query  = $query->orderby("distance", "ASC");
		  }else{
			 $query  = $query->orderBy('shops.id','DESC');
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
		  $response['image_base_path'] =  url("/upload/shops")."/";
		  
		/*$response['results']  = Shops::leftJoin('subcategories', 'subcategories.id', '=', 'shops.sub_category')
			->select('shops.id','shop_name','shop_owner_name','mobile_number',
		'taluka','address','city_town','district','state','pincode','latitude','longitude','subcategories.name as subcategory_name',
            DB::raw('(select image_name from  shop_images where shop_id  = shops.id order by id asc limit 1) as image_name'))
			->whereDate('shops.subscriptionEndDate', '>=', Carbon::now())
			->where('shops.status', 1)
		    ->orderBy('shops.id','DESC')->get();
		   $response['image_base_path'] =  url("/upload/shops")."/";*/
			
		return $this->sendResponse($response,"",200);
	}
	
	public function shopDetail(Request $request)
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
		$affectedRows = Shops::where('id', $id)->increment('views_count');
		$module_id = 0;
		if(isset($postData['module_id']) && $postData['module_id']!=''){
				$module_id = $postData['module_id'];
			}
		
		$results =$this->shopsRepo->getShop($id);
		if($results){
			$images_arr = ShopImages::where('shop_id',$results->id)->get();
			
			$response = array('results'=>$results,'module_images' =>$images_arr);
			$ratings = $this->userRepo->getRatingUsingModuleId($id,$module_id);
			$response['star_rating_count'] = $ratings['star_rating_count'];
			$response['review_exist'] = $ratings['review_exist'];
			$response['image_base_path'] =  url("/upload/shops")."/";
			
			return $this->sendResponse($response,trans('messages.records_found'));
        }else{
            return  $this->sendError([],trans('messages.records_not_found'),404); 
        }
    }

}