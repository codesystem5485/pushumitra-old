<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Notifications;
use Carbon\Carbon;
use App\Models\MilkCollections;
use Illuminate\Http\Request;
use DB;
use App\Repositories\Interfaces\User\UserRepositoryInterface;

class MilkCollectionsSubscriptionReminder extends Command
{
   
    protected $userRepository;
   /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:milkcollectionsubscription';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'milkcollectionsubscription end reminder';

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
		 
		$results = MilkCollections::leftJoin('users', 'users.id', '=', 'milkcollection_centers.user_id')
								->select('milkcollection_centers.id','milkcollection_centers.milkcollection_center_name',
								'milkcollection_centers.incharge_name','milkcollection_centers.mobile_number','users.fcm_id')
								->where( 'milkcollection_centers.subscriptionEndDate', '=', $date)
								->where( 'milkcollection_centers.status',1)
								->get();
								
		 
		$title = 'Reminder: Milk Collection Centre Listing Expiry';
		if($results)
		{
			foreach($results as $row)
			{ 
				$insertArray[] = '';
				$name = $row->milkcollection_center_name; 
				$owner_name = $row->incharge_name ;
				$send_message = 'Your Milk Collection Centre listing for '.$name.' is expiring in 2 days. Renew your subscription now to continue enjoying our services.
 Milk Collection Centre Details:
 Milk Collection Centre Name: '.$name.'
 Incharge Name: '.$owner_name.'
 Incharge Contact: '.$row->mobile_number;
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
