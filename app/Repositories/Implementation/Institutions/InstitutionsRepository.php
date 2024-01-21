<?php

namespace App\Repositories\Implementation\Institutions;

use App\Base\BaseRepository;
use App\Models\Institutions;
use App\Repositories\Interfaces\Institutions\InstitutionsRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use DB;
use DataTables;
use Spatie\Activitylog\Models\Activity;
class InstitutionsRepository  extends BaseRepository implements InstitutionsRepositoryInterface
{
    /**
     * @var Institutions
     */
    protected $institutionsModel; 

    /**
     * InstitutionsRepository constructor.
     *
     * @param User $institutionsModel
     */
    public function __construct(Institutions $institutionsModel)
    {
        parent::__construct($institutionsModel);
        $this->institutionsModelRepo = $institutionsModel;
    }

    /**
     * {@inheritDoc}
     */
    public function getInstitutionsList()
    {     
        return  $this->institutionsModelRepo
            ->orderBy('id', 'DESC')
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getInstitution(int $institutionId)
    {
		return Institutions::leftJoin('subcategories', 'subcategories.id', '=', 'institutions.sub_category')
					->select('institutions.*','subcategories.name as subcategory_name')
					->where('institutions.id',$institutionId)
					->first();
    }

    /**
     * {@inheritDoc}
     */
    public function updateInstitution($institutionsId, $request = []) 
    {
        DB::beginTransaction();
        try {
            $tainingCenters =  $this->institutionsModelRepo->find($institutionsId);
            $tainingCenters->update($request);
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
    public function deleteInstitution(int $institutionsId)
    { 
        try{
            $institutions =  $this->institutionsModelRepo->findOrFail($institutionsId);
            return $institutions->delete();
        } catch (\Exception $e) {  
            DB::rollback();
            return false;
        }
    }

    public function with(array $input)
    {
        return  $this->institutionsModelRepo->with($input)->orderBy('id', 'ASC')
        ->get();
    }
	
	public function getAjaxList(){
        
        $results = $this->getInstitutionsList(); 
        return Datatables::of($results)
        ->addIndexColumn()
        ->editColumn('added_date', function ($results) { 
		  $date ='-';
		 if($results->subscriptionStartDate!=''){
			 $date = date('d-M-Y',strtotime($results->subscriptionStartDate));
		 }
            return $date;
        })
        ->addColumn('action', function($results){
            $actionBtn = '';
			$actionBtn .= '<a href="'.route('institutions.detail',['id' => $results->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default button-view" data-toggle="tooltip" data-original-title="User Detail"><i class="icon-user" aria-hidden="true"></i>
                </button></a>';
			$actionBtn .= '<a href="'.route('institutions.edit',['id' => $results->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default m-r-5 button-edit" data-toggle="tooltip" data-original-title="Edit"><i class="icon-pencil" aria-hidden="true"></i> 
                </button></a>';
			$actionBtn .= '<a href="'.route('institutions.delete',['id' => $results->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default button-remove" data-toggle="tooltip" data-original-title="Remove"><i class="icon-trash" aria-hidden="true"></i></button></a>';
           
           
            if(auth()->user()->can('institutions-edit')){
               
            }
            if(auth()->user()->can('institutions-delete')){
             }
            return $actionBtn;
           
        })
        ->rawColumns(['action','added_date'])
        ->make(true);
    }
}
