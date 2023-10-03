<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\City\CityRepositoryInterface;

class CommonController extends BaseController
{
    protected $cityRepo;
    public function __construct(CityRepositoryInterface $cityRepo)
    {
        $this->cityRepo = $cityRepo;
    }
    public function get_cities(Request $request)
    {
        return $citiesstates = $this->cityRepo->getCities(['state_id'=>$request->state_id]);
    }
}