<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Notifications;
use Carbon\Carbon;
use App\Models\Institutions;
use Illuminate\Http\Request;
use DB;
use App\Repositories\Interfaces\User\UserRepositoryInterface;

class InstitutionSubscriptionReminder extends Command
{
   
    protected $userRepository;
   /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:institutionsubscription';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'institutionsubscription end reminder';

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
		 
		$results = Institutions::leftJoin('users', 'users.id', '=', 'institutions.user_id')
								->leftJoin('subcategories', 'subcategories.id', '=', 'institutions.sub_category')
								->select('subcategories.name as subcategory_name','institutions.id','institutions.institution_name',
								'institutions.incharge_name','institutions.mobile_number','users.fcm_id')
								->where( 'institutions.subscriptionEndDate', '=', $date)
								->where( 'institutions.status',1)
								->where( 'institutions.type','Private')
								->get();
								
		 
		$title = 'Reminder: Institution Listing Expiry';
		if($results)
		{
			foreach($results as $row)
			{ 
				$insertArray[] = '';
				$name = $row->institution_name; 
				$owner_name = $row->incharge_name ;
				$send_message = 'Your Institution listing for '.$name.' is expiring in 2 days. Renew your subscription now to continue enjoying our services.
 Institution Details:
 Institution Name: '.$name.'
 Type: '.$row->subcategory_name.'
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
