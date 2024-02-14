<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\Institutions;
use App\Models\InstitutionImages;
use App\Models\State;
use App\Repositories\Interfaces\Institutions\InstitutionsRepositoryInterface;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use DB;
use App\Traits\FileUpload;
use Validator;
use App\Models\Payments;
use Carbon\Carbon;

class InstitutionsController extends BaseController
{
    use FileUpload;
    protected $institutionsRepo;
	private $userRepo;
	
    /**
     * Transporter Construct 
     * @return url 
     */
    public function __construct(InstitutionsRepositoryInterface $institutionsRepo, UserRepositoryInterface $userRepository){

        $this->institutionsRepo = $institutionsRepo;
		$this->userRepo = $userRepository;
    } 

    /**
     * Transporter Add
   
     */
	 public function addInstitution(Request $request){
        
		$postData = request()->all();
		/*$validator = Validator::make($postData, [
				'institution_name' => 'required',
				'incharge_name' => 'required',
				'sub_category'=>'required',
				'mobile_number' => "required|numeric",
				'type' => "required",
				'address' => 'required|string',
				'state' => 'required|string',
				'city_town' => 'required|string',
				'pincode' => 'required|numeric|digits:6',
				'state_id' => 'required',
				'user_code'=>'required',
				'registration_number'=>'required',
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
            $results = $this->institutionsRepo->create($aInsertData);
			$category = $results->sub_category;
			
			$categoryName = $this->userRepo->getCategoryName($category);

            if($request->institution_photo)
            {
                foreach($request->institution_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'institutions');
                    if($fileName)
                    {
                        InstitutionImages::create(['institution_id'=>$results->id,'image_name' => $fileName]);
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
				
				$paymentArr = array( 'type'=>$payment->type,'institution_id'=>$results->id);
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
            $message = trans('messages.institution_create',['name' => $request->institution_name]);
            storeActicityLog(trans('messages.institution_create'),$message);
			return $this->sendResponse($response,$message,200);
      }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,$request->user_id);
			return  $this->sendError($response,trans('messages.something'),500);			
        }
    }
	
	public function getInstitutionList(Request $request)
	{
		$response['results']  =   Institutions::leftJoin('subcategories', 'subcategories.id', '=', 'institutions.sub_category')
								->select('institutions.id','institution_name','incharge_name','mobile_number','type',
		'registration_number','taluka','address','city_town','district','state','pincode','latitude','longitude','subcategories.name as subcategory_name',
            DB::raw('(select image_name from  institutions_images where institution_id  = institutions.id order by id asc limit 1) as image_name'))
			->where(function($query){
                            $query->where(function($query){
                                 $query->where('type','Private')->whereDate('institutions.subscriptionEndDate', '>=', Carbon::now());
                             })
							 ->orWhere(function($query){
                                 $query->where('type','Government')->where('institutions.subscriptionEndDate', '0000-00-00');
                             });
                         })
						 
			->where('institutions.status', 1)
		    ->orderBy('institutions.id','DESC')->get();
		   $response['image_base_path'] =  url("/upload/institutions")."/";
			
		return $this->sendResponse($response,"",200);
	}
	
	public function institutionDetail(Request $request)
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
		
		$results =$this->institutionsRepo->getInstitution($id);
		if($results){
			$images_arr = InstitutionImages::where('institution_id',$results->id)->get();
			
			$response = array('results'=>$results,'module_images' =>$images_arr);
			$response['image_base_path'] =  url("/upload/institutions")."/";
			
			return $this->sendResponse($response,trans('messages.records_found'));
        }else{
            return  $this->sendError([],trans('messages.records_not_found'),404); 
        }
    }

}