<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Notifications;
use Carbon\Carbon;
use App\Models\Veterinaryhospitals;
use Illuminate\Http\Request;
use DB;
use App\Repositories\Interfaces\User\UserRepositoryInterface;

class VetHospitalsSubscriptionReminder extends Command
{
   
    protected $userRepository;
   /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:vethospitalssubscription';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'vethospitalssubscription end reminder';

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
		 
		$results = Veterinaryhospitals::leftJoin('users', 'users.id', '=', 'veterinary_hospitals.user_id')
								->leftJoin('subcategories', 'subcategories.id', '=', 'veterinary_hospitals.sub_category')
								->select('subcategories.name as subcategory_name','veterinary_hospitals.id','veterinary_hospitals.hospital_name',
								'veterinary_hospitals.veterinary_owner_name','veterinary_hospitals.mobile_number','users.fcm_id')
								->where( 'veterinary_hospitals.subscriptionEndDate', '=', $date)
								->where( 'veterinary_hospitals.status',1)
								->where( 'veterinary_hospitals.type','Private')
								->get();
								
		 
		$title = 'Reminder: Vet Hospitals Listing Expiry';
		if($results)
		{
			foreach($results as $row)
			{
				$insertArray[] = '';
				$name = $row->hospital_name; 
				$owner_name = $row->veterinary_owner_name;
				$send_message = 'Your Vet Hospitals listing for '.$name.' is expiring in 2 days. Renew your subscription now to continue enjoying our services.
 Vet Hospitals Details:
 Vet Hospital Name: '.$name.'
 Type: '.$row->subcategory_name.'
 Owner Name: '.$owner_name.'
 Owner Contact: '.$row->mobile_number;
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
