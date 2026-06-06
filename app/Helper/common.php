<?php
use App\Models\User;
use App\Models\Payments;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
if(!function_exists('convert_permission_name')){ 
    function convert_permission_name(string $string = ''){
        return strtoupper(preg_replace("/[^a-zA-Z]+/", " ", $string));
    }
}

if(!function_exists('random_number')){ 
    function random_number(){
        return mt_rand(1111,9999);
    }
}

if(!function_exists('convert_small')){ 
    function convert_small($value){
        return strtolower($value);
    } 
}

if(!function_exists('skip_empty_field')){ 
    function skip_empty_field($data){
        if(!empty($data)){
            return array_filter($data);
        }
    }
}
if(!function_exists('encryptData')){ 
    function encryptData($id = ''){
        $encryptKey = base64_encode($id); 
        if($encryptKey){
            return $encryptKey;
        }
        return false;
    }
}
if(!function_exists('decryptData')){ 
    function decryptData($id = ''){
        $decryptKey = base64_decode($id); 

        if($decryptKey){
            return $decryptKey;
        }
        return false;
    }
}
if(!function_exists('storeActicityLog')){  
    function storeActicityLog($log_name = 'default', $msg='',$causered = [],$performed = [],$properties = []){
        $oActivity = activity($log_name);
        if(!empty($causered)){
            $oActivity = $oActivity->causedBy($causered);
        }
        if(!empty($performed)){
            $oActivity = $oActivity->performedOn($performed);
        }
        $oActivity->withProperties($properties)
        ->log($msg);
    }
}

if(!function_exists('convertDate')){ 
    function convertDate($date = '', $format = 'd-m-Y H:i:s'){
       return date($format,strtotime($date));
    }
}

if(!function_exists('getRoleById')){ 
    function getRoleById($nId){
       $nUserData = User::where('id',$nId)->first();
       return !empty($nUserData->roles[0]->name) ? $nUserData->roles[0]->name : null; 
    }
}

if(!function_exists('generateRoomCode')){ 
    function generateRoomCode()
    {
        $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $sString =  substr(str_shuffle($str_result),0, 7);
        $count = modelname::where('room_code',trim($sString))->count();
        if($count>0){
            generateRoomCode();
        }
        return $sString;
    }
}

if(!function_exists('calculatePrizePool')){ 
    function calculatePrizePool($nEntryFee,$nRoomSize)
    {
        $dTotalValue = $nEntryFee * $nRoomSize;
        $nPercentAmount = ($dTotalValue * 10) / 100;
        $dPrizePool = $dTotalValue - $nPercentAmount;
        return round($dPrizePool);
    }
}

if(!function_exists('addZeroPaymentAmountEntry')){
    function addZeroPaymentAmountEntry(array $input)
    {
        $roleId = $input['role_id'] ?? null;

        if(empty($roleId) && isset($input['role'])){
            if($input['role'] == "Pashumitra"){
                $roleId = 8;
            }elseif($input['role'] == "Registered-vet"){
                $roleId = 7;
            }elseif($input['role'] == "Animal-owner"){
                $roleId = 6;
            }elseif($input['role'] == "Other"){
                $roleId = 13;
            }
        }

        $moduleDetails = $input['module_details'] ?? '';
        if(is_array($moduleDetails)){
            $moduleDetails = json_encode($moduleDetails);
        }elseif(empty($moduleDetails) && isset($input['name']) && isset($input['mobile_number'])){
            $moduleDetails = json_encode([
                'name' => $input['name'],
                'mobile_number' => $input['mobile_number'],
            ]);
        }

        return Payments::create([
            'role_id' => $roleId,
            'user_id' => $input['user_id'] ?? null,
            'payment_id' => $input['payment_id'] ?? 0,
            'order_id' => $input['order_id'] ?? '',
            'module_type_id' => $input['module_type_id'] ?? null,
            'status' => $input['status'] ?? 1,
            'payment_date' => $input['payment_date'] ?? date("Y-m-d H:i:s"),
            'payment_response' => $input['payment_response'] ?? '',
            'payment_request' => $input['payment_request'] ?? '',
            'amount' => 0,
            'type' => $input['type'] ?? null,
            'module_details' => $moduleDetails,
        ]);
    }
}

	//sending push notifications to mobile devices
	function sendNotifications_old($input)
	{ 
		if($input['fcm_token']!=''){
			$tokens = array($input['fcm_token']);
			$body 	= $input['message'];
			$title	= $input['title'];
			
				$data = [
				"notification" => [
					"body"  => $body,
					"title" => $title,
				
				],
				"priority" =>  "high",
				"data" => [
					//"click_action"  =>  "FLUTTER_NOTIFICATION_CLICK",
					"id"            =>  "1",
					"status"        =>  "done",
					"info"          =>  [
						"title"  => $title,
					]
				],
				"to" => $input['fcm_token']
			];
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
			curl_setopt($ch, CURLOPT_POST, 1);

			$headers = array();
			$headers[] = 'Content-Type: application/json';
			$headers[] = 'Authorization: key=AAAA3VOatmM:APA91bH0smliP78ILBQ96TDyZvZoTkCaWQOZaBhMXROKgEXlmUjdJIkEHqFbk5B5zET51aFicM_tdH72oFtml_fkPPkuWR2ARpWFOnkVOne_LWlQ12sUuQ5t5UhWzx90dDMW0kqh5XAK';
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

			$result = curl_exec($ch);
			curl_close ($ch);
			
			if ($result === FALSE) {
				//echo 'Android: Curl failed: ' . curl_error($ch);
			}
			// Close connection
			curl_close($ch);
			return $result;
		}
	}
	
	
		//sending push notifications to mobile devices
if(!function_exists('sendNotifications')){
    function sendNotifications($input)
    {
        $fcmToken = $input['fcm_token'] ?? null;
        if (empty($fcmToken)) {
            return false;
        }

        $title = $input['title'] ?? '';
        $body = $input['message'] ?? '';
        $dataPayload = $input['data'] ?? [];

        try {
            $messaging = Firebase::messaging();
            $tokens = is_array($fcmToken) ? $fcmToken : [$fcmToken];
            $tokens = array_filter($tokens);
            if (empty($tokens)) {
                return false;
            }

            $notification = Notification::create($title, $body);
            $results = [];

            foreach ($tokens as $token) {
                $message = CloudMessage::withTarget('token', $token)
                    ->withNotification($notification);

                if (!empty($dataPayload) && is_array($dataPayload)) {
                    $message = $message->withData($dataPayload);
                }

                $results[] = $messaging->send($message);
            }

            return $results;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
