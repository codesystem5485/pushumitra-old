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
		$rolename = '';
		$getRoles = DB::table('model_has_roles')
						->where('model_id',$postData['user_id'])
						->first();
			
			if($getRoles->role_id == "8"){
				$rolename = "Pashumitra";
			}elseif($getRoles->role_id == "7")
			{
				$rolename = 'Registered-vet';
			}
			elseif($getRoles->role_id == "6")
			{
				$rolename = 'Animal-owner';
			}
			
			$query = Notifications::leftJoin('users', 'users.id', '=', 'notifications.sender_user_id')
			->select('notifications.*')
			->where(function($query) use ($rolename,$postData){
                            $query->where(function($query) use ($rolename){
                                 $query->where('type','3')
								 ->where('notifications.show_role',$rolename);
                             })
							 ->orWhere(function($query) use ($postData){
                                 $query->where('type','<','3')
								 ->where('notifications.sender_user_id',$postData['user_id']);
                             });
                         })
				//->where('notifications.sender_user_id',$postData['user_id'])
			->where('notifications.send_flag',1)->get();
			
			
		$response['notifications'] = $query;
		//$response['notifications']= array_merge($notifications,$notifications_role);
		
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
		$notifications = Notifications::select('notifications.*')
			->where('notifications.id',$postData['notification_id'])->first();
			if($notifications){
				if($notifications->read_flag==0){
					$array = array('read_flag'=> 1);
					$update = Notifications::where('id',$notifications->id)->update($array);
				}
					
			}
		$response['notifications'] = $notifications;
		return $this->sendResponse($response,'',200);
	}
	
}
