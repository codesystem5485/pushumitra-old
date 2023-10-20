<?php

namespace App\Repositories\Implementation\Addanimal;

use App\Base\BaseRepository;
use App\Models\Animals;
use App\Repositories\Interfaces\Addanimal\AddanimalRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use DB;
use DataTables;
use Spatie\Activitylog\Models\Activity;
class AddanimalRepository  extends BaseRepository implements AddanimalRepositoryInterface
{
    /**
     * @var Add Animal
     */
    protected $addAnimalModel; 

    /**
     * AddanimalRepository constructor.
     *
     * @param User $addAnimalModel
     */
    public function __construct(Animals $addAnimalModel)
    {
        parent::__construct($addAnimalModel);
        $this->addAnimalModelRepo = $addAnimalModel;
    }

    /**
     * {@inheritDoc}
     */
    public function getAddanimals()
    {     
        return  $this->addAnimalModelRepo
            ->orderBy('id', 'DESC')
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getAddanimal(int $addAnimalId)
    {
        return  $this->addAnimalModelRepo->findOrFail($addAnimalId);
    }

    /**
     * {@inheritDoc}
     */
    public function updateAddanimal($addAnimalId, $request = []) 
    {
        DB::beginTransaction();
        try {
            $animal =  $this->addAnimalModelRepo->find($addAnimalId);
            $animal->update($request);
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
    public function deleteAddanimal(int $addAnimalId)
    { 
        try{
            $animal =  $this->addAnimalModelRepo->findOrFail($animalId);
            return $animal->delete();
        } catch (\Exception $e) {  
            DB::rollback();
            return false;
        }
    }

    public function with(array $input)
    {
        return  $this->addAnimalModelRepo->with($input)->orderBy('id', 'ASC')
        ->get();
    }
}
