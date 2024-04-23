<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\Suppliers;
use App\Models\SupplierProductImages;
use App\Models\State;
use App\Repositories\Interfaces\Suppliers\SuppliersRepositoryInterface;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use DB;
use App\Traits\FileUpload;
use Validator;
use App\Models\Payments;
use Carbon\Carbon;

class SuppliersController extends BaseController
{
    use FileUpload;
    protected $url = '';
    protected $suppliersRepo;
	private $userRepo;
	
    /**
     * supplier Construct 
     * @return url 
     */
    public function __construct(SuppliersRepositoryInterface $suppliersRepo, UserRepositoryInterface $userRepository){

        $this->suppliersRepo = $suppliersRepo;
		$this->userRepo = $userRepository;
    } 

    /**
     * supplier Add
   
     */
	 public function addSupplier(Request $request){
        
		$postData = request()->all();
		/*$validator = Validator::make($postData, [
				'supplier_name' => 'required',
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
            $suppliers = $this->suppliersRepo->create($aInsertData);
			$category = $suppliers->sub_category;
			
			$categoryName = $this->userRepo->getCategoryName($category);

            if($request->supplier_photo)
            {
                foreach($request->supplier_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'suppliers');
                    if($fileName)
                    {
                        SupplierProductImages::create(['supplier_id'=>$suppliers->id,'image_name' => $fileName]);
                    }
                }
            }
			
			$payment = Payments::find($postData['payment_id']);
			$payment->module_type_id = $suppliers->id;
			$payment->save();
			
			$coordinateArr = $this->userRepo->getLatitudeLongitudes($suppliers);
			$paymentArr = array( 'type'=>$payment->type,'supplier_id'=>$suppliers->id);
			$subscriptionArr = $this->userRepo->getAllSubscriptionDates($paymentArr);
			
			$suppliers->latitude=$coordinateArr['latitude'];
			$suppliers->longitude=$coordinateArr['longitude'];
			$suppliers->subscriptionStartDate=$subscriptionArr['subscriptionStartDate'];
			$suppliers->subscriptionEndDate=$subscriptionArr['subscriptionEndDate'];
			$suppliers->update();
			
			
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
            
            DB::commit();
			## Store log
            $message = trans('messages.supplier_create',['name' => $request->supplier_name]);
            storeActicityLog(trans('messages.supplier_create'),$message);
			return $this->sendResponse($response,$message,200);
      }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,$request->user_id);
			return  $this->sendError($response,trans('messages.something'),500);			
        }
    }
	
	public function updateSupplier(Request $request){
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
			 $suppliers = $this->suppliersRepo->update($postData['edit_id'],$aInsertData);
			$existing_arr = [];
			if(isset($postData['existing_images'])){
				$existing_arr = $postData['existing_images'];
			}
			$images = SupplierProductImages::where('supplier_id',$suppliers->id)->get();
			
			if(count($images)>0)
			{
				foreach($images as $image)
				{
					if(!in_array($image['image_name'],$existing_arr)){
						$this->removeFile($image->image_name,'suppliers');
						$image->delete();
					}
				}
			}
			
            if($request->supplier_photo)
            {
                foreach($request->supplier_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'suppliers');
                    if($fileName)
                    {
                        SupplierProductImages::create(['supplier_id'=>$suppliers->id,'image_name' => $fileName]);
                    }
                }
            }
			$coordinateArr = $this->userRepo->getLatitudeLongitudes($suppliers);
			$suppliers->latitude=$coordinateArr['latitude'];
			$suppliers->longitude=$coordinateArr['longitude'];
			$suppliers->update();
			
            DB::commit();
			## Store log
            $message = trans('messages.supplier_update',['name' => $request->supplier_name]);
            storeActicityLog(trans('messages.supplier_update'),$message);
			return $this->sendResponse($response,$message,200);
      }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,$request->user_id);
			return  $this->sendError($response,trans('messages.something'),500);			
        }
    }
	
	public function getSupplierList(Request $request)
	{
		$requestData = request()->all();
		$module_id = 0;
		if(isset($requestData['module_id']) && $requestData['module_id']!=''){
				$module_id = $requestData['module_id'];
			}
		$haversine = $this->userRepo->getDistanceUsingLatLong($requestData);
		$query  = Suppliers::leftJoin('subcategories', 'subcategories.id', '=', 'suppliers.sub_category')
		->select('suppliers.id','suppliers.user_code','suppliers.supplier_name','suppliers.mobile_number','suppliers.sub_category',
		'suppliers.address','suppliers.city_town','suppliers.district','suppliers.taluka','suppliers.user_code','suppliers.latitude',
		'suppliers.longitude','subcategories.name as sub_category_name',
            DB::raw('(select image_name from  supplier_product_images where supplier_id  = suppliers.id order by id asc limit 1) as image_name'),
			DB::raw('(select AVG(star_ratings) from review_ratings where 
rateable_id  =   suppliers.id AND module_id ='.$module_id.' ) as star_rating_count'));
		
		if($haversine!=''){
			$query  = $query->selectRaw("$haversine AS distance");
		  }
		  
		  if(isset($requestData['search_input']) && $requestData['search_input']!=''){
			  $words = preg_split("/[\s,]+/", $requestData['search_input'], -1);
			  $query  =$query->where(function($query) use($words){
				foreach($words as $word) {
					$query->where(function($q) use($word){
						$q->where('supplier_name', 'LIKE', '%'.$word.'%')
						 ->orWhere('subcategories.name', 'LIKE', '%'.$word.'%')
						    ->orWhere('city_town', 'LIKE', '%'.$word.'%')
							 ->orWhere('taluka', 'LIKE', '%'.$word.'%')
							 ->orWhere('district', 'LIKE', '%'.$word.'%')
							 ->orWhere('pincode', 'LIKE', '%'.$word.'%');
					});
				}
			});
		  } 
		  
		  $query  = $query->whereDate('suppliers.subscriptionEndDate', '>=', Carbon::now());
		    
		   if($haversine!=''){
			$query  = $query->orderby("distance", "ASC");
		  }else{
			 $query  = $query->orderby("suppliers.id", "DESC"); 
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
		  $response['image_base_path'] =  url("/upload/suppliers")."/";
		  
		/*$response['results'] = Suppliers::leftJoin('subcategories', 'subcategories.id', '=', 'suppliers.sub_category')
		->select('suppliers.*','subcategories.name as sub_category_name',
            DB::raw('(select image_name from  supplier_product_images where supplier_id  = suppliers.id order by id asc limit 1) as image_name'))
			->whereDate('suppliers.subscriptionEndDate', '>=', Carbon::now())
			->where('suppliers.status', 1)
		    ->orderBy('suppliers.id','DESC')->get();
		$response['image_base_path'] =  url("/upload/suppliers")."/";*/
		return $this->sendResponse($response,"",200);
	}
	
	public function supplierDetail(Request $request)
	{
		$postData = request()->all();
		$validator = Validator::make($postData, [
				'detail_id' => 'required',
			]);
			
		if($validator->fails())
		{
			return $this->sendError([],implode(',',$validator->errors()->all()),400);
		}
		$response = [];
		$id = $request->detail_id;
		$module_id = 0;
		if(isset($postData['module_id']) && $postData['module_id']!=''){
				$module_id = $postData['module_id'];
			}
		
		$affectedRows = Suppliers::where('id', $id)->increment('views_count');
		
		$results =$this->suppliersRepo->getSuppliers($id);
		if($results){
			$images_arr = SupplierProductImages::where('supplier_id',$results->id)->get();
			$ratings = $this->userRepo->getRatingUsingModuleId($id,$module_id,$postData);
			$results['star_rating_count'] = $ratings['star_rating_count'];
			$results['review_exist'] = $ratings['review_exist'];
			$response = array('results'=>$results,'module_images' =>$images_arr);
			$response['image_base_path'] =  url("/upload/suppliers")."/";
			
			return $this->sendResponse($response,trans('messages.records_found'));
        }else{
            return  $this->sendError([],trans('messages.records_not_found'),404); 
        }
    }
	
	public function deleteSupplier(Request $request){
		$postData = request()->all();		
		$validator = Validator::make($postData, [
			'id' => 'required',
		]);
			
		if ($validator->fails())
		{
			return $this->sendError([],implode(',',$validator->errors()->all()),400);
		}
	
		$id = $request->id;
        $result = Suppliers::where('id',$id)->first();
		$name = '';
		if($result){
			$name = $result->supplier_name;
			$images = SupplierProductImages::where('supplier_id',$result->id)->get();
			
			if(count($images)>0)
			{
				foreach($images as $image)
				{
					$this->removeFile($image->image_name,'suppliers');
					$image->delete();
				}
			}
			$arr = array('status'=>0);
			$Suppliers = $this->suppliersRepo->update($id,$arr);
		}
		
        $response=[];
        ## Store log
        $message = trans('messages.supplier_delete',['name' => $name]);
        storeActicityLog(trans('messages.supplier_delete'),$message,$postData['user_id'],$result);
		return $this->sendResponse($response,$message,200);
    }
}