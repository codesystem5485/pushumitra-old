<?php

namespace App\Repositories\Implementation\Milkcollections;

use App\Base\BaseRepository;
use App\Models\Milkcollections;
use App\Repositories\Interfaces\Milkcollections\MilkCollectionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use DB;
use DataTables;
use Spatie\Activitylog\Models\Activity;
class MilkcollectionsRepository  extends BaseRepository implements MilkCollectionRepositoryInterface
{
    /**
     * @var Milkcollections
     */
    protected $milkcollectionsModel; 

    /**
     * MilkcollectionsRepository constructor.
     *
     * @param User $milkcollectionsModel
     */
    public function __construct(Milkcollections $milkcollectionsModel)
    {
        parent::__construct($milkcollectionsModel);
        $this->milkcollectionsModelRepo = $milkcollectionsModel;
    }

    /**
     * {@inheritDoc}
     */
    public function getMilkcollectionsList()
    {     
        return  $this->milkcollectionsModelRepo
            ->orderBy('id', 'DESC')
			->where('status', 1)
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getMilkcollection(int $milkCollectionId)
    {
		return Milkcollections::where('id',$milkCollectionId)->first();
    }

    /**
     * {@inheritDoc}
     */
    public function updateMilkcollection($milkCollectionId, $request = []) 
    {
        DB::beginTransaction();
        try {
            $milkcollections =  $this->milkcollectionsModelRepo->find($milkCollectionId);
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
    public function deleteMilkcollection(int $milkCollectionId)
    { 
        try{
            $milkcollection =  $this->milkcollectionsModelRepo->findOrFail($milkCollectionId);
            return $milkcollection->delete();
        } catch (\Exception $e) {  
            DB::rollback();
            return false;
        }
    }

    public function with(array $input)
    {
        return  $this->milkcollectionsModelRepo->with($input)->orderBy('id', 'ASC')
        ->get();
    }
	
	public function getAjaxList(){
        
        $results = $this->getMilkcollectionsList(); 
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
			$actionBtn .= '<a href="'.route('milkcollections.detail',['id' => $results->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default button-view" data-toggle="tooltip" data-original-title="User Detail"><i class="icon-user" aria-hidden="true"></i>
                </button></a>';
			$actionBtn .= '<a href="'.route('milkcollections.edit',['id' => $results->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default m-r-5 button-edit" data-toggle="tooltip" data-original-title="Edit"><i class="icon-pencil" aria-hidden="true"></i> 
                </button></a>';
			$actionBtn .= '<a href="'.route('milkcollections.delete',['id' => $results->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default button-remove" data-toggle="tooltip" data-original-title="Remove"><i class="icon-trash" aria-hidden="true"></i></button></a>';
           
           
            if(auth()->user()->can('milkcollection-edit')){
               
            }
            if(auth()->user()->can('milkcollection-delete')){
             }
            return $actionBtn;
           
        })
        ->rawColumns(['action','added_date'])
        ->make(true);
    }
}
