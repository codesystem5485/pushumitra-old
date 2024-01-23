<?php

namespace App\Repositories\Implementation\Dogshelters;

use App\Base\BaseRepository;
use App\Models\DogShelters;
use App\Repositories\Interfaces\Dogshelters\DogsheltersRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use DB;
use DataTables;
use Spatie\Activitylog\Models\Activity;
class DogsheltersRepository  extends BaseRepository implements DogsheltersRepositoryInterface
{
    /**
     * @var DogShelters
     */
    protected $dogsheltersModel; 

    /**
     * DogsheltersRepository constructor.
     *
     * @param User $dogsheltersModel
     */
    public function __construct(DogShelters $dogsheltersModel)
    {
        parent::__construct($dogsheltersModel);
        $this->dogsheltersModelRepo = $dogsheltersModel;
    }

    /**
     * {@inheritDoc}
     */
    public function getDogsheltersList()
    {     
        return  $this->dogsheltersModelRepo
            ->orderBy('id', 'DESC')
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getDogshelter(int $dogshelterId)
    {
		return DogShelters::where('id',$dogshelterId)
					->first();
    }

    /**
     * {@inheritDoc}
     */
    public function updateDogshelter($dogshelterId, $request = []) 
    {
        DB::beginTransaction();
        try {
            $dogshelters =  $this->dogsheltersModelRepo->find($dogshelterId);
            $dogshelters->update($request);
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
    public function deleteDogshelter(int $dogshelterId)
    { 
        try{
            $dogshelter =  $this->dogsheltersModelRepo->findOrFail($dogshelterId);
            return $dogshelter->delete();
        } catch (\Exception $e) {  
            DB::rollback();
            return false;
        }
    }

    public function with(array $input)
    {
        return  $this->dogsheltersModelRepo->with($input)->orderBy('id', 'ASC')
        ->get();
    }
	
	public function getAjaxList(){
        
        $results = $this->getDogsheltersList(); 
        return Datatables::of($results)
        ->addIndexColumn()
        ->editColumn('added_date', function ($results) { 
		  $date ='-';
		 if($results->subscriptionStartDate!=''){
			 $date = date('d-M-Y',strtotime($results->subscriptionStartDate));
		 }
            return $date;
        })
		->editColumn('expire_date', function ($results) { 
		  $date ='-';
		 if($results->subscriptionEndDate!='' || $results->subscriptionEndDate!='0000-00-00'){
			 $date = date('d-M-Y',strtotime($results->subscriptionEndDate));
		 }
            return $date;
        })
        ->addColumn('action', function($results){
            $actionBtn = '';
			if(auth()->user()->can('dogshelter-detail')){
				$actionBtn .= '<a href="'.route('dogshelters.detail',['id' => $results->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default button-view" data-toggle="tooltip" data-original-title="User Detail"><i class="icon-user" aria-hidden="true"></i>
                </button></a>';
			}
			if(auth()->user()->can('dogshelter-edit')){
			$actionBtn .= '<a href="'.route('dogshelters.edit',['id' => $results->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default m-r-5 button-edit" data-toggle="tooltip" data-original-title="Edit"><i class="icon-pencil" aria-hidden="true"></i> 
                </button></a>';
			}
			if(auth()->user()->can('dogshelter-delete')){
			$actionBtn .= '<a href="'.route('dogshelters.delete',['id' => $results->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default button-remove" data-toggle="tooltip" data-original-title="Remove"><i class="icon-trash" aria-hidden="true"></i></button></a>';
           
           }
            
            return $actionBtn;
           
        })
        ->rawColumns(['action','added_date'])
        ->make(true);
    }
}
