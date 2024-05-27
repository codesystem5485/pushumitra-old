<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\User;
use App\Models\Notifications;
use Carbon\Carbon;
use App\Models\Breeder;
use Illuminate\Http\Request;
use DB;
use App\Repositories\Interfaces\User\UserRepositoryInterface;

class BreederSubscriptionReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:breedersubscription';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'breedersubscription end reminder';

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
		 
		$results = Breeder::leftJoin('species', 'species.id', '=', 'breeders.species')
							->leftJoin('users', 'users.id', '=', 'breeders.user_id')
								->select('breeders.*','users.fcm_id','species.specie as species_name')
								//->where( 'breeders.subscriptionEndDate', '=', $date)
								 ->where(DB::raw("(DATE_FORMAT(breeders.subscriptionEndDate,'%Y-%m-%d'))"), "=", $date)
								->where( 'breeders.status',1)
								->get();
								
		$module = 'Breeder';
		$title = 'Reminder: Breeder Listing Expiry';
		if($results)
		{
			foreach($results as $row)
			{
				$breeder_name = $row->breeder_name;
				$breed = $row->animal_breed;
				$species_name =  $row->species_name;
				$age =  $row->age;	
				$send_message = 'Your Breeder listing for '.$breeder_name.' is expiring in 2 days. Renew your subscription now to continue enjoying our services.
  Breeder Details:
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
