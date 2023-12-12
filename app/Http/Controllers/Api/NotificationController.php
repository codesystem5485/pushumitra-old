<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Session;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use Auth;
use App\Http\Controllers\BaseController as BaseController;
use Validator;
use App\Models\User;
use App\Models\Notifications;

class NotificationController extends BaseController
{
	protected $userRepository;
	
    public function __construct(UserRepositoryInterface $userRepository){

        $this->userRepo = $userRepository;
    }
	
	public function getNotificationList(Request $request)
	{
		$postData = request()->all();
		$validator = Validator::make($postData, [
				'user_id' => 'required',
			]);
			
		if ($validator->fails())
		{
			return $this->sendError([],implode(',',$validator->errors()->all()),400);
		}
		
		$response = [];
		$notifications = Notifications::leftJoin('users', 'users.id', '=', 'notifications.sender_user_id')
			->select('notifications.*')
			->where('notifications.sender_user_id',$postData['user_id'])
			->where('notifications.send_flag',1)->get();
		$response['notifications'] = $notifications;
		
		return $this->sendResponse($response,'',200);
	}
	
	public function getNotificationDetails(Request $request)
	{
		$postData = request()->all();
		$validator = Validator::make($postData, [
				'notification_id' => 'required',
			]);
			
		if ($validator->fails())
		{
			return $this->sendError([],implode(',',$validator->errors()->all()),400);
		}
		
		$response = [];
		$notifications = Notifications::leftJoin('users', 'users.id', '=', 'notifications.sender_user_id')
			->select('notifications.*')
			->where('notifications.id',$postData['notification_id'])->get();
		$response['notifications'] = $notifications;
		
		return $this->sendResponse($response,'',200);
	}
	
}
