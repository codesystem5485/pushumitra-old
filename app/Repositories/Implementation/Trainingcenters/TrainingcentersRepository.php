<?php

namespace App\Repositories\Implementation\Trainingcenters;

use App\Base\BaseRepository;
use App\Models\TrainingCenters;
use App\Repositories\Interfaces\Trainingcenters\TrainingcentersRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use DB;
use DataTables;
use Spatie\Activitylog\Models\Activity;
class TrainingcentersRepository  extends BaseRepository implements TrainingcentersRepositoryInterface
{
    /**
     * @var trainingcenters
     */
    protected $trainingcentersModel; 

    /**
     * TrainingcentersRepository constructor.
     *
     * @param User $trainingcentersModel
     */
    public function __construct(Trainingcenters $trainingcentersModel)
    {
        parent::__construct($trainingcentersModel);
        $this->trainingcentersModelRepo = $trainingcentersModel;
    }

    /**
     * {@inheritDoc}
     */
    public function getTrainingcentersList()
    {     
        return  $this->trainingcentersModelRepo
            ->orderBy('id', 'DESC')
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getTrainingcenter(int $tainingCenterId)
    {
		return Trainingcenters::leftJoin('subcategories', 'subcategories.id', '=', 'training_centers.sub_category')
					->select('training_centers.*','subcategories.name as subcategory_name')
					->where('training_centers.id',$tainingCenterId)
					->first();
    }

    /**
     * {@inheritDoc}
     */
    public function updateTrainingcenter($tainingCenterId, $request = []) 
    {
        DB::beginTransaction();
        try {
            $tainingCenters =  $this->trainingcentersModelRepo->find($tainingCenterId);
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
    public function deleteTrainingcenter(int $tainingCenterId)
    { 
        try{
            $trainingcenter =  $this->trainingcentersModelRepo->findOrFail($tainingCenterId);
            return $trainingcenter->delete();
        } catch (\Exception $e) {  
            DB::rollback();
            return false;
        }
    }

    public function with(array $input)
    {
        return  $this->trainingcentersModelRepo->with($input)->orderBy('id', 'ASC')
        ->get();
    }
	
	public function getAjaxList(){
        
        $results = $this->getTrainingcentersList(); 
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
			$actionBtn .= '<a href="'.route('trainingcenters.detail',['id' => $results->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default button-view" data-toggle="tooltip" data-original-title="User Detail"><i class="icon-user" aria-hidden="true"></i>
                </button></a>';
			$actionBtn .= '<a href="'.route('trainingcenters.edit',['id' => $results->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default m-r-5 button-edit" data-toggle="tooltip" data-original-title="Edit"><i class="icon-pencil" aria-hidden="true"></i> 
                </button></a>';
			$actionBtn .= '<a href="'.route('trainingcenters.delete',['id' => $results->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default button-remove" data-toggle="tooltip" data-original-title="Remove"><i class="icon-trash" aria-hidden="true"></i></button></a>';
           
           
            if(auth()->user()->can('trainingcenters-edit')){
               
            }
            if(auth()->user()->can('trainingcenters-delete')){
             }
            return $actionBtn;
           
        })
        ->rawColumns(['action','added_date'])
        ->make(true);
    }
}
