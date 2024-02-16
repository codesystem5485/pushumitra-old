<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\Panjarpol;
use App\Models\PanjarpolImages;
use App\Models\State;
use App\Repositories\Interfaces\Panjarpol\PanjarpolRepositoryInterface;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use DB;
use App\Traits\FileUpload;
use Validator;
use App\Models\Payments;
use Carbon\Carbon;

class PanjarpolController extends BaseController
{
    use FileUpload;
    protected $panjarpolRepo;
	private $userRepo;
	
    /**
     * Panjarpol Construct 
     * @return url 
     */
    public function __construct(PanjarpolRepositoryInterface $panjarpolRepo, UserRepositoryInterface $userRepository){

        $this->panjarpolRepo = $panjarpolRepo;
		$this->userRepo = $userRepository;
    } 

    /**
     * Panjarpol Add
   
     */
	 public function addPanjarpol(Request $request){
        
		$postData = request()->all();
		/*$validator = Validator::make($postData, [
				'panjarpol_name' => 'required',
				//'manager_name' => 'required',
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
            $results = $this->panjarpolRepo->create($aInsertData);

            if($request->panjarpol_photo)
            {
                foreach($request->panjarpol_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'panjarpol');
                    if($fileName)
                    {
                        PanjarpolImages::create(['panjarpol_id'=>$results->id,'image_name' => $fileName]);
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
				
				$paymentArr = array( 'type'=>$payment->type,'panjarpol_id'=>$results->id);
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
				$aInsertData['title'] = "Payment Receipt for Panjarpol";
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
            $message = trans('messages.panjarpol_create',['name' => $request->panjarpol_name]);
            storeActicityLog(trans('messages.panjarpol_create'),$message);
			return $this->sendResponse($response,$message,200);
      }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,$request->user_id);
			return  $this->sendError($response,trans('messages.something'),500);			
        }
    }
	
	public function getPanjarpolList(Request $request)
	{
		$response['results']  = Panjarpol::select('panjarpol.id','registration_number','panjarpol_name','manager_name','mobile_number',
		'taluka','address','city_town','district','state','pincode','latitude','longitude',
            DB::raw('(select image_name from panjarpol_images where panjarpol_id  = panjarpol.id order by id asc limit 1) as image_name'))
			->whereDate('panjarpol.subscriptionEndDate', '>=', Carbon::now())
			->where('panjarpol.status', 1)
		    ->orderBy('panjarpol.id','DESC')->get();
		   $response['image_base_path'] =  url("/upload/panjarpol")."/";
			
		return $this->sendResponse($response,"",200);
	}
	
	public function panjarpolDetail(Request $request)
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
		
		$results =$this->panjarpolRepo->getPanjarpol($id);
		if($results){
			$images_arr = PanjarpolImages::where('panjarpol_id',$results->id)->get();
			
			$response = array('results'=>$results,'module_images' =>$images_arr);
			$response['image_base_path'] =  url("/upload/panjarpol")."/";
			
			return $this->sendResponse($response,trans('messages.records_found'));
        }else{
            return  $this->sendError([],trans('messages.records_not_found'),404); 
        }
    }

}