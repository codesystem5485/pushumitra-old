<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Notifications;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;
use App\Repositories\Interfaces\User\UserRepositoryInterface;

class PashumitraSubscriptionReminder extends Command
{
   
    protected $userRepository;
   /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:pashumitrasubscription';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'pashumitrasubscription end reminder';

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
       $input[] ='';
		$input['sRoleName'] ='Pashumitra';
		$todayDate = date("Y-m-d");
		$todayDate1 = Carbon::createFromFormat('Y-m-d', $todayDate);
		$date = date("Y-m-d",strtotime($todayDate1->addDays(2)));
		
		$results = User::with(['roles'])
		->select('fcm_id','full_name','subscriptionEndDate','pm_code')
        ->whereHas('roles', function($q) use($input) {
            if(!empty($input['sRoleName'])){
                $q->where('name', 'Pashumitra');
            }
        })
        ->where('is_active',1)
		->where('fcm_id','!=','')
		->where( 'subscriptionEndDate', '=', $date)
        ->orderBy('id', 'DESC')
        ->get();
		
		$subscriptionEndDate = date("d-M-Y",strtotime($date));
		$title = 'Reminder: 1-Year Membership Renewal';
		if($results)
		{
			foreach($results as $row)
			{ 
			    $insertArray[] = '';
				$send_message = 'Dear '.$row->full_name.',

Your 1-year membership with PashuMitra is set to expire soon. Renew now to continue enjoying uninterrupted access to all our features and services.

*Membership Details:*
- *Expiry Date:* '.$subscriptionEndDate.'
- *Membership ID:* '.$row->pm_code.'

Renew your membership today to keep benefiting from our platform.

Best regards,  
The PashuMitra Team';
			  
				$insertArray['userFcmToken'] = $row->fcm_id;
				$insertArray['message'] =$send_message;
				$insertArray['title'] =$title;
				$insertArray['scheduled_date']=$todayDate;
				$insertArray['sender_user_id']=$row->id;
				$insertArray['type']=5;
				$insertArray['send_flag']=1;
				$insertArray['rx_reminder_id']=0;
				$insertArray['send_date']=$todayDate;
			 
				$notifications = $this->userRepo->sendRenewReminderNotifications($insertArray);
			}
		}
    }
}
