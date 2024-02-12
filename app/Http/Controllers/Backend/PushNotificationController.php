<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Session;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\User;
use App\Models\Notifications;
use App\Models\SendPushNotifications;
use Auth;
use Carbon\Carbon;

class PushNotificationController extends BaseController
{
	protected $userRepository;
   
	
    public function __construct(UserRepositoryInterface $userRepository){

		$this->middleware('permission:sendnotifications-create', ['only' => ['index','show','create','send']]);

        $this->url = [   
           
            'createUrl' => route('sendnotifications.create')
        ];
        $this->userRepo = $userRepository;
       
    }
	
	public function create(){
        
        return view('backend.send_notifications.create',['url' => $this->url]); 
    }
	
	public function sendNotifications(Request $request)
	{
		$sRoleName = $request->role;
		$fcmArray = $this->userRepo->getUsersFcmIds(['sRoleName' => $sRoleName]);
		$sendFcmArray =array();
		foreach($fcmArray as $row){
				array_push($sendFcmArray,$row->fcm_id);
		}
		DB::beginTransaction();
        try{
			//insert to notifications
			$notifications = new Notifications();
			$notifications->title = $request->title;
			$notifications->message = $request->message;
			$notifications->show_role = $request->role;
			$notifications->type = 3;
			$notifications->send_flag = 1;
			$notifications->scheduled_date= date("Y-m-d");
			$notifications->send_date = date("Y-m-d");
			$notifications->save();
			
			$body 	= $request->message;
			$title	= $request->title;
				
				$data = [
					"registration_ids"=>$sendFcmArray,
					"notification" => [
						"body"  => $body,
						"title" => $title,
					
					],
					"priority" =>  "high",
					"data" => [
						"info"          =>  [
							"title"  => $title,
						]
					],
					
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
			$message = 'Push notification send successfully';
            storeActicityLog('Push notification',$message,Auth::user(),$notifications);
			 Session::flash('success', 'Push notifications send successfully');
            return redirect()->route('sendnotifications.create');    
        }catch(\Exception $e){ 
            DB::rollback();
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('sendnotifications.create'); 
        }
	}
	
	
}
