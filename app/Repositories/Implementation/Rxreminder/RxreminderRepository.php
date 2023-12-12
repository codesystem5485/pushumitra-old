<?php

namespace App\Repositories\Implementation\Rxreminder;

use App\Base\BaseRepository;
use App\Models\Rxreminder;
use App\Models\Notifications;
use App\Repositories\Interfaces\Rxreminder\RxreminderRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use DB;
use DataTables;
use Spatie\Activitylog\Models\Activity;

class RxreminderRepository extends BaseRepository implements RxreminderRepositoryInterface
{
    /**
     * @var breeder sale
     */
    protected $rxreminderModel; 

    /**
     * rxreminder Repository constructor.
     *
     * @param User $rxreminderModel
     */
    public function __construct(Rxreminder $rxreminderModel)
    {
        parent::__construct($rxreminderModel);
        $this->rxreminderModel = $rxreminderModel;
    }

    /**
     * {@inheritDoc}
     */
    public function getRxreminder()
    {     
        return  $this->rxreminderModel
            ->orderBy('id', 'DESC')
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getSingleRxreminder(int $rxreminderId)
    {
        return  $this->rxreminderModel->findOrFail($rxreminderId);
    }

    /**
     * {@inheritDoc}
     */
    public function updaterRreminder($rxreminderId, $request = []) 
    {
        DB::beginTransaction();
        try {
            $animalsale =  $this->rxreminderModel->find($rxreminderId);
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
    public function deleteRxreminder(int $rxreminderId)
    { 
        try{
            $rxreminder =  $this->rxreminderModel->findOrFail($rxreminderId);
            return $rxreminder->delete();
        } catch (\Exception $e) {  
            DB::rollback();
            return false;
        }
    }

    public function with(array $input)
    {
        return  $this->rxreminderModel->with($input)->orderBy('id', 'ASC')
        ->get();
    }
	
	public function addReminderToNotifications(array $input){
		
		$insertArray = array(
				'message' =>$input['scheduled_message'],
				'scheduled_date' => $input['scheduled_date'],
				'sender_user_id' => $input['sender_user_id'],
				'rx_reminder_id' => $input['rx_reminder_id'],
				'title' => $input['title'],
			);
			
		$response = Notifications::create($insertArray);
			
	}
}
