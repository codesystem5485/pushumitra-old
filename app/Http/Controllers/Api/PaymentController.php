<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use DB;
use Validator;
use App\Models\Fee;
use App\Models\Payments;
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
		$feeArray = Fee::orderBy('id','ASC')->get()->toArray();
		$createArray = [];
		if(count($feeArray) > 0){
			foreach($feeArray as $row){
				$name = str_replace(' ', '_', $row['name']);
				$createArray[$name]=array('fee'=>$row['fee'],'id'=>$row['id']);
			}
		}
		
		
		
		$user_id = $request->user_id;
		$role = $request->role;
		
		$completedProfile =0; 
		$completedPayment =1;
		$verified=0;
		$paymentMsg = '';
		$verifyMsg = '';
		$profileMsg ='';
		
		$response = $this->userRepo->checkProfilePaymentDetails($user_id,$role);
		$response['fee'] = $createArray;
		
		 
		 
		/* $response['verifyMsg'] = $verifyMsg;
		 $response['profileMsg'] = $profileMsg;
		 $response['paymentMsg'] = $paymentMsg;
		 $response['completedProfile'] = $completedProfile;
		 $response['completedPayment'] = $completedPayment;
		 $response['verified'] = $verified;*/
		 
		 
		//$userArray = $this->checkProfile($user_id,$role);
		
		return $this->sendResponse($response,"",200);
	}
	
	
	public function checkProfile($user_id,$role)
	{
		$completedProfile =0; 
		$completedPayment =0;
		$verified=0;		
		
		$select = ['*'];
        
		$with  = ['getUserDetail'];			
		$userDetail = $this->userRepo->getSingleRecords($filter,$select,$with);
		if($userDetail['full_name']!='' && $userDetail['mobile']!='' && $userDetail['date_of_birth']!='' &&
		 $userDetail['gender']!=''  && $userDetail['city_town']!='' && $userDetail['pm_aadhar_no']!='' && 
		 $userDetail['pm_pan_no']!='' && $userDetail['job_type']!=''){
			 
			 $completedProfile =1;
		 }
		 $verified=$userDetail['is_verified'];
		
		
	}
	
	public function generatePaymentOrderId(Request $request){
		
		$postData = $request->all();
		
		$api_key='rzp_test_fbiwRvGp1057Ua';
		$api_secret='O4q2Dcq4PhZHNAwYvXifusHF';
		
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
		
		$paymentId =0;$status=0;$payment_request = '';
		
		if($aInsertData['payment_id']!=0 || $aInsertData['payment_id']!=''){
			$paymentId =$aInsertData['payment_id'];
			
			$status = 1;
			
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
				);
			
			
			$payment = Payments::create($insertArray);
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
}
