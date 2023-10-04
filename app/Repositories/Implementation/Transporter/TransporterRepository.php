<?php

namespace App\Repositories\Implementation\Transporter;

use App\Base\BaseRepository;
use App\Models\Transporters;
use App\Repositories\Interfaces\Transporter\TransporterRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use DB;
use DataTables;
use Spatie\Activitylog\Models\Activity;
class TransporterRepository  extends BaseRepository implements TransporterRepositoryInterface
{
    /**
     * @var Transporter
     */
    protected $transporterModel; 

    /**
     * TransporterRepository constructor.
     *
     * @param User $transporterModel
     */
    public function __construct(Transporters $transporterModel)
    {
        parent::__construct($transporterModel);
        $this->transporterModelRepo = $transporterModel;
    }

    /**
     * {@inheritDoc}
     */
    public function getTransporters()
    {     
        return  $this->transporterModelRepo
            ->orderBy('id', 'DESC')
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getTransporter(int $transporterId)
    {
        return  $this->transporterModelRepo->findOrFail($transporterId);
    }

    /**
     * {@inheritDoc}
     */
    public function updateTransporter($transporterId, $request = []) 
    {
        DB::beginTransaction();
        try {
            $transporter =  $this->transporterModelRepo->find($transporterId);
            $transporter->update($request);
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
    public function deleteTransporter(int $transporterId)
    { 
        try{
            $transporter =  $this->transporterModelRepo->findOrFail($transporterId);
            return $transporter->delete();
        } catch (\Exception $e) {  
            DB::rollback();
            return false;
        }
    }

    public function with(array $input)
    {
        return  $this->transporterModelRepo->with($input)->orderBy('id', 'ASC')
        ->get();
    }
}
