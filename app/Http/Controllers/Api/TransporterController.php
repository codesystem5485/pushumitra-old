<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\Transporters;
use App\Models\VehicleImages;
use App\Models\TransporterRcbookImages;
use App\Models\State;
use App\Repositories\Interfaces\Transporter\TransporterRepositoryInterface;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use DB;
use App\Traits\FileUpload;
use Validator;
use App\Models\Payments;
use Carbon\Carbon;

class TransporterController extends BaseController
{
    use FileUpload;
    protected $url = '';
    protected $transporterRepo;
	private $userRepo;
	
    /**
     * Transporter Construct 
     * @return url 
     */
    public function __construct(TransporterRepositoryInterface $transporterRepo, UserRepositoryInterface $userRepository){

        $this->transporterRepo = $transporterRepo;
		$this->userRepo = $userRepository;
    } 

    /**
     * Transporter Add
   
     */
	 public function addTransporter(Request $request){
        
		$postData = request()->all();
		$validator = Validator::make($postData, [
				'transporter_name' => 'required',
				'vehicle_name' => 'required',
				'mobile_number' => "required|numeric",
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
		}
		
        $response = [];
		
        DB::beginTransaction();
        try{            
            $aInsertData = $request->all();
            $transporter = $this->transporterRepo->create($aInsertData);

            if($request->vehicle_photo)
            {
                foreach($request->vehicle_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'vehicle');
                    if($fileName)
                    {
                        VehicleImages::create(['transporter_id'=>$transporter->id,'image_name' => $fileName]);
                    }
                }
            }
			
			if($request->rcbook_photo)
            {
                foreach($request->rcbook_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'rcbooks');
                    if($fileName)
                    {
                        TransporterRcbookImages::create(['transporter_id'=>$transporter->id,'image_name' => $fileName]);
                    }
                }
            }
			
			$payment = Payments::find($postData['payment_id']);
			$payment->module_type_id = $transporter->id;
			$payment->save();
			
			$coordinateArr = $this->userRepo->getLatitudeLongitudes($transporter);
			$paymentArr = array( 'type'=>$payment->type,'transporter_id'=>$transporter->id);
			$subscriptionArr = $this->userRepo->getAllSubscriptionDates($paymentArr);
			
			$transporter->latitude=$coordinateArr['latitude'];
			$transporter->longitude=$coordinateArr['longitude'];
			$transporter->subscriptionStartDate=$subscriptionArr['subscriptionStartDate'];
			$transporter->subscriptionEndDate=$subscriptionArr['subscriptionEndDate'];
			$transporter->update();
			
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
			$aInsertData['title'] = "Payment Receipt for Transporter";
			$notifications = $this->userRepo->addAllPaymentToNotifications($aInsertData);
            
            DB::commit();
			 ## Store log
             $message = trans('messages.transporter_create',['name' => $request->input('transporter_name')]);
            storeActicityLog(trans('messages.transporter_create'),$message);
			return $this->sendResponse($response,$message,200);
       }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,$request->user_id);
			return  $this->sendError($response,trans('messages.something'),500);			
        }
    }
	
	public function getTransporterList(Request $request)
	{
		$response['results']  =   Transporters::select( 'transporters.*',
            DB::raw('(select image_name from  vehicle_images where transporter_id  = transporters.id order by id asc limit 1) as image_name'))
			->whereDate('transporters.subscriptionEndDate', '>=', Carbon::now())
			->where('transporters.status', 1)
		    ->orderBy('transporters.id','DESC')->get();
		   $response['image_base_path'] =  url("/upload/vehicle/");
			
		return $this->sendResponse($response,"",200);
	}
	
	public function transporterDetail(Request $request)
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
		$results = Transporters::where('id',$id)->first();
		if($results){
			$images_arr = VehicleImages::where('transporter_id',$results->id)->get();
			$images_rcbook_arr = VehicleImages::where('transporter_id',$results->id)->get();
			$response = array('results'=>$results,'module_images' =>$images_arr,'rcbooksImages'=>$images_rcbook_arr);
			
			$response['vehicles_image_path'] =  url("/upload/vehicle/");
			$response['rcbooks_image_path'] =  url("/upload/rcbooks/");
			
			return $this->sendResponse($response,trans('messages.records_found'));
        }else{
            return  $this->sendError([],trans('messages.records_not_found'),404); 
        }
    }

}