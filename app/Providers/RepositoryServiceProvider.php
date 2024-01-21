<?php

namespace App\Providers; 

use Illuminate\Support\ServiceProvider;   
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use App\Repositories\Implementation\User\UserRepository;
use App\Repositories\Interfaces\User\UserDetailRepositoryInterface;
use App\Repositories\Implementation\User\UserDetailRepository;
use App\Repositories\Interfaces\State\StateRepositoryInterface;
use App\Repositories\Implementation\State\StateRepository;
use App\Repositories\Interfaces\City\CityRepositoryInterface;
use App\Repositories\Implementation\City\CityRepository;
use App\Repositories\Interfaces\Chemist\ChemistRepositoryInterface;
use App\Repositories\Implementation\Chemist\ChemistRepository;
use App\Repositories\Interfaces\Transporter\TransporterRepositoryInterface;
use App\Repositories\Implementation\Transporter\TransporterRepository;
use App\Repositories\Interfaces\Animalsale\AnimalsaleRepositoryInterface;
use App\Repositories\Implementation\Animalsale\AnimalsaleRepository;
use App\Repositories\Interfaces\Productsale\ProductsaleRepositoryInterface;
use App\Repositories\Implementation\Productsale\ProductsaleRepository;
use App\Repositories\Interfaces\Addanimal\AddanimalRepositoryInterface;
use App\Repositories\Implementation\Addanimal\AddanimalRepository;
use App\Repositories\Interfaces\Product\ProductRepositoryInterface;
use App\Repositories\Implementation\Product\ProductRepository;
use App\Repositories\Interfaces\Breeder\BreederRepositoryInterface;
use App\Repositories\Implementation\Breeder\BreederRepository;
use App\Repositories\Interfaces\Rxreminder\RxreminderRepositoryInterface;
use App\Repositories\Implementation\Rxreminder\RxreminderRepository;
use App\Repositories\Interfaces\Vethospitals\VethospitalsRepositoryInterface;
use App\Repositories\Implementation\Vethospitals\VethospitalsRepository;
use App\Repositories\Interfaces\Suppliers\SuppliersRepositoryInterface;
use App\Repositories\Implementation\Suppliers\SuppliersRepository;
use App\Repositories\Interfaces\Trainingcenters\TrainingcentersRepositoryInterface;
use App\Repositories\Implementation\Trainingcenters\TrainingcentersRepository;
use App\Repositories\Interfaces\Shops\ShopsRepositoryInterface;
use App\Repositories\Implementation\Shops\ShopsRepository;
use App\Repositories\Interfaces\Farms\FarmsRepositoryInterface;
use App\Repositories\Implementation\Farms\FarmsRepository;
use App\Repositories\Interfaces\Institutions\InstitutionsRepositoryInterface;
use App\Repositories\Implementation\Institutions\InstitutionsRepository;

class RepositoryServiceProvider extends ServiceProvider 
{
    /**
     * Register services.
     *
     * @return void
     */ 
    public function register()  
    {  
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(UserDetailRepositoryInterface::class, UserDetailRepository::class);
        $this->app->bind(StateRepositoryInterface::class, StateRepository::class);
        $this->app->bind(CityRepositoryInterface::class, CityRepository::class);
        $this->app->bind(ChemistRepositoryInterface::class, ChemistRepository::class);        
        $this->app->bind(TransporterRepositoryInterface::class, TransporterRepository::class);        
        $this->app->bind(AnimalsaleRepositoryInterface::class, AnimalsaleRepository::class);        
        $this->app->bind(ProductsaleRepositoryInterface::class, ProductsaleRepository::class);        
        $this->app->bind(AddanimalRepositoryInterface::class, AddanimalRepository::class);        
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
		$this->app->bind(BreederRepositoryInterface::class, BreederRepository::class); 
		$this->app->bind(RxreminderRepositoryInterface::class, RxreminderRepository::class); 
		$this->app->bind(VethospitalsRepositoryInterface::class, VethospitalsRepository::class); 
		$this->app->bind(SuppliersRepositoryInterface::class, SuppliersRepository::class);
		$this->app->bind(TrainingcentersRepositoryInterface::class, TrainingcentersRepository::class);
		$this->app->bind(ShopsRepositoryInterface::class, ShopsRepository::class);
		$this->app->bind(FarmsRepositoryInterface::class, FarmsRepository::class);
		$this->app->bind(InstitutionsRepositoryInterface::class, InstitutionsRepository::class);        
    } 

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot() 
    {
        //
    }
}
