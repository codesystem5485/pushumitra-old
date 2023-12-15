<?php

namespace App\Http\Controllers\Crons;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Session;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use App\Repositories\Interfaces\Rxreminder\RxreminderRepositoryInterface;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\User;
use App\Models\Notifications;
use Carbon\Carbon;

class NotificationController extends BaseController
{
	protected $userRepository;
    protected $rxreminderRepo;
	
    public function __construct(RxreminderRepositoryInterface $rxreminderRepo, UserRepositoryInterface $userRepository){

        $this->userRepo = $userRepository;
       
        $this->rxreminderRepo = $rxreminderRepo;
    }
	
	public function getNotificationsToSend()
	{
		DB::enableQueryLog();

		$todayDate = date("Y-m-d");
		
		$notifications = Notifications::leftJoin('users', 'users.id', '=', 'notifications.sender_user_id')
								//->whereDate('scheduled_date', '=', $todayDate)
								->where( 'scheduled_date', '>', Carbon::now()->subDays(1))
								->where('send_flag',0)
								->get();
								
		print_r(DB::getQueryLog());exit;
								
		if($notifications)
		{
			foreach($notifications as $row){
				$userFcmToken = $row->fcm_id;
				$message = $row->message;
				$title = $row->title;
				
			}
		}
	}
}
