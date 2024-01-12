<?php

namespace App\Repositories\Implementation\Vethospitals;

use App\Base\BaseRepository;
use App\Models\Veterinaryhospitals;
use App\Repositories\Interfaces\Vethospitals\VethospitalsRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use DB;
use DataTables;
use Spatie\Activitylog\Models\Activity;
class VethospitalsRepository  extends BaseRepository implements VethospitalsRepositoryInterface
{
    /**
     * @var vethospital
     */
    protected $vethospitalsModel; 

    /**
     * VethospitalsRepository constructor.
     *
     * @param User $vethospitalsModel
     */
    public function __construct(Veterinaryhospitals $vethospitalsModel)
    {
        parent::__construct($vethospitalsModel);
        $this->vethospitalsModelRepo = $vethospitalsModel;
    }

    /**
     * {@inheritDoc}
     */
    public function getVethospitalsList()
    {     
        return  $this->vethospitalsModelRepo
            ->orderBy('id', 'DESC')
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getVethospital(int $vethospitalId)
    {
		return Veterinaryhospitals::leftJoin('subcategories', 'subcategories.id', '=', 'veterinary_hospitals.sub_category')
					->select('veterinary_hospitals.*','subcategories.name as subcategory_name')
					->where('veterinary_hospitals.id',$vethospitalId)
					->first();
    }

    /**
     * {@inheritDoc}
     */
    public function updateVethospital($vethospitalId, $request = []) 
    {
        DB::beginTransaction();
        try {
            $vethospitals =  $this->vethospitalsModelRepo->find($vethospitalId);
            $vethospitals->update($request);
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
    public function deleteVethospitals(int $vethospitalId)
    { 
        try{
            $vethospital =  $this->vethospitalsModelRepo->findOrFail($vethospitalId);
            return $vethospital->delete();
        } catch (\Exception $e) {  
            DB::rollback();
            return false;
        }
    }

    public function with(array $input)
    {
        return  $this->vethospitalsModelRepo->with($input)->orderBy('id', 'ASC')
        ->get();
    }
}
