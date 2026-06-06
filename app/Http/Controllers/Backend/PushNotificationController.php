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
use App\Helper\FirebaseHelper;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
// use Kreait\Firebase\Messaging\MulticastMessage;
use Kreait\Laravel\Firebase\Facades\Firebase;
use App\Jobs\SendPushNotificationJob;

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
	
	public function sendNotificationsold(Request $request)
	{
	    ini_set('memory_limit', '3000M');
        ini_set('max_execution_time', '2000');
       set_time_limit(2000);
	     $this->validate($request, [
            'role' => 'required', 
			'title' => 'required', 	
			'message' => 'required', 	
        ]);
        
		$sRoleName = $request->role;
		$fcmArray = $this->userRepo->getUsersFcmIds(['sRoleName' => $sRoleName]);
    //	$fcmArray = DB::table('users')->whereIn('id', array(90,104,96))->get();
  	
		$notifications = new Notifications();
		$notifications->title = $request->title;
		$notifications->message = $request->message;
		$notifications->show_role = $request->role;
		$notifications->type = 3;
		$notifications->send_flag = 1;
		$notifications->scheduled_date= date("Y-m-d");
		$notifications->send_date = date("Y-m-d");
		$notifications->save();
			
		$sendFcmArray =array();
		foreach($fcmArray as $row)
		{
		  $fcmId = $row->fcm_id;
	 
		
		/*DB::beginTransaction();
        try{*/
			//insert to notifications
			
		
	  $body 	= $request->message;
			$title	= $request->title;
					$sendFcmArray =array();
				$data = [
					//"registration_ids"=>$fcmId,
					"to"=>$fcmId,
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
					'Android: Curl failed: ' . curl_error($ch);
				}
				// Close connection
				curl_close($ch); 
		 	}
		 
			
			$message = 'Push notification send successfully';
            storeActicityLog('Push notification',$message,Auth::user(),$notifications);
			 Session::flash('success', 'Push notifications send successfully');
            return redirect()->route('sendnotifications.create');    
       /* }catch(\Exception $e){ 
            DB::rollback();
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('sendnotifications.create'); 
        }*/
	}
	
    public function sendNotifications_new31dec2025(Request $request)
    {
        
        ini_set('memory_limit', '3000M');
        ini_set('max_execution_time', '2000');
        set_time_limit(2000);
    
        $this->validate($request, [
            'role'    => 'required',
            'title'   => 'required',
            'message' => 'required',
        ]);
    
        $title = $request->title;
        $body  = $request->message;
        $role  = $request->role;
       
        $fcmArray = $this->userRepo->getUsersFcmIds(['sRoleName' => $role]);
    
        if (empty($fcmArray)) {
            Session::flash('error', 'No users found with FCM tokens.');
            return redirect()->route('sendnotifications.create');
        }
        
        
        // Save notification
        $notifications = new Notifications();
        $notifications->title = $title;
        $notifications->message = $body;
        $notifications->show_role = $role;
        $notifications->type = 3;
        $notifications->send_flag = 1;
        $notifications->scheduled_date = date('Y-m-d');
        $notifications->send_date = date('Y-m-d');
        $notifications->save();
    
        $accessToken = FirebaseHelper::getAccessToken();
        $projectId = env('FIREBASE_PROJECT_ID');
    
        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";
    
        $success = 0;
        $failed  = 0;
    
        foreach ($fcmArray as $row) {
    
            if (empty($row->fcm_id)) {
                $failed++;
                continue;
            }
    
            $payload = [
                "message" => [
                    "token" => $row->fcm_id,
                    "notification" => [
                        "title" => $title,
                        "body"  => $body,
                    ],
                    "data" => [
                        "title" => $title,
                        "message" => $body,
                    ],
                ]
            ];
    
            $headers = [
                "Authorization: Bearer {$accessToken}",
                "Content-Type: application/json",
            ];
    
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
                 dd($response);   

            if ($httpCode === 200) {
                $success++;
            } else {
                $failed++;
                Log::error('FCM Error', [
                    'fcm_id' => substr($row->fcm_id, 0, 20),
                    'response' => $response
                ]);
            }
        }
    
        storeActicityLog(
            'Push notification',
            "Success: {$success}, Failed: {$failed}",
            Auth::user(),
            $notifications
        );
    
        Session::flash(
            $success ? 'success' : 'error',
            "Push notification completed. Success: {$success}, Failed: {$failed}"
        );
    
        return redirect()->route('sendnotifications.create');
    }
    	
    public function sendNotificationsnotinuse(Request $request)
	{
	    ini_set('memory_limit', '3000M');
        ini_set('max_execution_time', '2000');
       set_time_limit(2000);
		 $this->validate($request, [
        'role' => 'required', 
        'title' => 'required', 	
        'message' => 'required', 	
    ]);
    
    $sRoleName = $request->role;
    $fcmIds = $this->userRepo->getUsersFcmIds(['sRoleName' => $sRoleName]);
    
    if (empty($fcmIds)) {
        Session::flash('error', 'No FCM tokens found');
        return redirect()->route('sendnotifications.create');
    }
    
    $notifications = new Notifications();
    $notifications->title = $request->title;
    $notifications->message = $request->message;
    $notifications->show_role = $request->role;
    $notifications->type = 3;
    $notifications->send_flag = 1;
    $notifications->scheduled_date = date("Y-m-d");
    $notifications->send_date = date("Y-m-d");
    $notifications->save();
    
    $messaging = Firebase::messaging();
    $successcount = 0;
    $failcount = 0;
    $fcmIds = ['egMn8976STeJwNRfj9uCiK:APA91bF6e9X1O6Ct7dwAYqG-tw8Z4e1WSDFlzlMAwPSl2Evv6ZziWMgDcTQ2OWnHpaDLCf76YqzF8DYL8MDRxcMn2SRbwPvJVGCLS3I5qwTLvqMuta9j1b8'];
    foreach (array_chunk($fcmIds, 5) as $tokens) {
        try {
            $message = CloudMessage::new()
                                        ->withNotification([
                                            'title' => 'Hello',
                                            'body' => 'Test message'
                                        ]);
            $report = $messaging->sendMulticast($message, $tokens);
            foreach ($report->failures()->getItems() as $failure) {
                $token = $failure->target()->value();
                $error = $failure->error()->getMessage();
            
                echo "Token: $token <br>";
                echo "Error: $error <br><br>";
            }

            // ✅ Success & failure info
            $successCount = $report->successes()->count();
            $failureCount = $report->failures()->count();
            $successcount += $report->successes()->count();
            $failcount += $report->failures()->count();
        } catch (\Throwable $e) {
            $failcount += count($tokens);
            storeActicityLog('Push notification error', $e->getMessage(), Auth::user());
        }
    }
    
    $message = "Push notification sent! Success: $successcount, Failed: $failcount";
    storeActicityLog('Push notification', $message, Auth::user(), $notifications);
    Session::flash('success', $message);
    return redirect()->route('sendnotifications.create');    
	}
	
	public function sendNotifications(Request $request)
    {
        ini_set('memory_limit', '3000M');
        ini_set('max_execution_time', '2000');
        set_time_limit(2000);
        $this->validate($request, [
            'role' => 'required',
            'title' => 'required',
            'message' => 'required',
        ]);
    
        $sRoleName = $request->role;
        $fcmIds = $this->userRepo->getUsersFcmIds(['sRoleName' => $sRoleName]);
    
        if (empty($fcmIds)) {
            Session::flash('error', 'No FCM tokens found');
            return redirect()->route('sendnotifications.create');
        }
    
        $notifications = new Notifications();
        $notifications->title = $request->title;
        $notifications->message = $request->message;
        $notifications->show_role = $request->role;
        $notifications->type = 3;
        $notifications->send_flag = 1;
        $notifications->scheduled_date = date("Y-m-d");
        $notifications->send_date = date("Y-m-d");
        $notifications->save();
    
        // 🚀 Dispatch job (ASYNC)
        SendPushNotificationJob::dispatch(
            $fcmIds,
            $request->title,
            $request->message,
            $notifications->id
        );
    
        Session::flash('success', 'Notification queued successfully!');
        return redirect()->route('sendnotifications.create');
    }
	
}
