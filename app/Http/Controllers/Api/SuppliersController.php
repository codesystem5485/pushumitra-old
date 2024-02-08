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
     * Transporter Add
   
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
	
	public function getSupplierList(Request $request)
	{
		$response['results'] = Suppliers::leftJoin('subcategories', 'subcategories.id', '=', 'suppliers.sub_category')
		->select('suppliers.*','subcategories.name as sub_category_name',
            DB::raw('(select image_name from  supplier_product_images where supplier_id  = suppliers.id order by id asc limit 1) as image_name'))
			->whereDate('suppliers.subscriptionEndDate', '>=', Carbon::now())
			->where('suppliers.status', 1)
		    ->orderBy('suppliers.id','DESC')->get();
		$response['image_base_path'] =  url("/upload/suppliers")."/";
		return $this->sendResponse($response,"",200);
	}
	
	public function supplierDetail(Request $request)
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
		
		$results =$this->suppliersRepo->getSuppliers($id);
		if($results){
			$images_arr = SupplierProductImages::where('supplier_id',$results->id)->get();
			
			$response = array('results'=>$results,'module_images' =>$images_arr);
			$response['image_base_path'] =  url("/upload/suppliers")."/";
			
			return $this->sendResponse($response,trans('messages.records_found'));
        }else{
            return  $this->sendError([],trans('messages.records_not_found'),404); 
        }
    }
}