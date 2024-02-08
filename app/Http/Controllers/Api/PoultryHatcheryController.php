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
     * Transporter Add
   
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
	
	public function getPoultryHatcheryList(Request $request)
	{
		$query = PoultryHatchery::select('id','poultryhatchery_center_name','incharge_name','mobile_number','type',
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
			$response['image_base_path'] =  url("/upload/poultryhatchery")."/";
			
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
		
		$results =$this->poultryhatcheryRepo->getPoultryHatchery($id);
		if($results){
			$images_arr = PoultryHatcheryImages::where('poultryhatchery_center_id',$results->id)->get();
			
			$response = array('results'=>$results,'module_images' =>$images_arr);
			$response['image_base_path'] =  url("/upload/poultryhatchery")."/";
			
			return $this->sendResponse($response,trans('messages.records_found'));
        }else{
            return  $this->sendError([],trans('messages.records_not_found'),404); 
        }
    }

}