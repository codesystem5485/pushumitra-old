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
            $animalsale =  $this->breederModel->find($breederId);
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
    public function deleteBreeder(int $breederId)
    { 
        try{
            $animalsale =  $this->breederModel->findOrFail($breederId);
            return $animalsale->delete();
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
}
