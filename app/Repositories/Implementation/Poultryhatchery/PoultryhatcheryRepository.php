<?php

namespace App\Repositories\Implementation\Poultryhatchery;

use App\Base\BaseRepository;
use App\Models\Poultryhatchery;
use App\Repositories\Interfaces\Poultryhatchery\PoultryhatcheryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use DB;
use DataTables;
use Spatie\Activitylog\Models\Activity;
class PoultryhatcheryRepository  extends BaseRepository implements PoultryhatcheryRepositoryInterface
{
    /**
     * @var poultryhatcheryModel
     */
    protected $poultryhatcheryModel; 

    /**
     * PoultryhatcheryRepository constructor.
     *
     * @param Poultryhatchery $poultryhatcheryModel
     */
    public function __construct(Poultryhatchery $poultryhatcheryModel)
    {
        parent::__construct($poultryhatcheryModel);
        $this->poultryhatcheryModelRepo = $poultryhatcheryModel;
    }

    /**
     * {@inheritDoc}
     */
    public function getPoultryhatcheryList()
    {     
        return  $this->poultryhatcheryModelRepo
            ->orderBy('id', 'DESC')
			->where('status', 1)
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getPoultryhatchery(int $poultryhatcheryId)
    {
		return Poultryhatchery::where('id',$poultryhatcheryId)->first();
    }

    /**
     * {@inheritDoc}
     */
    public function updateMilkcollection($poultryhatcheryId, $request = []) 
    {
        DB::beginTransaction();
        try {
            $milkcollections =  $this->poultryhatcheryModelRepo->find($poultryhatcheryId);
            $milkcollections->update($request);
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
    public function deleteMilkcollection(int $poultryhatcheryId)
    { 
        try{
            $milkcollection =  $this->poultryhatcheryModelRepo->findOrFail($poultryhatcheryId);
            return $milkcollection->delete();
        } catch (\Exception $e) {  
            DB::rollback();
            return false;
        }
    }

    public function with(array $input)
    {
        return  $this->poultryhatcheryModelRepo->with($input)->orderBy('id', 'ASC')
        ->get();
    }
	
	public function getAjaxList(){
        
        $results = $this->getPoultryhatcheryList(); 
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
			$actionBtn .= '<a href="'.route('poultryhatchery.detail',['id' => $results->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default button-view" data-toggle="tooltip" data-original-title="User Detail"><i class="icon-user" aria-hidden="true"></i>
                </button></a>';
			$actionBtn .= '<a href="'.route('poultryhatchery.edit',['id' => $results->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default m-r-5 button-edit" data-toggle="tooltip" data-original-title="Edit"><i class="icon-pencil" aria-hidden="true"></i> 
                </button></a>';
			$actionBtn .= '<a href="'.route('poultryhatchery.delete',['id' => $results->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default button-remove" data-toggle="tooltip" data-original-title="Remove"><i class="icon-trash" aria-hidden="true"></i></button></a>';
           
           
            if(auth()->user()->can('poultryhatchery-edit')){
               
            }
            if(auth()->user()->can('poultryhatchery-delete')){
             }
            return $actionBtn;
           
        })
        ->rawColumns(['action','added_date'])
        ->make(true);
    }
}
