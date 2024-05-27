<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Notifications;
use Carbon\Carbon;
use App\Models\Transporters;
use Illuminate\Http\Request;
use DB;
use App\Repositories\Interfaces\User\UserRepositoryInterface;

class TransporterSubscriptionReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:transportersubscription';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'transportersubscription end reminder';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
		$this->userRepo = $userRepository;
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
		$todayDate1 = Carbon::createFromFormat('Y-m-d', $todayDate);
		$date = date("Y-m-d",strtotime($todayDate1->addDays(2))); 
		 
		$results = Transporters::leftJoin('users', 'users.id', '=', 'transporters.user_id')
								->select('transporters.*','users.fcm_id')
								->where( 'transporters.subscriptionEndDate', '=', $date)
								 //->where(DB::raw("(DATE_FORMAT(transporters.subscriptionEndDate,'%Y-%m-%d'))"), "=", $date)
								->where( 'transporters.status',1)
								->get();
								
		$module = 'Transporter';
		$title = 'Reminder: Transporter Listing Expiry';
		if($results)
		{
			foreach($results as $row)
			{
				$transporter_name = $row->transporter_name;
				$vehicle_name= $row->vehicle_name;
				$send_message = 'Your Transporter listing for '.$transporter_name.' is expiring in 2 days. Renew your subscription now to continue enjoying our services.
  Transporter Details:
 *Name: '.$transporter_name.'
 *Vehicle name: '.$vehicle_name;
 
				$insertArray[] = '';
			    $user_id =  $row->user_id; 
				$insertArray['userFcmToken'] = $row->fcm_id;
				
				$insertArray['message'] = $send_message;
				$insertArray['title'] =$title;
				$insertArray['scheduled_date']=$todayDate;
				$insertArray['sender_user_id']=$row->user_id;
				$insertArray['type']=5;
				$insertArray['send_flag']=1;
				$insertArray['rx_reminder_id']=0;
				$insertArray['send_date']=$todayDate;
			 
				$notifications = $this->userRepo->sendRenewReminderNotifications($insertArray);
			}
		}
    }
}
