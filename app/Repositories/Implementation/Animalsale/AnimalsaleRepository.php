<?php

namespace App\Repositories\Implementation\Animalsale;

use App\Base\BaseRepository;
use App\Models\AnimalForSale;
use App\Repositories\Interfaces\Animalsale\AnimalsaleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use DB;
use DataTables;
use Spatie\Activitylog\Models\Activity;
class AnimalsaleRepository  extends BaseRepository implements AnimalsaleRepositoryInterface
{
    /**
     * @var Animal sale
     */
    protected $animalSaleModel; 

    /**
     * Animal for sale Repository constructor.
     *
     * @param User $animalSaleModel
     */
    public function __construct(Animalforsale $animalSaleModel)
    {
        parent::__construct($animalSaleModel);
        $this->animalSaleModel = $animalSaleModel;
    }

    /**
     * {@inheritDoc}
     */
    public function getAnimalforsale()
    {     
        return  $this->animalSaleModel
            ->orderBy('id', 'DESC')
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getAnimalsale(int $animalsaleId)
    {
        return  $this->animalSaleModel->findOrFail($animalsaleId);
    }

    /**
     * {@inheritDoc}
     */
    public function updateAnimalsale($animalsaleId, $request = []) 
    {
        DB::beginTransaction();
        try {
            $animalsale =  $this->animalSaleModel->find($animalsaleId);
            $animalsale->update($request);
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
    public function deleteAnimalsale(int $animalsaleId)
    { 
        try{
            $animalsale =  $this->animalSaleModel->findOrFail($animalsaleId);
            return $animalsale->delete();
        } catch (\Exception $e) {  
            DB::rollback();
            return false;
        }
    }

    public function with(array $input)
    {
        return  $this->animalSaleModel->with($input)->orderBy('id', 'ASC')
        ->get();
    }
}
