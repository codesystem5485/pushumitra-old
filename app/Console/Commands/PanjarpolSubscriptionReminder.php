<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Notifications;
use Carbon\Carbon;
use App\Models\Panjarpol;
use Illuminate\Http\Request;
use DB;
use App\Repositories\Interfaces\User\UserRepositoryInterface;

class PanjarpolSubscriptionReminder extends Command
{
   
    protected $userRepository;
   /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:panjarpolsubscription';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'panjarpolsubscription end reminder';

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
		 
		$results = Panjarpol::leftJoin('users', 'users.id', '=', 'panjarpol.user_id')
								->select('panjarpol.id','panjarpol.panjarpol_name',
								'panjarpol.manager_name','panjarpol.mobile_number','users.fcm_id')
								->where( 'panjarpol.subscriptionEndDate', '=', $date)
								->where( 'panjarpol.status',1)
								->get();
								
		 
		$title = 'Reminder: Panjarpol Listing Expiry';
		if($results)
		{
			foreach($results as $row)
			{ 
				$insertArray[] = '';
				$name = $row->panjarpol_name; 
				$owner_name = $row->manager_name ;
				$send_message = 'Your Panjarpol listing for '.$name.' is expiring in 2 days. Renew your subscription now to continue enjoying our services.
 Panjarpol Details:
 *Panjarpol Name: '.$name.'
 *Manager Name: '.$owner_name.'
 *Manager Contact: '.$row->mobile_number;
			    $user_id =  $row->user_id; 
				$insertArray['userFcmToken'] = $row->fcm_id;
				
				$insertArray['message'] =$send_message;
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
