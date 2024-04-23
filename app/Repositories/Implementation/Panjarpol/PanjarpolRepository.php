<?php

namespace App\Repositories\Implementation\Panjarpol;

use App\Base\BaseRepository;
use App\Models\Panjarpol;
use App\Repositories\Interfaces\Panjarpol\PanjarpolRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use DB;
use DataTables;
use Spatie\Activitylog\Models\Activity;
class PanjarpolRepository  extends BaseRepository implements PanjarpolRepositoryInterface
{
    /**
     * @var Panjarpol
     */
    protected $panjarpolModel; 

    /**
     * PanjarpolRepository constructor.
     *
     * @param User $panjarpolModel
     */
    public function __construct(Panjarpol $panjarpolModel)
    {
        parent::__construct($panjarpolModel);
        $this->panjarpolModelRepo = $panjarpolModel;
    }

    /**
     * {@inheritDoc}
     */
    public function getPanjarpolList()
    {     
        return  $this->panjarpolModelRepo
			->where('status',1)
            ->orderBy('id', 'DESC')
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getPanjarpol(int $panjarpolId)
    {
		return Panjarpol::where('id',$panjarpolId)
					->first();
    }

    /**
     * {@inheritDoc}
     */
    public function updatePanjarpol($panjarpolId, $request = []) 
    {
        DB::beginTransaction();
        try {
            $panjarpol =  $this->panjarpolModelRepo->find($panjarpolId);
            $panjarpol->update($request);
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
    public function deletePanjarpol(int $panjarpolId)
    { 
        try{
            $panjarpol =  $this->panjarpolModelRepo->findOrFail($panjarpolId);
            return $panjarpol->delete();
        } catch (\Exception $e) {  
            DB::rollback();
            return false;
        }
    }

    public function with(array $input)
    {
        return  $this->panjarpolModelRepo->with($input)->orderBy('id', 'ASC')
        ->get();
    }
	
	public function getAjaxList(){
        
        $results = $this->getPanjarpolList(); 
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
			if(auth()->user()->can('panjarpol-detail')){
			$actionBtn .= '<a href="'.route('panjarpols.detail',['id' => $results->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default button-view" data-toggle="tooltip" data-original-title="User Detail"><i class="icon-user" aria-hidden="true"></i>
                </button></a>';
			}
			if(auth()->user()->can('panjarpol-edit')){
			$actionBtn .= '<a href="'.route('panjarpols.edit',['id' => $results->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default m-r-5 button-edit" data-toggle="tooltip" data-original-title="Edit"><i class="icon-pencil" aria-hidden="true"></i> 
                </button></a>';
			}
			if(auth()->user()->can('panjarpol-delete')){
			$actionBtn .= '<a href="'.route('panjarpols.delete',['id' => $results->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default button-remove" data-toggle="tooltip" data-original-title="Remove"><i class="icon-trash" aria-hidden="true"></i></button></a>';
           
           }
           
            return $actionBtn;
           
        })
        ->rawColumns(['action','added_date'])
        ->make(true);
    }
}
