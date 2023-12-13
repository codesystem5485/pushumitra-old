<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;
use Session;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use App\Repositories\Interfaces\Rxreminder\RxreminderRepositoryInterface;
use Auth;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\Rxreminder;
use Validator;
use App\Models\User;
use App\Models\Animals;

class RxreminderController extends BaseController
{
	protected $userRepository;
    protected $rxreminderRepo;
	
    public function __construct(RxreminderRepositoryInterface $rxreminderRepo, UserRepositoryInterface $userRepository){

        $this->userRepo = $userRepository;
       
        $this->rxreminderRepo = $rxreminderRepo;
    }
	
	public function addRxreminder(Request $request){
		
		$postData = request()->all();
		$validator = Validator::make($postData, [
				'animal_owner_id' => 'required',
				'animal_id' => 'required',
				//'UID_number' => 'required',
				'prescription' => "required",
				'description' => "required",
				//'scheduled_date' => 'required',
				//'scheduled_message' => 'required',
				'user_id' => 'required',
				'role'=>'required'
			]);
			
		if ($validator->fails())
		{
			return $this->sendError([],implode(',',$validator->errors()->all()),400);
		}
		 $response = [];
		
		DB::beginTransaction();
        try{ 
		
			$aInsertData = $request->all();
			$roleId = null;
			
			if($aInsertData['role']=="Pashumitra"){
				$roleId = 8;
			}elseif($aInsertData['role']=="Registered-vet")
			{
				$roleId = 7;
			}
			elseif($aInsertData['role']=="Animal-owner")
			{
				$roleId = 6;
			}
		
			$response = [];		
            
			$aInsertData['role_id'] =$roleId;
            $rxreminder = $this->rxreminderRepo->create($aInsertData);
			
			//add to notifications
			$aInsertData['sender_user_id'] = $aInsertData['animal_owner_id'];
			$aInsertData['rx_reminder_id'] = $rxreminder->id;
			$aInsertData['title'] = "Rx Reminder";
			$notifications = $this->rxreminderRepo->addReminderToNotifications($aInsertData);

            DB::commit();
            ## Store log
            $message = trans('messages.rxreminder_create',['name' => $request->UID_number]);
            storeActicityLog(trans('messages.rxreminder_create'),$message,$request->user_id,$rxreminder);
			
           return $this->sendResponse($response,trans('messages.rxreminder_create'),200);
        }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,$request->user_id);
           
            return  $this->sendError($response,trans('messages.something'),500);
        }
	}
	
	public function getAnimalOwnerList(Request $request)
	{
		$input['sRoleName'] = 'Animal-Owner';
		/*$getUsers =  User::whereHas('roles', function($q) use($input) {
            if(!empty($input['sRoleName'])){
                $q->where('name', $input['sRoleName']);
            }
        })
		->select('id', 'full_name','mobile_number')
		->orderBy('id', 'DESC')
		->get();*/
		
		$getUsers =  User::select('id', 'full_name','mobile_number')
		->where('is_active',1)
		->orderBy('id', 'DESC')
		->get();
		
		$response = [];
		$response = $getUsers;
		return $this->sendResponse($response,'',200);
	}
	
	public function getAnimalNameList(Request $request)
	{
		$postData = request()->all();
		$validator = Validator::make($postData, [
				'animal_owner_id' => 'required',
			]);
			
		if ($validator->fails())
		{
			return $this->sendError([],implode(',',$validator->errors()->all()),400);
		}
		
		$response = [];
		$getAnimals =  Animals::select('id', 'name','UID_number')
					->where('animal_owner',$postData['animal_owner_id'])
					->where('is_active',1)
					->orderBy('id', 'DESC')
					->get();
					
		$response = $getAnimals;
		
		return $this->sendResponse($response,'',200);
	}
	
	public function getRxReminderHistory(Request $request)
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
		$animals = Rxreminder::leftJoin('animals', 'animals.id', '=', 'rx_reminders.animal_id')
			->leftJoin('breeds', 'breeds.id', '=', 'animals.breed')
			->leftJoin('species', 'species.id', '=', 'animals.species')
			->select('animals.id','animals.name','breeds.breed as breed_name','species.specie as species_name','animals.UID_number',DB::raw('(select image_name from add_animal_images where animal_id  =   animals.id order by id asc limit 1) as image_name'))
			->where('rx_reminders.user_id',$postData['user_id'])->groupBy('animal_id')->get();
		$response['animals'] = $animals;
		$response['animal_image_path'] =  url("/upload/animal/");
		
		return $this->sendResponse($response,'',200);
	}
	
	public function getRxReminderHistoryDetails(Request $request)
	{
		$postData = request()->all();
		$validator = Validator::make($postData, [
				'animal_id' => 'required',
			]);
			
		if ($validator->fails())
		{
			return $this->sendError([],implode(',',$validator->errors()->all()),400);
		}
		
		$response = [];
		$animals = Rxreminder::leftJoin('animals', 'animals.id', '=', 'rx_reminders.animal_id')
			->leftJoin('users', 'users.id', '=', 'rx_reminders.animal_owner_id')
			->leftJoin('breeds', 'breeds.id', '=', 'animals.breed')
			->leftJoin('species', 'species.id', '=', 'animals.species')
			->select('users.full_name as animal_owner_name','breeds.breed as breed_name','species.specie as species_name','rx_reminders.*','animals.id','animals.name','animals.UID_number',
			'animals.UID_number',DB::raw('(select image_name from add_animal_images where animal_id  =   animals.id order by id asc limit 1) as image_name'))
			->where('rx_reminders.animal_id',$postData['animal_id'])
			->where('rx_reminders.user_id',$postData['user_id'])->get();
		$response['animals'] = $animals;
		$response['animal_image_path'] =  url("/upload/animal/");
		
		return $this->sendResponse($response,'',200);
	}
	
}
