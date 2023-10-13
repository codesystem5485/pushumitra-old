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
        return  $this->ProductsaleModel
            ->orderBy('id', 'DESC')
            ->get();
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
}
