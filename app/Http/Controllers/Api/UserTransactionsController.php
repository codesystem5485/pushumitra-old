<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\Veterinaryhospitals;
use App\Models\PoultryHatchery;
use App\Models\Panjarpol;
use App\Models\Shops;
use App\Models\Farms;
use App\Models\MilkCollections;
use App\Models\DogShelters;
use App\Models\Animals;
use App\Models\Labs;
use App\Models\Ngo;
use App\Models\Easycares;

use App\Models\TrainingCenters;
use App\Models\Institutions;
use App\Models\Chemist;
use App\Models\Transporters;
use App\Models\ProductForSale;
use App\Models\AnimalForSale;
use App\Models\Breeder;
use App\Models\Suppliers;
use DB;

use Illuminate\Support\Arr;



class UserTransactionsController extends BaseController
{
     protected $url = '';
     
     
    public function __construct(){

        
    }
	
	

	public function getTransactions(Request $request)
	{
		$requestData = request()->all();
		$user_id = $request->user_id;
		
		$hospitalArr = Veterinaryhospitals::where('user_id', $user_id)->get();
		$hospitalModuleArr = [];
		foreach($hospitalArr as $hospital)
		{
			$myArray  = Arr::add($hospital, 'module_id', 7);
			array_push($hospitalModuleArr,$myArray);
		}

		$milkCollectionArr = MilkCollections::where('user_id', $user_id)->get();
		$milkCollectionModuleArr = [];
		foreach($milkCollectionArr as $millCollection)
		{
			$myArray  = Arr::add($millCollection, 'module_id', 17);
			array_push($milkCollectionModuleArr,$myArray);
		}
		
		$panjarpolArr = Panjarpol::where('user_id', $user_id)->get();
		$panjarpolModuleArr = [];
		foreach($panjarpolArr as $panjarpol)
		{
			$myArray  = Arr::add($panjarpol, 'module_id', 13);
			array_push($panjarpolModuleArr,$myArray);
		}
		
		$poultryArr = PoultryHatchery::where('user_id', $user_id)->get();
		$poultryModuleArr = [];
		foreach($poultryArr as $poultry)
		{
			$myArray  = Arr::add($poultry, 'module_id', 14);
			array_push($poultryModuleArr,$myArray);
		}
		
		$dogShelterArr = DogShelters::where('user_id', $user_id)->get();
		$dogShelterModuleArr = [];
		foreach($dogShelterArr as $dogShelter)
		{
			$myArray  = Arr::add($dogShelter, 'module_id', 15);
			array_push($dogShelterModuleArr,$myArray);
		}
		
		
		$animalArr = Animals::where('user_id', $user_id)->get();
		$animalModuleArr = [];
		foreach($animalArr as $animal)
		{
			$myArray  = Arr::add($animal, 'module_id', 21);
			array_push($animalModuleArr,$myArray);
		}
		
		$labArr = Labs::where('user_id', $user_id)->get();
		$labModuleArr = [];
		foreach($labArr as $lab)
		{
			$myArray  = Arr::add($lab, 'module_id', 18);
			array_push($labModuleArr,$myArray);
		}
		$ngoArr = Ngo::where('user_id', $user_id)->get();
		$ngoModuleArr = [];
		foreach($ngoArr as $ngo)
		{
			$myArray  = Arr::add($ngo, 'module_id', 20);
			array_push($ngoModuleArr,$myArray);
		}
		
		$easycareArr = Easycares::where('user_id', $user_id)->get();
		$easycareModuleArr = [];
		foreach($easycareArr as $easycare)
		{
			$myArray  = Arr::add($easycare, 'module_id', 19);
			array_push($easycareModuleArr,$myArray);
		}
		
		$shopArr = Shops::where('user_id', $user_id)->get();
		$shopModuleArr = [];
		foreach($shopArr as $shop)
		{
			$myArray  = Arr::add($shop, 'module_id', 12);
			array_push($shopModuleArr,$myArray);
		}
		
		$farmArr = Farms::where('user_id', $user_id)->get();
		$farmModuleArr = [];
		foreach($farmArr as $farm)
		{
			$myArray  = Arr::add($farm, 'module_id', 10);
			array_push($farmModuleArr,$myArray);
		}
		
		$trainingCenterArr = TrainingCenters::where('user_id', $user_id)->get();
		$trainingCenterModuleArr = [];
		foreach($trainingCenterArr as $training)
		{
			$myArray  = Arr::add($training, 'module_id', 11);
			array_push($trainingCenterModuleArr,$myArray);
		}
		
		$institutionArr = Institutions::where('user_id', $user_id)->get();
		$institutionModuleArr = [];
		foreach($institutionArr as $institution)
		{
			$myArray  = Arr::add($institution, 'module_id', 16);
			array_push($institutionModuleArr,$myArray);
		}
		
		$chemistArr 	  = Chemist::where('user_id', $user_id)->get();
		$chemistModuleArr = [];
		foreach($chemistArr as $chemist)
		{
			$myArray  = Arr::add($chemist, 'module_id', 5);
			array_push($chemistModuleArr,$myArray);
		}
		
		$transporterCount = Transporters::where('user_id', $user_id)->get();
		$institutionModuleArr = [];
		foreach($institutionModuleArr as $institution)
		{
			$myArray  = Arr::add($institution, 'module_id', 4);
			array_push($institutionModuleArr,$myArray);
		}
		
		$productSaleArr = ProductForSale::where('user_id', $user_id)->get();
		$productSaleModuleArr = [];
		foreach($productSaleArr as $product)
		{
			$myArray  = Arr::add($product, 'module_id', 8);
			array_push($productSaleModuleArr,$myArray);
		}
		
		$animalSaleArr = AnimalForSale::where('user_id', $user_id)->get();
		$animalSaleModuleArr = [];
		foreach($animalSaleArr as $animalsale)
		{
			$myArray  = Arr::add($animalsale, 'module_id', 2);
			array_push($animalSaleModuleArr,$myArray);
		}
		
		$breederArr = Breeder::where('user_id', $user_id)->get();
		$breederModuleArr = [];
		foreach($breederArr as $breeder)
		{
			$myArray  = Arr::add($breeder, 'module_id', 3);
			array_push($breederModuleArr,$myArray);
		}
		
		$supplierArr = Suppliers::where('user_id', $user_id)->get();
		$supplierModuleArr = [];
		foreach($supplierArr as $supplier)
		{
			$myArray  = Arr::add($supplier, 'module_id', 9);
			array_push($supplierModuleArr,$myArray);
		}
		
		$collection = collect([$milkCollectionModuleArr,$hospitalModuleArr,$panjarpolModuleArr,$poultryModuleArr,
		$dogShelterModuleArr,$animalModuleArr,$labModuleArr,$ngoModuleArr,$easycareModuleArr,$shopModuleArr,
		$farmModuleArr,$trainingCenterModuleArr,$institutionModuleArr,$supplierModuleArr,
		$productSaleModuleArr,$chemistModuleArr,$breederModuleArr,$animalSaleModuleArr]);
		$collapsed = $collection->collapse();
		
		$response['results']= $collapsed;
		return $this->sendResponse($response,"",200);
	}
	
	

}