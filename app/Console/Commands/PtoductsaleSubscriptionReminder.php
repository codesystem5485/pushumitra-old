<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Notifications;
use Carbon\Carbon;
use App\Models\ProductForSale;
use Illuminate\Http\Request;
use DB;
use App\Repositories\Interfaces\User\UserRepositoryInterface;

class PtoductsaleSubscriptionReminder extends Command
{
   
    protected $userRepository;
   /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:productsalesubscription';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'productsalesubscription  end reminder';

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
		 
		$results = ProductForSale::leftJoin('users', 'users.id', '=', 'product_for_sales.user_id')
								->select('product_for_sales.*','users.fcm_id')
								->where( 'product_for_sales.subscriptionEndDate', '=', $date)
								->where( 'product_for_sales.status',1)
								->get();
								
		$module = 'Product For Sale';
		$title = 'Reminder: Product For Sale Listing Expiry';
		if($results)
		{
			foreach($results as $row)
			{
				$insertArray[] = '';
				$product_name = $row->product_name;
				$price = $row->price;
				$send_message = 'Your Product for sale listing for '.$product_name.' is expiring in 2 days. Renew your subscription now to continue enjoying our services.
  Product Details:
 Product Name: '.$product_name.'
 Price: '.$price.'
 Product Owner Name: '.$row->contact_number_of_owner.'
 Product Owner Contact: '.$row->contact_name_of_owner;
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
