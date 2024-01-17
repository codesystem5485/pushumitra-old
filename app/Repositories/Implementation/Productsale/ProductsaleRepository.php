<?php

namespace App\Repositories\Implementation\Productsale;

use App\Base\BaseRepository;
use App\Models\ProductForSale;
use App\Repositories\Interfaces\Productsale\ProductsaleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use DB;
use DataTables;
use Spatie\Activitylog\Models\Activity;
class ProductsaleRepository  extends BaseRepository implements ProductsaleRepositoryInterface
{
    /**
     * @var Product sale
     */
    protected $ProductsaleModel; 

    /**
     * Product for sale Repository constructor.
     *
     * @param User $ProductsaleModel
     */
    public function __construct(ProductForSale $productsaleModel)
    {
        parent::__construct($productsaleModel);
        $this->productsaleModel = $productsaleModel;
    }

    /**
     * {@inheritDoc}
     */
    public function getProductforsale()
    {     
       /* return  $this->ProductsaleModel
            ->orderBy('id', 'DESC')
            ->get();*/
		return Productforsale::orderBy('id','DESC')->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getProductsale(int $ProductsaleId)
    {
        return  $this->ProductsaleModel->findOrFail($ProductsaleId);
    }

    /**
     * {@inheritDoc}
     */
    public function updateProductsale($ProductsaleId, $request = []) 
    {
        DB::beginTransaction();
        try {
            $Productsale =  $this->ProductsaleModel->find($ProductsaleId);
            $Productsale->update($request);
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
    public function deleteProductsale(int $ProductsaleId)
    { 
        try{
            $Productsale =  $this->ProductsaleModel->findOrFail($ProductsaleId);
            return $Productsale->delete();
        } catch (\Exception $e) {  
            DB::rollback();
            return false;
        }
    }

    public function with(array $input)
    {
        return  $this->ProductsaleModel->with($input)->orderBy('id', 'ASC')
        ->get();
    }
	
	public function getAjaxList(){
        
        $results = $this->getProductforsale(); 
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
			$actionBtn .= '<a href="'.route('product-sale.detail',['id' => $results->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default button-view" data-toggle="tooltip" data-original-title="User Detail"><i class="icon-user" aria-hidden="true"></i>
                </button></a>';
			
			
           
            if(auth()->user()->can('product-sale-edit')){
				$actionBtn .= '<a href="'.route('product-sale.edit',['id' => $results->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default m-r-5 button-edit" data-toggle="tooltip" data-original-title="Edit"><i class="icon-pencil" aria-hidden="true"></i> 
                </button></a>';
               
            }
            if(auth()->user()->can('product-sale-delete')){
				
				$actionBtn .= '<a href="'.route('product-sale.delete',['id' => $results->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default button-remove" data-toggle="tooltip" data-original-title="Remove"><i class="icon-trash" aria-hidden="true"></i></button></a>';
           
             }
            return $actionBtn;
           
        })
        ->rawColumns(['action','added_date'])
        ->make(true);
    }
}
