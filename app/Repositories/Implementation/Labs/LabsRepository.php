<?php

namespace App\Repositories\Implementation\Labs;

use App\Base\BaseRepository;
use App\Models\Labs;
use App\Repositories\Interfaces\Labs\LabsRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use DB;
use DataTables;
use Spatie\Activitylog\Models\Activity;
class LabsRepository extends BaseRepository implements LabsRepositoryInterface
{
    /**
     * @var vethospital
     */
    protected $labsModel; 

    /**
     * LabsRepository constructor.
     *
     * @param User $labsModel
     */
    public function __construct(Labs $labsModel)
    {
        parent::__construct($labsModel);
        $this->labsModelRepo = $labsModel;
    }

    /**
     * {@inheritDoc}
     */
    public function getLabsList()
    {     
        return  $this->labsModelRepo
            ->where('status',1)
			->orderBy('id', 'DESC')
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getLab(int $labId)
    {
		return Labs::leftJoin('subcategories', 'subcategories.id', '=', 'labs.sub_category')
					->select('labs.*','subcategories.name as subcategory_name')
					->where('labs.id',$labId)
					->first();
    }

    /**
     * {@inheritDoc}
     */
    public function updateLab($labId, $request = []) 
    {
        DB::beginTransaction();
        try {
            $vethospitals =  $this->labsModelRepo->find($vethospitalId);
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
    public function deleteLab(int $vethospitalId)
    { 
        try{
            $vethospital =  $this->labsModelRepo->findOrFail($vethospitalId);
            return $vethospital->delete();
        } catch (\Exception $e) {  
            DB::rollback();
            return false;
        }
    }

    public function with(array $input)
    {
        return  $this->labsModelRepo->with($input)->orderBy('id', 'ASC')
        ->get();
    }
	
	public function getAjaxList(){
        
        $results = $this->getLabsList(); 
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
			if(auth()->user()->can('lab-detail')){
			$actionBtn .= '<a href="'.route('labs.detail',['id' => $results->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default button-view" data-toggle="tooltip" data-original-title="User Detail"><i class="icon-user" aria-hidden="true"></i>
                </button></a>';
			}
			if(auth()->user()->can('lab-edit')){
			$actionBtn .= '<a href="'.route('labs.edit',['id' => $results->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default m-r-5 button-edit" data-toggle="tooltip" data-original-title="Edit"><i class="icon-pencil" aria-hidden="true"></i> 
                </button></a>';
			}
			if(auth()->user()->can('lab-delete')){
			$actionBtn .= '<a href="'.route('labs.delete',['id' => $results->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default button-remove" data-toggle="tooltip" data-original-title="Remove"><i class="icon-trash" aria-hidden="true"></i></button></a>';
           
           }
            
            return $actionBtn;
           
        })
        ->rawColumns(['action','added_date'])
        ->make(true);
    }
}
