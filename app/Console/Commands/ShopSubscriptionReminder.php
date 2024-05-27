<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Notifications;
use Carbon\Carbon;
use App\Models\Shops;
use Illuminate\Http\Request;
use DB;
use App\Repositories\Interfaces\User\UserRepositoryInterface;

class ShopSubscriptionReminder extends Command
{
   
    protected $userRepository;
   /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:shopsubscription';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'shopsubscription end reminder';

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
		 
		$results = Shops::leftJoin('users', 'users.id', '=', 'shops.user_id')
								->leftJoin('subcategories', 'subcategories.id', '=', 'shops.sub_category')
								->select('subcategories.name as subcategory_name','shops.id','shops.shop_name',
								'shops.shop_owner_name','shops.mobile_number','users.fcm_id')
								->where( 'shops.subscriptionEndDate', '=', $date)
								->where( 'shops.status',1)
								->get();
								
		 
		$title = 'Reminder: Shop Listing Expiry';
		if($results)
		{
			foreach($results as $row)
			{ 
				$insertArray[] = '';
				$name = $row->shop_name; 
				$owner_name = $row->shop_owner_name ;
				$send_message = 'Your Shop listing for '.$name.' is expiring in 2 days. Renew your subscription now to continue enjoying our services.
 Shop Details:
 *Shop Name: '.$name.'
 *Type: '.$row->subcategory_name.'
 *Shop Name: '.$owner_name.'
 *Shop Contact: '.$row->mobile_number;
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
