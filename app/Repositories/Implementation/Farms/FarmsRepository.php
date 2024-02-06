<?php

namespace App\Repositories\Implementation\Farms;

use App\Base\BaseRepository;
use App\Models\Farms;
use App\Repositories\Interfaces\Farms\FarmsRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use DB;
use DataTables;
use Spatie\Activitylog\Models\Activity;
class FarmsRepository  extends BaseRepository implements FarmsRepositoryInterface
{
    /**
     * @var Farms
     */
    protected $farmsModel; 

    /**
     * FarmsRepository constructor.
     *
     * @param User $farmsModel
     */
    public function __construct(Farms $farmsModel)
    {
        parent::__construct($farmsModel);
        $this->farmsModelRepo = $farmsModel;
    }

    /**
     * {@inheritDoc}
     */
    public function getFarmsList()
    {     
        return  $this->farmsModelRepo
            ->orderBy('id', 'DESC')
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getFarm(int $farmId)
    {
		return Farms::leftJoin('subcategories', 'subcategories.id', '=', 'farms.sub_category')
					->select('farms.*','subcategories.name as subcategory_name')
					->where('farms.id',$farmId)
					->first();
    }

    /**
     * {@inheritDoc}
     */
    public function updateFarm($farmId, $request = []) 
    {
        DB::beginTransaction();
        try {
            $farms =  $this->farmsModelRepo->find($farmId);
            $farms->update($request);
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
    public function deleteFarm(int $farmId)
    { 
        try{
            $farm =  $this->farmsModelRepo->findOrFail($farmId);
            return $farm->delete();
        } catch (\Exception $e) {  
            DB::rollback();
            return false;
        }
    }

    public function with(array $input)
    {
        return  $this->farmsModelRepo->with($input)->orderBy('id', 'ASC')
        ->get();
    }
	
	public function getAjaxList(){
        
        $results = $this->getFarmsList(); 
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
			$actionBtn .= '<a href="'.route('farms.detail',['id' => $results->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default button-view" data-toggle="tooltip" data-original-title="User Detail"><i class="icon-user" aria-hidden="true"></i>
                </button></a>';
			$actionBtn .= '<a href="'.route('farms.edit',['id' => $results->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default m-r-5 button-edit" data-toggle="tooltip" data-original-title="Edit"><i class="icon-pencil" aria-hidden="true"></i> 
                </button></a>';
			$actionBtn .= '<a href="'.route('farms.delete',['id' => $results->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default button-remove" data-toggle="tooltip" data-original-title="Remove"><i class="icon-trash" aria-hidden="true"></i></button></a>';
           
           
            if(auth()->user()->can('farms-edit')){
               
            }
            if(auth()->user()->can('farms-delete')){
             }
            return $actionBtn;
           
        })
        ->rawColumns(['action','added_date'])
        ->make(true);
    }
}
