<?php

namespace App\Repositories\Implementation\State;

use App\Base\BaseRepository;
use App\Models\State;
use App\Repositories\Interfaces\State\StateRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use DB;
use DataTables;
use Spatie\Activitylog\Models\Activity;
class StateRepository  extends BaseRepository implements StateRepositoryInterface
{
    /**
     * @var State
     */
    protected $stateModel; 

    /**
     * StateRepository constructor.
     *
     * @param User $stateModel
     */
    public function __construct(State $stateModel)
    {
        parent::__construct($stateModel);
        $this->stateModelRepo = $stateModel;
    }

    /**
     * {@inheritDoc}
     */
    public function getStates()
    {     
        return  $this->stateModelRepo->where('is_active','1')->where('country_id','101')
            ->orderBy('state', 'ASC')
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getState(int $stateId)
    {
        return  $this->stateModelRepo->findOrFail($stateId);
    }

    /**
     * {@inheritDoc}
     */
    public function updateState($stateId, $request = []) 
    {
        DB::beginTransaction();
        try {
            $state =  $this->stateModelRepo->find($stateId);
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
    public function deleteState(int $stateId)
    { 
        try{
            $state =  $this->stateModelRepo->findOrFail($stateId);
            return $state->delete();
        } catch (\Exception $e) {  
            DB::rollback();
            return false;
        }
    }

    public function with(array $input)
    {
        return  $this->stateModelRepo->with($input)->orderBy('id', 'ASC')
        ->get();
    }
}
