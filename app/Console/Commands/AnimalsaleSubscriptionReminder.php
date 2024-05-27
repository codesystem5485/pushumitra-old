<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Notifications;
use Carbon\Carbon;
use App\Models\AnimalForSale;
use Illuminate\Http\Request;
use DB;
use App\Repositories\Interfaces\User\UserRepositoryInterface;

class AnimalsaleSubscriptionReminder extends Command
{
   
    protected $userRepository;
   /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:animalsalesubscription';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'animalsalesubscription end reminder';

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
		 
		$results = AnimalForSale::leftJoin('species', 'species.id', '=', 'animal_for_sales.species')
								 ->leftJoin('users', 'users.id', '=', 'animal_for_sales.user_id')
								->select('animal_for_sales.*','users.fcm_id','species.specie as species_name')
								->where( 'animal_for_sales.subscriptionEndDate', '=', $date)
								->where( 'animal_for_sales.status', 1)
								->get();
								
		$module = 'Animal For Sale';
		$title = 'Reminder: Animal For Sale Listing Expiry';
		if($results)
		{
			foreach($results as $row)
			{ 
				$breed =  $row->breed;
				$species_name =  $row->species_name;
				$age =  $row->age;	
$uidnumber = $row->UID_number;				
				$send_message = 'Your animal for sale listing '.$uidnumber.' is expiring in 2 days. Renew your subscription now to continue enjoying our services.
  Animal Details:
 *species: '.$species_name.'
 *Age: '.$age.'
 *Breed: '.$breed;
 
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
