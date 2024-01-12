<?php

namespace App\Repositories\Implementation\Suppliers;

use App\Base\BaseRepository;
use App\Models\Suppliers;
use App\Repositories\Interfaces\Suppliers\SuppliersRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use DB;
use DataTables;
use Spatie\Activitylog\Models\Activity;
class SuppliersRepository  extends BaseRepository implements SuppliersRepositoryInterface
{
    /**
     * @var suppliers
     */
    protected $suppliersModel; 

    /**
     * SuppliersRepository constructor.
     *
     * @param User $suppliersModelRepo
     */
    public function __construct(Suppliers $suppliersModel)
    {
        parent::__construct($suppliersModel);
        $this->suppliersModelRepo = $suppliersModel;
    }

    /**
     * {@inheritDoc}
     */
    public function getSuppliersList()
    {     
        return  $this->suppliersModelRepo
            ->orderBy('id', 'DESC')
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getSuppliers(int $supplierlId)
    {
		return Suppliers::leftJoin('subcategories', 'subcategories.id', '=', 'suppliers.sub_category')
					->select('suppliers.*','subcategories.name as subcategory_name')
					->where('suppliers.id',$supplierlId)
					->first();
    }

    /**
     * {@inheritDoc}
     */
    public function updatSupplier($supplierlId, $request = []) 
    {
        DB::beginTransaction();
        try {
            $suppliers =  $this->suppliersModelRepo->find($supplierlId);
            $suppliers->update($request);
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
    public function deleteSupplier(int $supplierlId)
    { 
        try{
            $supplier =  $this->suppliersModelRepo->findOrFail($supplierlId);
            return $supplier->delete();
        } catch (\Exception $e) {  
            DB::rollback();
            return false;
        }
    }

    public function with(array $input)
    {
        return  $this->suppliersModelRepo->with($input)->orderBy('id', 'ASC')
        ->get();
    }
}
