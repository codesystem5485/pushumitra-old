<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Notifications;
use Carbon\Carbon;
use App\Models\Suppliers;
use Illuminate\Http\Request;
use DB;
use App\Repositories\Interfaces\User\UserRepositoryInterface;

class SupplierSubscriptionReminder extends Command
{
   
    protected $userRepository;
   /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:suppliersubscription';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'suppliersubscription end reminder';

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
		 
		$results = Suppliers::leftJoin('users', 'users.id', '=', 'suppliers.user_id')
								->leftJoin('subcategories', 'subcategories.id', '=', 'suppliers.sub_category')
								->select('subcategories.name as subcategory_name','suppliers.id','suppliers.supplier_name',
								'suppliers.mobile_number','users.fcm_id')
								->where( 'suppliers.subscriptionEndDate', '=', $date)
								->where( 'suppliers.status',1)
								->get();
								
		 
		$title = 'Reminder: Suppliers Listing Expiry';
		if($results)
		{
			foreach($results as $row)
			{
				$insertArray[] = '';
				$name = $row->supplier_name; 
				$send_message = 'Your Supplier listing for '.$name.' is expiring in 2 days. Renew your subscription now to continue enjoying our services.
 Supplier Details:
 *Supplier Name: '.$name.'
 *Type: '.$row->subcategory_name.'
 *Suppliers Contact: '.$row->mobile_number;
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
