<?php

namespace App\Repositories\Implementation\Ngo;

use App\Base\BaseRepository;
use App\Models\Ngo;
use App\Repositories\Interfaces\Ngo\NgoRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use DB;
use DataTables;
use Spatie\Activitylog\Models\Activity;
class NgoRepository  extends BaseRepository implements NgoRepositoryInterface
{
    /**
     * @var Ngo
     */
    protected $ngoModel; 

    /**
     * ngoRepository constructor.
     *
     * @param User $ngoModel
     */
    public function __construct(Ngo $ngoModel)
    {
        parent::__construct($ngoModel);
        $this->ngoModelRepo = $ngoModel;
    }

    /**
     * {@inheritDoc}
     */
    public function getNgoList()
    {     
        return  $this->ngoModelRepo
            ->orderBy('id', 'DESC')
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getNgo(int $ngoId)
    {
		return Ngo::where('id',$ngoId)
					->first();
    }

    /**
     * {@inheritDoc}
     */
    public function updateNgo($ngoId, $request = []) 
    {
        DB::beginTransaction();
        try {
            $ngo =  $this->ngoModelRepo->find($ngoId);
            $ngo->update($request);
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
    public function deletePanjarpol(int $ngoId)
    { 
        try{
            $ngo =  $this->ngoModelRepo->findOrFail($panjarpolId);
            return $ngo->delete();
        } catch (\Exception $e) {  
            DB::rollback();
            return false;
        }
    }

    public function with(array $input)
    {
        return  $this->ngoModelRepo->with($input)->orderBy('id', 'ASC')
        ->get();
    }
	
	public function getAjaxList(){
        
        $results = $this->getNgoList(); 
        return Datatables::of($results)
        ->addIndexColumn()
        ->editColumn('added_date', function ($results) { 
		  $date ='-';
		 if($results->created_at!=''){
			 $date = date('d-M-Y',strtotime($results->created_at));
		 }
            return $date;
        })
        ->addColumn('action', function($results){
            $actionBtn = '';
			if(auth()->user()->can('ngo-detail')){
			$actionBtn .= '<a href="'.route('ngo.detail',['id' => $results->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default button-view" data-toggle="tooltip" data-original-title="User Detail"><i class="icon-user" aria-hidden="true"></i>
                </button></a>';
			}
			if(auth()->user()->can('ngo-edit')){
			$actionBtn .= '<a href="'.route('ngo.edit',['id' => $results->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default m-r-5 button-edit" data-toggle="tooltip" data-original-title="Edit"><i class="icon-pencil" aria-hidden="true"></i> 
                </button></a>';
			}
			if(auth()->user()->can('ngo-delete')){
			$actionBtn .= '<a href="'.route('ngo.delete',['id' => $results->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default button-remove" data-toggle="tooltip" data-original-title="Remove"><i class="icon-trash" aria-hidden="true"></i></button></a>';
           
           }
           
            return $actionBtn;
           
        })
        ->rawColumns(['action','added_date'])
        ->make(true);
    }
}
