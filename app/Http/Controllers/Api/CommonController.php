<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;

use App\Repositories\Interfaces\State\StateRepositoryInterface;
use App\Repositories\Interfaces\City\CityRepositoryInterface;

class CommonController extends BaseController
{
    protected $stateRepo;
   
    public function __construct(StateRepositoryInterface $stateRepo, CityRepositoryInterface $cityRepo)
    {
        $this->stateRepo = $stateRepo;
        $this->cityRepo = $cityRepo;
    }

    public function getStates()
    {
        $response['states'] = $this->stateRepo->getStates();
        return $this->sendResponse($response,"",200);
    }

    public function getCities(Request $request)
    {
        $response['cities'] = $this->cityRepo->getCities(['state_id'=>$request->state_id]);
        return $this->sendResponse($response,"",200);
    }
}