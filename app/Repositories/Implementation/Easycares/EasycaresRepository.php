<?php

namespace App\Repositories\Implementation\Easycares;

use App\Base\BaseRepository;
use App\Models\Easycares;
use App\Repositories\Interfaces\Easycares\EasycaresRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use DB;
use DataTables;
use Spatie\Activitylog\Models\Activity;
class EasycaresRepository  extends BaseRepository implements EasycaresRepositoryInterface
{
    /**
     * @var Easycares
     */
    protected $easycaresModel; 

    /**
     * EasycareRepository constructor.
     *
     * @param User $easycaresModel
     */
    public function __construct(Easycares $easycaresModel)
    {
        parent::__construct($easycaresModel);
        $this->easycaresModelRepo = $easycaresModel;
    }

    /**
     * {@inheritDoc}
     */
    public function getEasycareList()
    {     
        return  $this->easycaresModelRepo
            ->orderBy('id', 'DESC')
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getEasycare(int $easycareId)
    {
		return Easycares::where('id',$easycareId)
					->first();
    }

    /**
     * {@inheritDoc}
     */
    public function updateEasycare($easycareId, $request = []) 
    {
        DB::beginTransaction();
        try {
            $easycare =  $this->easycaresModelRepo->find($easycareId);
            $easycare->update($request);
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
    public function deleteEasycare(int $easycareId)
    { 
        try{
            $easycare =  $this->easycaresModelRepo->findOrFail($easycareId);
            return $easycare->delete();
        } catch (\Exception $e) {  
            DB::rollback();
            return false;
        }
    }

    public function with(array $input)
    {
        return  $this->easycaresModelRepo->with($input)->orderBy('id', 'ASC')
        ->get();
    }
	
	public function getAjaxList(){
        
        $results = $this->getEasycareList(); 
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
			if(auth()->user()->can('easycare-detail')){
			$actionBtn .= '<a href="'.route('easycares.detail',['id' => $results->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default button-view" data-toggle="tooltip" data-original-title="User Detail"><i class="icon-user" aria-hidden="true"></i>
                </button></a>';
			}
			if(auth()->user()->can('easycare-edit')){
			$actionBtn .= '<a href="'.route('easycares.edit',['id' => $results->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default m-r-5 button-edit" data-toggle="tooltip" data-original-title="Edit"><i class="icon-pencil" aria-hidden="true"></i> 
                </button></a>';
			}
			if(auth()->user()->can('easycare-delete')){
			$actionBtn .= '<a href="'.route('easycares.delete',['id' => $results->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default button-remove" data-toggle="tooltip" data-original-title="Remove"><i class="icon-trash" aria-hidden="true"></i></button></a>';
           
           }
           
            return $actionBtn;
           
        })
        ->rawColumns(['action','added_date'])
        ->make(true);
    }
}
