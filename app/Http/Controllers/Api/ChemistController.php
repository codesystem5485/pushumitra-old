<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Chemist;
use App\Models\ChemistShopImages;
use App\Models\State;
use App\Models\Cities;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\ChemistProcessRequest;
use App\Repositories\Interfaces\Chemist\ChemistRepositoryInterface;
use App\Http\Controllers\BaseController as BaseController;
use DB;
use App\Traits\FileUpload;
use Validator;
use App\Models\Payments;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use Carbon\Carbon;


class ChemistController extends BaseController
{
    use FileUpload;
    protected $url = '';
    protected $chemistRepo;
	private $userRepo;
    /**
     * Chemist Construct 
     * @return url 
     */
    public function __construct(ChemistRepositoryInterface $chemistRepo,UserRepositoryInterface $userRepository){

        $this->chemistRepo = $chemistRepo;
		$this->userRepo = $userRepository;
    } 

    /**
     * Chemist List
     * @return View
     */
    public function index(){
        $chemist = Chemist::orderBy('id','ASC')->get();
        return view('backend.chemist.index',['chemist'=>$chemist,'url' => $this->url]); 
    }

    public function addChemist(Request $request){
       $postData = request()->all();
		/*$validator = Validator::make($postData, [
				'shop_name' => 'required',
				'owner_name' => 'required|string',
				'mobile_number' => "required|numeric|digits:10",
				'address' => 'required|string',
				'city_town' => 'required|string',
				'state' => 'required|string',
				'pincode' => 'required|numeric|digits:6',
				'taluka' => 'nullable|string',
				'district' => 'nullable|string',
				'user_code' =>'required'
			]);
			
		if ($validator->fails())
		{
			return $this->sendError([],implode(',',$validator->errors()->all()),400);
		}*/
        DB::beginTransaction();
       try{   
			$response = [];		
            $aInsertData = $request->all();
			$aInsertData['address_line_1'] = $aInsertData['address'];
            $chemist = $this->chemistRepo->create($aInsertData);
            if($request->shop_photo)
            {
                foreach($request->shop_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'chemist');
                    if($fileName)
                    {
                        ChemistShopImages::create(['chemist_id'=>$chemist->id,'image_name' => $fileName]);
                    }
                }
            }
			
			$payment = Payments::find($postData['payment_id']);
			$payment->module_type_id = $chemist->id;
			$payment->save();
			
			$coordinateArr = $this->userRepo->getLatitudeLongitudes($chemist);
			
			$paymentArr = array( 'type'=>$payment->type,'animalsale_id'=>$chemist->id);
			$subscriptionArr = $this->chemistRepo->getSubscriptionDates($paymentArr);
			
			$chemist->latitude=$coordinateArr['latitude'];
			$chemist->longitude=$coordinateArr['longitude'];
			$chemist->subscriptionStartDate=$subscriptionArr['subscriptionStartDate'];
			$chemist->subscriptionEndDate=$subscriptionArr['subscriptionEndDate'];
			$chemist->update();
			
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
			$aInsertData['title'] = "Payment Receipt for Chemist";
			$notifications = $this->chemistRepo->addPaymentToNotifications($aInsertData);

            DB::commit();
            ## Store log
            $message = trans('messages.chemist_create',['name' => $request->shop_name]);
            storeActicityLog(trans('messages.chemist_create'),$message,$request->user_id,$chemist);
			return $this->sendResponse($response,$message,200);
        }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,$request->user_id);
           
            return  $this->sendError($response,trans('messages.something'),500);
            
        }  
    }
	
	public function getChemistList(Request $request)
	{
		$response['results']  =   Chemist::select( 'chemists.*', DB::raw('(select image_name from chemist_shop_images where chemist_id  =   chemists.id order by id asc limit 1) as image_name')  )
           ->whereDate('chemists.subscriptionEndDate', '>=', Carbon::now())
		   ->orderBy('chemists.id','ASC')->get();
		   $response['image_base_path'] =  url("/upload/chemist")."/";
			
		return $this->sendResponse($response,"",200);
	}

    public function chemistDetail(Request $request){
		$id = $request->chemist_id;
        
		$chemist = Chemist::where('id',$id)->first();
		$response = [];
		
		$chemistimages=array();
		if($chemist){
			$chemistimages = ChemistShopImages::where('chemist_id',$chemist->id)->get();
		}
		if($chemist){
			
			$response = array('results'=>$chemist,'module_images' =>$chemistimages);
			$response['image_base_path'] =  url("/upload/chemist")."/";
			
			return $this->sendResponse($response,trans('messages.records_found'));
        }else{
            return  $this->sendError([],trans('messages.records_not_found'),404); 
        }
    }

}
