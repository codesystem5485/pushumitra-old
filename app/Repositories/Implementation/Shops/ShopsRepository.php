<?php

namespace App\Repositories\Implementation\Shops;

use App\Base\BaseRepository;
use App\Models\Shops;
use App\Repositories\Interfaces\Shops\ShopsRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use DB;
use DataTables;
use Spatie\Activitylog\Models\Activity;
class ShopsRepository  extends BaseRepository implements ShopsRepositoryInterface
{
    /**
     * @var Shops
     */
    protected $shopsModel; 

    /**
     * ShopsRepository constructor.
     *
     * @param User $shopsModel
     */
    public function __construct(Shops $shopsModel)
    {
        parent::__construct($shopsModel);
        $this->shopsModelRepo = $shopsModel;
    }

    /**
     * {@inheritDoc}
     */
    public function getShopsList()
    {     
        return  $this->shopsModelRepo
            ->orderBy('id', 'DESC')
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getShop(int $shopId)
    {
		return Shops::leftJoin('subcategories', 'subcategories.id', '=', 'shops.sub_category')
					->select('shops.*','subcategories.name as subcategory_name')
					->where('shops.id',$shopId)
					->first();
    }

    /**
     * {@inheritDoc}
     */
    public function updateShop($shopId, $request = []) 
    {
        DB::beginTransaction();
        try {
            $shops =  $this->shopModelRepo->find($tainingCenterId);
            $shops->update($request);
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
            $trainingcenter =  $this->shopModelRepo->findOrFail($tainingCenterId);
            return $trainingcenter->delete();
        } catch (\Exception $e) {  
            DB::rollback();
            return false;
        }
    }

    public function with(array $input)
    {
        return  $this->shopModelRepo->with($input)->orderBy('id', 'ASC')
        ->get();
    }
	
	public function getAjaxList(){
        
        $results = $this->getShopsList(); 
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
			$actionBtn .= '<a href="'.route('shops.detail',['id' => $results->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default button-view" data-toggle="tooltip" data-original-title="User Detail"><i class="icon-user" aria-hidden="true"></i>
                </button></a>';
			$actionBtn .= '<a href="'.route('shops.edit',['id' => $results->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default m-r-5 button-edit" data-toggle="tooltip" data-original-title="Edit"><i class="icon-pencil" aria-hidden="true"></i> 
                </button></a>';
			$actionBtn .= '<a href="'.route('shops.delete',['id' => $results->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default button-remove" data-toggle="tooltip" data-original-title="Remove"><i class="icon-trash" aria-hidden="true"></i></button></a>';
           
           
            if(auth()->user()->can('shops-edit')){
               
            }
            if(auth()->user()->can('shops-delete')){
             }
            return $actionBtn;
           
        })
        ->rawColumns(['action','added_date'])
        ->make(true);
    }
}
