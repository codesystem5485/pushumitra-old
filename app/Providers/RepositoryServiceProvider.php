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
