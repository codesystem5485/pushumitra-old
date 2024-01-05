<?php

namespace App\Repositories\Implementation\Breeder;

use App\Base\BaseRepository;
use App\Models\Breeder;
use App\Repositories\Interfaces\Breeder\BreederRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use DB;
use DataTables;
use Spatie\Activitylog\Models\Activity;
use App\Models\Fee;
use App\Models\Notifications;

class BreederRepository  extends BaseRepository implements BreederRepositoryInterface
{
    /**
     * @var breeder sale
     */
    protected $breederModel; 

    /**
     * breeder Repository constructor.
     *
     * @param User $breederModel
     */
    public function __construct(Breeder $breederModel)
    {
        parent::__construct($breederModel);
        $this->breederModel = $breederModel;
    }

    /**
     * {@inheritDoc}
     */
    public function getBreeder()
    {     
        return  $this->breederModel
            ->orderBy('id', 'DESC')
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getSingleBreeder(int $breederId)
    {
        return  $this->breederModel->findOrFail($breederId);
    }

    /**
     * {@inheritDoc}
     */
    public function updateBreeder($breederId, $request = []) 
    {
        DB::beginTransaction();
        try {
            $breeder =  $this->breederModel->find($breederId);
            $breeder->update($request);
            DB::commit();
            return true;
        } catch (\Exception $e) {  
            DB::rollback();
            return false;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function deleteBreeder(int $breederId)
    { 
        try{
            $breeder =  $this->breederModel->findOrFail($breederId);
            return $breeder->delete();
        } catch (\Exception $e) {  
            DB::rollback();
            return false;
        }
    }

    public function with(array $input)
    {
        return  $this->breederModel->with($input)->orderBy('id', 'ASC')
        ->get();
    }
	
	//get breeder subscriptions date 
	public function getSubscriptionDates(array $input)
	{
		$subscriptionStartDate = date("Y-m-d");
		$subscriptionEndDate = '';
		$feeDetails = Fee::where('id',$input['type'])->first();
		if($feeDetails){
			$months =$feeDetails->valid_months;
			$subscriptionEndDate = date('Y-m-d', strtotime($subscriptionStartDate. ' + '.$months.' months'));
		}
		
		$dateArray =array(
			'subscriptionStartDate'=>$subscriptionStartDate,
			'subscriptionEndDate'=>$subscriptionEndDate,
		);
		
		return $dateArray;
		
	}
	
	public function addPaymentToNotifications(array $input){
	
		$todayDate = date("Y-m-d");	
		$insertArray = array(
				'message' =>$input['scheduled_message'],
				'scheduled_date' => $input['scheduled_date'],
				'sender_user_id' => $input['sender_user_id'],
				'rx_reminder_id' => $input['rx_reminder_id'],
				'title' => $input['title'],
				'type' => $input['type'],
				'link'=> $input['link'],
			);
			
		$response = Notifications::create($insertArray);
		
		$notifications = Notifications::leftJoin('users', 'users.id', '=', 'notifications.sender_user_id')
								->select('notifications.*','users.id','users.fcm_id','notifications.id as notification_id')
								->where( 'notifications.id', $response->id)
								->first();
								
		if($notifications)
		{
				$userFcmToken = $notifications->fcm_id;
				$message = $notifications->message;
				$title = $notifications->title;
				$notificationId = $notifications->notification_id;
				$link = $notifications->link;
				
				$sendArray = array(
					'fcm_token'=> $userFcmToken,
					'message' =>$message,
					'title'=>$title
				);
				
				//send notifications
				sendNotifications($sendArray);
				
				//update send info in notifications
				$updateArray = array(
					'send_flag'=>1,
					'send_date'=>$todayDate,
					
				);
				
				$update = Notifications::where('id',$notifications->notification_id)->update($updateArray);
		}
	}

}
