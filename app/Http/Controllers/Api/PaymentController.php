<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use DB;
use Validator;
use App\Models\Fee;

class PaymentController extends BaseController
{
  
    public function __construct(){
		
    } 
	
	public function getFee(Request $request)
	{
		$response = Fee::orderBy('id','ASC')->get();
		
		return $this->sendResponse($response,"",200);
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

    

}
