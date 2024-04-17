<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\ProductForSale;
use App\Models\ProductImages;
use App\Http\Requests\ProductsaleProcessRequest;
use App\Repositories\Interfaces\Productsale\ProductsaleRepositoryInterface;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use DB;
use App\Traits\FileUpload;
use Validator;
use App\Models\Payments;
use Carbon\Carbon;

class ProductsaleController extends BaseController
{
    use FileUpload;
    protected $url = '';
    protected $productsaleRepo;
	private $userRepo;
    /**
     * Product Sale Construct 
     * @return url 
     */
    public function __construct(ProductsaleRepositoryInterface $productsaleRepo, UserRepositoryInterface $userRepository){

        $this->productsaleRepo = $productsaleRepo;
		$this->userRepo = $userRepository;
    } 
	
	 public function addProductSale(Request $request){
        
		$postData = request()->all();
		/*$validator = Validator::make($postData, [
				'product_name' => 'required',
				'contact_name_of_owner' => 'required',
				'contact_number_of_owner' => "required|numeric",
				'price'=>"required",
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
            $productsale = $this->productsaleRepo->create($aInsertData);

            if($request->product_photo)
            {
                foreach($request->product_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'productsale');
                    if($fileName)
                    {
                        ProductImages::create(['product_sale_id'=>$productsale->id,'image_name' => $fileName]);
                    }
                }
            }
			
			$payment = Payments::find($postData['payment_id']);
			$payment->module_type_id = $productsale->id;
			$payment->save();
			
			$coordinateArr = $this->userRepo->getLatitudeLongitudes($productsale);
			$paymentArr = array( 'type'=>$payment->type,'productsale_id'=>$productsale->id);
			$subscriptionArr = $this->userRepo->getAllSubscriptionDates($paymentArr);
			
			$productsale->latitude=$coordinateArr['latitude'];
			$productsale->longitude=$coordinateArr['longitude'];
			$productsale->subscriptionStartDate=$subscriptionArr['subscriptionStartDate'];
			$productsale->subscriptionEndDate=$subscriptionArr['subscriptionEndDate'];
			$productsale->update();
			
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
			$aInsertData['title'] = "Payment Receipt for product sale";
			$notifications = $this->userRepo->addAllPaymentToNotifications($aInsertData);
            
            DB::commit();
			
			## Store log
            $message = trans('messages.productsale_create',['name' => $request->product_name]);
            storeActicityLog(trans('messages.productsale_create'),$message);
			return $this->sendResponse($response,$message,200);
       }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,$request->user_id);
			return  $this->sendError($response,trans('messages.something'),500);			
        }
    }
	
	public function updateProductSale(Request $request){
        
		$postData = request()->all();
		
        $response = [];
		
        DB::beginTransaction();
        try{            
            $aInsertData = $request->all();
			if(isset($aInsertData['user_code']))
			{
				unset($aInsertData['user_code']);
			}
            $productsale = $this->productsaleRepo->update($postData['edit_id'],$aInsertData);
			$existing_arr = [];
			if(isset($postData['existing_images'])){
				$existing_arr = $postData['existing_images'];
			}
			$images = ProductImages::where('product_sale_id',$productsale->id)->get();
			
			if(count($images)>0)
			{
				foreach($images as $image)
				{
					if(!in_array($image['image_name'],$existing_arr)){
						$this->removeFile($image->image_name,'productsale');
						$image->delete();
						 
					}
				}
			}

            if($request->product_photo)
            {
                foreach($request->product_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'productsale');
                    if($fileName)
                    {
                        ProductImages::create(['product_sale_id'=>$productsale->id,'image_name' => $fileName]);
                    }
                }
            }
			
			$coordinateArr = $this->userRepo->getLatitudeLongitudes($productsale);
			$productsale->latitude=$coordinateArr['latitude'];
			$productsale->longitude=$coordinateArr['longitude'];
			$productsale->update();
			
            DB::commit();
			
			## Store log
            $message = trans('messages.productsale_update',['name' => $request->product_name]);
            storeActicityLog(trans('messages.productsale_update'),$message);
			return $this->sendResponse($response,$message,200);
       }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,$request->user_id);
			return  $this->sendError($response,trans('messages.something'),500);			
        }
    }
	
	
	public function getProductsaleList(Request $request)
	{
		$requestData = request()->all();
		$module_id = 0;
		if(isset($requestData['module_id']) && $requestData['module_id']!=''){
				$module_id = $requestData['module_id'];
			}
		
		$haversine = $this->userRepo->getDistanceUsingLatLong($requestData);
		
		$query  =  ProductForSale::select('product_for_sales.*',
            DB::raw('(select image_name from product_images where product_sale_id  = product_for_sales.id order by id asc limit 1) as image_name'),
			DB::raw('(select AVG(star_ratings) from review_ratings where 
rateable_id  = product_for_sales.id AND module_id ='.$module_id.' ) as star_rating_count'));
			
		  if(isset($requestData['search_input']) && $requestData['search_input']!=''){
			  $words = preg_split("/[\s,]+/", $requestData['search_input'], -1);
			  $query  =$query->where(function($query) use($words){
				foreach($words as $word) {
					$query->where(function($q) use($word){
						$q->where('product_name', 'LIKE', '%'.$word.'%')
						   ->orWhere('price', 'LIKE', '%'.$word.'%')
						   
							->orWhere('address', 'LIKE', '%'.$word.'%')
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
		  $query  = $query->whereDate('product_for_sales.subscriptionEndDate', '>=', Carbon::now())
			->where('product_for_sales.status', 1);
			
		   if($haversine!='')
		   {
				$query  = $query->orderby("distance", "ASC");
		   }else{
			 $query  = $query->orderby("product_for_sales.id", "DESC"); 
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
		  
		   $response['image_base_path'] =  url("/upload/productsale")."/";
		   /*$response['results'] = ProductForSale::select('product_for_sales.*',
            DB::raw('(select image_name from product_images where product_sale_id  = product_for_sales.id order by id asc limit 1) as image_name'))
			->whereDate('product_for_sales.subscriptionEndDate', '>=', Carbon::now())
			->where('product_for_sales.status', 1)
		    ->orderBy('product_for_sales.id','DESC')->get();
		   $response['image_base_path'] =  url("/upload/productsale")."/";*/
			
		return $this->sendResponse($response,"",200);
	}
	
	public function productsaleDetail(Request $request)
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
		$affectedRows = ProductForSale::where('id', $id)->increment('views_count');
		
		$results = ProductForSale::where('id',$id)->first();
		if($results){
			$images_arr = ProductImages::where('product_sale_id',$results->id)->get();
			$ratings = $this->userRepo->getRatingUsingModuleId($id,$module_id,$postData);
			$results['star_rating_count'] = $ratings['star_rating_count'];
			$results['review_exist'] = $ratings['review_exist'];
			$response = array('results'=>$results,'module_images' =>$images_arr);
			$response['image_base_path'] =  url("/upload/productsale")."/";
			return $this->sendResponse($response,trans('messages.records_found'));
        }else{
            return  $this->sendError([],trans('messages.records_not_found'),404); 
        }
    }
}
