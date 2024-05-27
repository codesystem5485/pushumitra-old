<?php

namespace App\Http\Controllers\Crons;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Session;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use App\Repositories\Interfaces\Rxreminder\RxreminderRepositoryInterface;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\User;
use App\Models\Notifications;
use Carbon\Carbon;

class NotificationController extends BaseController
{
	protected $userRepository;
    protected $rxreminderRepo;
	
    public function __construct(RxreminderRepositoryInterface $rxreminderRepo, UserRepositoryInterface $userRepository){

        $this->userRepo = $userRepository;
       
        $this->rxreminderRepo = $rxreminderRepo;
    }
	
	public function getNotificationsToSend()
	{
		$todayDate = date("Y-m-d");
		//echo $date = $todayDate->addDays(1);exit;
		
		$todayDate1 = Carbon::createFromFormat('Y-m-d', $todayDate);
	//	$date = $todayDate1->addDays(1);
		$newDate = date("Y-m-d",strtotime($todayDate1));

		
		$notifications = Notifications::leftJoin('users', 'users.id', '=', 'notifications.sender_user_id')
								->select('notifications.*','users.id','users.fcm_id','notifications.id as notification_id')
								->where( 'scheduled_date', '=', $newDate)
								->where( 'type', 1)
								->where('send_flag',0)
								//->where('sender_user_id',104)
								->get();
								
			//		echo '<pre>'; print_r($notifications);exit;
								
		if($notifications)
		{
			foreach($notifications as $row)
			{
				$userFcmToken = $row->fcm_id;
				$message = $row->message;
				$title = $row->title;
		$notificationId = $row->notification_id;
				
				$sendArray = array(
					'fcm_token'=> $userFcmToken,
					'message' =>$message,
					'title'=>$title
				);
				
				//send notifications
				sendNotifications($sendArray);
				
				//update send info in notifications
				$updateArray = array(
					'send_flag'=>1,
					'send_date'=>$todayDate,
					
				);
				
				$update = Notifications::where('id',$notificationId)->update($updateArray);
			
				
			}
		}
	}
	
	public function getPaymentNotificationsToSend()
	{
		DB::enableQueryLog();
		$todayDate = date("Y-m-d");
		//echo $date = $todayDate->addDays(1);exit;
		
		$todayDate1 = Carbon::createFromFormat('Y-m-d', $todayDate);
		$date = $todayDate1->addDays(1);
		$newDate = date("Y-m-d",strtotime($date));

		
		$notifications = Notifications::leftJoin('users', 'users.id', '=', 'notifications.sender_user_id')
								->select('notifications.*','users.id','users.fcm_id')
								->where( 'scheduled_date', '=', $newDate)
								->where( 'type', 2)
								->where('send_flag',0)
								->get();
								
		if($notifications)
		{
			foreach($notifications as $row)
			{
				$userFcmToken = $row->fcm_id;
				$message = $row->message;
				$title = $row->title;
				$notificationId = $row->id;
				
				$sendArray = array(
					'fcm_token'=> $userFcmToken,
					'message' =>$message,
					'title'=>$title
				);
				
				//send notifications
				sendNotifications($sendArray);
				
				//update send info in notifications
				$updateArray = array(
					'send_flag'=>1,
					'send_date'=>$todayDate,
					
				);
				
				$update = Notifications::where('id',$row->id)->update($updateArray);
				
			}
		}
	}
}
