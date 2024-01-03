<?php

namespace App\Repositories\Implementation\Animalsale;

use App\Base\BaseRepository;
use App\Models\AnimalForSale;
use App\Models\Fee;
use App\Repositories\Interfaces\Animalsale\AnimalsaleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use DB;
use DataTables;
use Spatie\Activitylog\Models\Activity;
use App\Models\Notifications;

class AnimalsaleRepository  extends BaseRepository implements AnimalsaleRepositoryInterface
{
    /**
     * @var Animal sale
     */
    protected $animalSaleModel; 

    /**
     * Animal for sale Repository constructor.
     *
     * @param User $animalSaleModel
     */
    public function __construct(Animalforsale $animalSaleModel)
    {
        parent::__construct($animalSaleModel);
        $this->animalSaleModel = $animalSaleModel;
    }

    /**
     * {@inheritDoc}
     */
    public function getAnimalforsale()
    {     
        return  $this->animalSaleModel
            ->orderBy('id', 'DESC')
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getAnimalsale(int $animalsaleId)
    {
        return  $this->animalSaleModel->findOrFail($animalsaleId);
    }

    /**
     * {@inheritDoc}
     */
    public function updateAnimalsale($animalsaleId, $request = []) 
    {
        DB::beginTransaction();
        try {
            $animalsale =  $this->animalSaleModel->find($animalsaleId);
            $animalsale->update($request);
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
    public function deleteAnimalsale(int $animalsaleId)
    { 
        try{
            $animalsale =  $this->animalSaleModel->findOrFail($animalsaleId);
            return $animalsale->delete();
        } catch (\Exception $e) {  
            DB::rollback();
            return false;
        }
    }

    public function with(array $input)
    {
        return  $this->animalSaleModel->with($input)->orderBy('id', 'ASC')
        ->get();
    }
	
	//get animal for sale subscriptions date 
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
								->select('notifications.*','users.id','users.fcm_id')
								->where( 'notifications.id', $response->id)
								->first();
								
		if($notifications)
		{
				$userFcmToken = $notifications->fcm_id;
				$message = $notifications->message;
				$title = $notifications->title;
				$notificationId = $notifications->id;
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
				
				$update = Notifications::where('id',$notifications->id)->update($updateArray);
		
		}
	}
}
