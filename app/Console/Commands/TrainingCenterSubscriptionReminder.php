<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Notifications;
use Carbon\Carbon;
use App\Models\TrainingCenters;
use Illuminate\Http\Request;
use DB;
use App\Repositories\Interfaces\User\UserRepositoryInterface;

class TrainingCenterSubscriptionReminder extends Command
{
   
    protected $userRepository;
   /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:trainingcentresubscription';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'trainingcentresubscription end reminder';

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
		 
		$results = TrainingCenters::leftJoin('users', 'users.id', '=', 'training_centers.user_id')
								->leftJoin('subcategories', 'subcategories.id', '=', 'training_centers.sub_category')
								->select('subcategories.name as subcategory_name','training_centers.id','training_centers.training_center_name',
								'training_centers.incharge_name','training_centers.mobile_number','users.fcm_id')
								->where( 'training_centers.subscriptionEndDate', '=', $date)
								->where( 'training_centers.status',1)
								->where( 'training_centers.type','Private')
								->get();
								
		 
		$title = 'Reminder: Training Centre Listing Expiry';
		if($results)
		{
			foreach($results as $row)
			{
				$insertArray[] = '';
				$name = $row->training_center_name; 
				$owner_name = $row->incharge_name ;
				$send_message = 'Your Training Centre listing for '.$name.' is expiring in 2 days. Renew your subscription now to continue enjoying our services.
 Training Centre Details:
 *Training Centre Name: '.$name.'
 *Type: '.$row->subcategory_name.'
 *Incharge Name: '.$owner_name.'
 *Incharge Contact: '.$row->mobile_number;
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
