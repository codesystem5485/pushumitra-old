<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Notifications;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;

class RxreminderCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rxreminder:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
		$todayDate = date("Y-m-d");
		//echo $date = $todayDate->addDays(1);exit;
	//	$todayDate ='2024-01-20';
		
		$todayDate1 = Carbon::createFromFormat('Y-m-d', $todayDate);
		$date = $todayDate1->addDays(1);
		$newDate = date("Y-m-d",strtotime($date));

		
		$notifications = Notifications::leftJoin('users', 'users.id', '=', 'notifications.sender_user_id')
								->select('notifications.*','users.id','users.fcm_id','notifications.id as notification_id')
								->where( 'scheduled_date', '=', $newDate)
								->where( 'type', 1)
								->where('send_flag',0)
								//->where('sender_user_id',104)
								->get();
								
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
}
