<?php

namespace App\Repositories\Implementation\Transporter;

use App\Base\BaseRepository;
use App\Models\Transporters;
use App\Repositories\Interfaces\Transporter\TransporterRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use DB;
use DataTables;
use Spatie\Activitylog\Models\Activity;
class TransporterRepository  extends BaseRepository implements TransporterRepositoryInterface
{
    /**
     * @var Transporter
     */
    protected $transporterModel; 

    /**
     * TransporterRepository constructor.
     *
     * @param User $transporterModel
     */
    public function __construct(Transporters $transporterModel)
    {
        parent::__construct($transporterModel);
        $this->transporterModelRepo = $transporterModel;
    }

    /**
     * {@inheritDoc}
     */
    public function getTransporters()
    {     
        return  $this->transporterModelRepo
			->where('status',1)
            ->orderBy('id', 'DESC')
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getTransporter(int $transporterId)
    {
        return  $this->transporterModelRepo->findOrFail($transporterId);
    }

    /**
     * {@inheritDoc}
     */
    public function updateTransporter($transporterId, $request = []) 
    {
        DB::beginTransaction();
        try {
            $transporter =  $this->transporterModelRepo->find($transporterId);
            $transporter->update($request);
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
    public function deleteTransporter(int $transporterId)
    { 
        try{
            $transporter =  $this->transporterModelRepo->findOrFail($transporterId);
            return $transporter->delete();
        } catch (\Exception $e) {  
            DB::rollback();
            return false;
        }
    }

    public function with(array $input)
    {
        return  $this->transporterModelRepo->with($input)->orderBy('id', 'ASC')
        ->get();
    }
	
	public function getAjaxList(){
        
        $results = $this->getTransporters(); 
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
		  
			 if($results->subscriptionEndDate!=''){
				 $date = date('d-M-Y',strtotime($results->subscriptionEndDate));
			 }
		  
            return $date;
        })
        ->addColumn('action', function($results){
            $actionBtn = '';
			$actionBtn .= '<a href="'.route('transporter.detail',['id' => $results->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default button-view" data-toggle="tooltip" data-original-title="User Detail"><i class="icon-user" aria-hidden="true"></i>
                </button></a>';
			
			
           
            if(auth()->user()->can('transporter-edit')){
				$actionBtn .= '<a href="'.route('transporter.edit',['id' => $results->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default m-r-5 button-edit" data-toggle="tooltip" data-original-title="Edit"><i class="icon-pencil" aria-hidden="true"></i> 
                </button></a>';
               
            }
            if(auth()->user()->can('transporter-delete')){
				
				$actionBtn .= '<a href="'.route('transporter.delete',['id' => $results->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default button-remove" data-toggle="tooltip" data-original-title="Remove"><i class="icon-trash" aria-hidden="true"></i></button></a>';
           
             }
            return $actionBtn;
           
        })
        ->rawColumns(['action','added_date'])
        ->make(true);
    }
}
