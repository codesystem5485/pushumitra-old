<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\DogShelters;
use App\Models\DogshelterImages;
use App\Models\State;
use App\Repositories\Interfaces\Dogshelters\DogsheltersRepositoryInterface;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use DB;
use App\Traits\FileUpload;
use Validator;
use App\Models\Payments;
use Carbon\Carbon;

class DogShelterController extends BaseController
{
    use FileUpload;
    protected $dogshelterRepo;
	private $userRepo;
	
    /**
     * Transporter Construct 
     * @return url 
     */
    public function __construct(DogsheltersRepositoryInterface $dogshelterRepo, UserRepositoryInterface $userRepository){

        $this->dogshelterRepo = $dogshelterRepo;
		$this->userRepo = $userRepository;
    } 

    /**
     * Transporter Add
   
     */
	 public function addDogshelter(Request $request){
        
		$postData = request()->all();
		$validator = Validator::make($postData, [
				'dogshelter_name' => 'required',
				'incharge_name' => 'required',
				//'sub_category'=>'required',
				'mobile_number' => "required|numeric",
				//'type' => "required",
				'address' => 'required|string',
				'state' => 'required|string',
				'city_town' => 'required|string',
				'pincode' => 'required|numeric|digits:6',
				'state_id' => 'required',
				'user_code'=>'required',
				'registration_number'=>'required',
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
			$aInsertData['type']='Private'; // default
            $results = $this->dogshelterRepo->create($aInsertData);
			/*$category = $results->sub_category;
			
			$categoryName = $this->userRepo->getCategoryName($category);*/

            if($request->dogshelter_photo)
            {
                foreach($request->dogshelter_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'dogshelters');
                    if($fileName)
                    { 
                        DogshelterImages::create(['dog_shelter_id'=>$results->id,'image_name' => $fileName]);
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
				
				$paymentArr = array( 'type'=>$payment->type,'tainingcenter_id'=>$results->id);
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
				$aInsertData['title'] = "Payment Receipt for Dog shelter";
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
            $message = trans('messages.dogshelter_create',['name' => $request->dogshelter_name]);
            storeActicityLog(trans('messages.dogshelter_create'),$message);
			return $this->sendResponse($response,$message,200);
      }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,$request->user_id);
			return  $this->sendError($response,trans('messages.something'),500);			
        }
    }
	
	public function getDogshelterList(Request $request)
	{
		$response['results']  =   DogShelters::select('id','dogshelter_name','incharge_name','mobile_number',
		'registration_number','taluka','address','city_town','district','state','pincode','latitude','longitude',
            DB::raw('(select image_name from  dog_shelter_images where dog_shelter_id  = dog_shelters.id order by id asc limit 1) as image_name'))
			->whereDate('dog_shelters.subscriptionEndDate', '>=', Carbon::now())
			->where('dog_shelters.status', 1)
		    ->orderBy('dog_shelters.id','DESC')->get();
		   $response['image_base_path'] =  url("/upload/dogshelters")."/";
			
		return $this->sendResponse($response,"",200);
	}
	
	public function dogshelterDetail(Request $request)
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
		
		$results =$this->dogshelterRepo->getDogshelter($id);
		if($results){
			$images_arr = DogshelterImages::where('dog_shelter_id',$results->id)->get();
			
			$response = array('results'=>$results,'module_images' =>$images_arr);
			$response['image_base_path'] =  url("/upload/dogshelters")."/";
			
			return $this->sendResponse($response,trans('messages.records_found'));
        }else{
            return  $this->sendError([],trans('messages.records_not_found'),404); 
        }
    }
}