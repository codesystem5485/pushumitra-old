<?php

namespace App\Repositories\Implementation\City;

use App\Base\BaseRepository;
use App\Models\State;
use App\Models\Cities;
use App\Repositories\Interfaces\City\CityRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use DB;
use DataTables;
use Spatie\Activitylog\Models\Activity;
class CityRepository  extends BaseRepository implements CityRepositoryInterface
{
    /**
     * @var City
     */
    protected $cityModel; 

    /**
     * CityRepository constructor.
     *
     * @param User $cityModel
     */
    public function __construct(Cities $cityModel)
    {
        parent::__construct($cityModel);
        $this->cityModelRepo = $cityModel;
    }

    /**
     * {@inheritDoc}
     */
    public function getCities(array $input = [])
    {     
        return  $this->cityModelRepo->where('is_active','1')->where('state_id',$input['state_id'])
            ->orderBy('city', 'ASC')
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getCity(int $cityId)
    {
        return  $this->cityModelRepo->findOrFail($cityId);
    }

    /**
     * {@inheritDoc}
     */
    public function updateCity($cityId, $request = []) 
    {
        DB::beginTransaction();
        try {
            $state =  $this->cityModelRepo->find($cityId);
            $state->update($request);
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
    public function deleteCity(int $cityId)
    { 
        try{
            $state =  $this->cityModelRepo->findOrFail($stateId);
            return $state->delete();
        } catch (\Exception $e) {  
            DB::rollback();
            return false;
        }
    }

    public function with(array $input)
    {
        return  $this->cityModelRepo->with($input)->orderBy('id', 'ASC')
        ->get();
    }
}
