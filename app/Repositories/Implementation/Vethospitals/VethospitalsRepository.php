<?php

namespace App\Repositories\Implementation\Vethospitals;

use App\Base\BaseRepository;
use App\Models\Veterinaryhospitals;
use App\Repositories\Interfaces\Vethospitals\VethospitalsRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use DB;
use DataTables;
use Spatie\Activitylog\Models\Activity;
class VethospitalsRepository extends BaseRepository implements VethospitalsRepositoryInterface
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
			->where('status',1)
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
	
	public function getAjaxList(){
        
        $results = $this->getVethospitalsList(); 
        return Datatables::of($results)
        ->addIndexColumn()
        ->editColumn('added_date', function ($results) { 
		  $date ='-';
		   if($results->type=='Private'){
				 if($results->subscriptionStartDate!=''){
					 $date = date('d-M-Y',strtotime($results->subscriptionStartDate));
				 }
		   }
            return $date;
        })
		->editColumn('expire_date', function ($results) { 
		  $date ='-';
		  if($results->type=='Private'){
			 if($results->subscriptionEndDate!=''){
				 $date = date('d-M-Y',strtotime($results->subscriptionEndDate));
			 }
		  }
            return $date;
        })
        ->addColumn('action', function($results){
            $actionBtn = '';
			if(auth()->user()->can('hospital-detail')){
			$actionBtn .= '<a href="'.route('hospitals.detail',['id' => $results->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default button-view" data-toggle="tooltip" data-original-title="User Detail"><i class="icon-user" aria-hidden="true"></i>
                </button></a>';
			}
			if(auth()->user()->can('hospital-edit')){
			$actionBtn .= '<a href="'.route('hospitals.edit',['id' => $results->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default m-r-5 button-edit" data-toggle="tooltip" data-original-title="Edit"><i class="icon-pencil" aria-hidden="true"></i> 
                </button></a>';
			}
			if(auth()->user()->can('hospital-delete')){
			$actionBtn .= '<a href="'.route('hospitals.delete',['id' => $results->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default button-remove" data-toggle="tooltip" data-original-title="Remove"><i class="icon-trash" aria-hidden="true"></i></button></a>';
           
           }
            
            return $actionBtn;
           
        })
        ->rawColumns(['action','added_date'])
        ->make(true);
    }
}
