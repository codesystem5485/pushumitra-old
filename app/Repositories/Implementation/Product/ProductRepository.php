<?php

namespace App\Repositories\Implementation\Product;

use App\Base\BaseRepository;
use App\Models\Product;
use App\Repositories\Interfaces\Product\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use DB;
use DataTables;
use Spatie\Activitylog\Models\Activity;
class ProductRepository  extends BaseRepository implements ProductRepositoryInterface
{
    /**
     * @var Product 
     */
    protected $ProductModel; 

    /**
     * Product Repository constructor.
     *
     * @param User $ProductModel
     */
    public function __construct(Product $productModel)
    {
        parent::__construct($productModel);
        $this->productModel = $productModel;
    }

    /**
     * {@inheritDoc}
     */
    public function getProducts()
    {     
        return  $this->ProductModel
            ->orderBy('id', 'DESC')
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getProduct(int $ProductId)
    {
        return  $this->ProductModel->findOrFail($ProductId);
    }

    /**
     * {@inheritDoc}
     */
    public function updateProduct($ProductId, $request = []) 
    {
        DB::beginTransaction();
        try {
            $Product =  $this->ProductModel->find($ProductId);
            $Product->update($request);
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
    public function deleteProduct(int $ProductId)
    { 
        try{
            $Product =  $this->ProductModel->findOrFail($ProductId);
            return $Product->delete();
        } catch (\Exception $e) {  
            DB::rollback();
            return false;
        }
    }

    public function with(array $input)
    {
        return  $this->ProductModel->with($input)->orderBy('id', 'ASC')
        ->get();
    }
}
