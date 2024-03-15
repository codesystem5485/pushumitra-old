<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use DB;
use Validator;
use App\Models\Fee;
use App\Models\Payments;
use App\Models\UserModuleCounts;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use App\Repositories\Interfaces\User\UserDetailRepositoryInterface;


class PaymentController extends BaseController
{
    private $userRepo;
    private $userDetailRepo;
	
    public function __construct(
        UserRepositoryInterface $userRepository,
        UserDetailRepositoryInterface $userDetailRepository ){
		$this->userRepo = $userRepository;
        $this->userDetailRepo = $userDetailRepository;
		
    } 
	
	public function getConfig(Request $request)
	{
		$user_id = $request->user_id;
		$role = $request->role;
		
		//add modules count data
		$moduleres = $this->addModulesCount($user_id);
				
		$feeArray = Fee::orderBy('id','ASC')->get()->toArray();
		$createArray = [];
		if(count($feeArray) > 0){
			foreach($feeArray as $row){
				$name = str_replace(' ', '_', $row['name']);
				$createArray[$name]=array('fee'=>(string)$row['fee'],'id'=>$row['id']);
			}
		}
		
		$completedProfile =0; 
		$completedPayment =1;
		$verified=0;
		$paymentMsg = '';
		$verifyMsg = '';
		$profileMsg ='';
		
		$response = $this->userRepo->checkProfilePaymentDetails($user_id,$role);
		$response['fee'] = $createArray;
		$response['module_counts'] = $moduleres;
		
		return $this->sendResponse($response,"",200);
	}
	
	public function addModulesCount($user_id){
	
		$chkCount = UserModuleCounts::where('user_id',$user_id)->count();
		if($chkCount == 0){		
				$modulesArr	=array(
					'Pashumitra Registration'=> 0,
					'Add Animal for sale'=> 0,
					'Add Breeder'=> 0,
					'Add Transporter'=> 0,
					'Add chemist'=> 0,
					'Registered-vet Registration'=> 0,
					'Add Veterinary Hospitals'=>0,
					'Add Product For Sale'=> 0,
					'Add Supplier'=> 0,
					'Add Farm'=> 0,
					'Add Training Centre'=> 0,
					'Add Shop'=> 0,
					'Go Shala / Panjarpol'=> 0,
					'Poultry Hatchery'=> 0,
					'Dog Shelter'=> 0,
					'Institutions'=> 0,
					'Milk Collection'=> 0,
					'Add Lab'=> 0,
					'Add NGO'=> 0,
					'Knowledge Sahring'=> 0,
				);
				
				$module = json_encode($modulesArr);
				$modules = new UserModuleCounts;
				$modules->user_id = $user_id;
				$modules->module = $module;
				$modules->save();
		}
		$moduleArr=[];
		$chkarr = UserModuleCounts::where('user_id',$user_id)->first();
		if($chkarr->module!=''){
			$moduleArr = json_decode($chkarr->module, true);
		}
	
		$createArray = [];
		if(count($moduleArr) > 0){
			
			foreach($moduleArr as $key => $val){ 
				$name = str_replace(' ', '_', $key);
				$createArray[$name]= (int)$val;
			}
		}
		return $createArray;
	}
	
	public function generatePaymentOrderId(Request $request){
		
		$postData = $request->all();
		//$api_key='rzp_test_bmd8yXq2RAx4Uy';
		//$api_secret='rQ9oPkuZ0CdQcL2A9TbPVf3g';
		
		//$api_key='rzp_live_bhuyXPXQEwDPjI';
		//$api_secret='Jw41w0fZnCUcobdfTp4w7oYb';
		$api_key= env('RAZORPAY_KEY');
		$api_secret=env('RAZORPAY_SECRET_KEY');
		
		$validator = Validator::make($postData, [
                'amount'=>'required',
                'type' => 'required',
				'receipt'=>'required',
            ]);
			
		$response = [];
		if ($validator->fails())
		{
			return $this->sendError($response,implode(',',$validator->errors()->all()),400);
		}
		 $url = 'https://api.razorpay.com/v1/orders/';
		 
		 $data= array('receipt' => $postData['receipt'], 'amount' => $postData['amount'], 'currency' => 'INR', 'notes'=> array('key1'=> $postData['type']));
		 $key_id = $api_key;
		$key_secret = $api_secret;
		$params = http_build_query($data);
		//cURL Request
		$ch = curl_init();
		//set the url, number of POST vars, POST data
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_USERPWD, $key_id . ':' . $key_secret);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		$result = curl_exec($ch);
		
		$response = json_decode($result);
		
		$details = array('response'=>$response);
		return $this->sendResponse($response,"",200);
        
    }
	
	public function addPayments(Request $request)
	{
		$postData = request()->all();
		
		$validator = Validator::make($postData, [
				'role' => 'required',
				'amount' => 'required',
				'type' => 'required',
				'order_id' => 'required',
			]);
			
		if ($validator->fails())
		{
			return $this->sendError([],implode(',',$validator->errors()->all()),400);
		}
		
        $response = [];
		
        DB::beginTransaction();
        try{            
            $aInsertData = $request->all();
		$roleId = null;
		if($aInsertData['role']=="Pashumitra"){
			$roleId = 8;
		}elseif($aInsertData['role']=="Registered-vet")
		{
			$roleId = 7;
		}
		elseif($aInsertData['role']=="Animal-owner")
		{
			$roleId = 6;
		}
		
		$payment_response = $aInsertData['payment_response'];
		$amount = $aInsertData['amount'];
		$type = $aInsertData['type'];
		$order_id =$aInsertData['order_id'];
		
		$paymentId =0;
		$status=0;
		$payment_request = '';
		
		if($aInsertData['payment_id']!=''){
			$paymentId =$aInsertData['payment_id'];
			$status = 1;
		}
		$jsonArr = '';
			if(isset($aInsertData['name']) && isset($aInsertData['mobile_number'])){
				$moduleDetails = array('name'=>$aInsertData['name'],'mobile_number'=>$aInsertData['mobile_number']);
				$jsonArr = json_encode($moduleDetails);
			}
		
		
			$insertArray = array(
					'role_id'=>$roleId,
					'user_id'=>$aInsertData['user_id'],
					'payment_id' =>$paymentId,
					'order_id' =>$aInsertData['order_id'],
					'status' =>$status,
					'payment_date' =>date("Y-m-d H:i:s"),
					'payment_response' =>$payment_response,
					//'payment_request' =>$payment_request,
					'amount' =>$amount,
					'type' =>$type,
					'module_details'=>$jsonArr 
				);
			
			$payment = Payments::create($insertArray);
			
			$createdPaymentId = 0;
			
			//pashumitra sign up
			if($roleId==8 && $type==1){
				if(isset($payment->id))
				{ 
					$createdPaymentId = $payment->id;
					//get subscriptions date
					$paymentArr = array( 'type'=>$payment->type,'id'=>$aInsertData['user_id']);
					$subscriptionArr = $this->userRepo->getSubscriptionDates($paymentArr);
					//generate pm_code & update to user table
					$param['pm_code'] = $this->userRepo->generatePashumitraCode();
					 $param['subscriptionStartDate']=$subscriptionArr['subscriptionStartDate'];
					 $param['subscriptionEndDate']=$subscriptionArr['subscriptionEndDate'];
					$this->userRepo->update($aInsertData['user_id'],$param);  
				}
			}
			
			//registered vet sign up
			if($roleId==7 && $type==6){
				if(isset($payment->id))
				{ 
					$createdPaymentId = $payment->id;
					//get subscriptions date
					$paymentArr = array( 'type'=>$payment->type,'id'=>$aInsertData['user_id']);
					$subscriptionArr = $this->userRepo->getSubscriptionDates($paymentArr);
					//generate rv_code & update to user table
					$param['rv_code'] = $this->userRepo->generateRegisteredvetCode();
					$param['subscriptionStartDate']=$subscriptionArr['subscriptionStartDate'];
					$param['subscriptionEndDate']=$subscriptionArr['subscriptionEndDate'];
					$this->userRepo->update($aInsertData['user_id'],$param); 
				}
			}
			
			$link = url("/invoice/download/".$aInsertData['user_id'].'/'.$createdPaymentId);
			
			$aInsertData['sender_user_id'] = $aInsertData['user_id'];
			$aInsertData['rx_reminder_id'] = 0;
			$aInsertData['type'] = 2;
			$aInsertData['link'] = $link;
			$aInsertData['scheduled_date'] = date("Y-m-d");
			$aInsertData['scheduled_message'] ="Thank you.Your payment has been confirmed.Please download your bill receipt.";
			$aInsertData['title'] = 'Payment Receipt';
			$notifications = $this->userRepo->addPaymentToNotifications($aInsertData);
			
			$response['payments'] =$payment; 
            DB::commit();
			 ## Store log
            $message = trans('messages.payments_create',['name' => $paymentId]);
            storeActicityLog(trans('messages.payments_create'),$message);
			
			return $this->sendResponse($response,trans('messages.payments_create'),200);
        }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,$request->user_id);
			return  $this->sendError($response,trans('messages.something'),500);			
        }
	}
	
	public function updateModuleCount(Request $request)
	{
		$postData = request()->all();
		$validator = Validator::make($postData, [
				'module_name' => 'required',
				
			]);
			
		if ($validator->fails())
		{
			return $this->sendError([],implode(',',$validator->errors()->all()),400);
		}
		
		$postData['flag'] = 0;
		$response=[];
		$updateModuleCount = $this->userRepo->updateModuleCount($postData);
		$message = "Count updated successfully";
		return $this->sendResponse($response,$message,200);
	}
}
