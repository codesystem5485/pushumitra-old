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
		
		$hospitalArr = Veterinaryhospitals::select('veterinary_hospitals.*',
            DB::raw('(select image_name from  veterinary_hospitals_images where veterinary_hospitals_id  = veterinary_hospitals.id order by id asc limit 1) as image_name'))
			->where('user_id', $user_id)->get();
		$hospitalModuleArr = [];
		foreach($hospitalArr as $hospital)
		{
			$myArray  = Arr::add($hospital, 'module_id', 7);
			array_push($hospitalModuleArr,$myArray);
		}

		$milkCollectionArr = MilkCollections::select('id','user_code','registration_number','milkcollection_center_name','incharge_name','mobile_number','type',
		'taluka','address','city_town','district','state','pincode','latitude','longitude','created_at','updated_at',
            DB::raw('(select image_name from  milkcollection_center_images where milkcollection_center_id  = milkcollection_centers.id order by id asc limit 1) as image_name'))
			->where('user_id', $user_id)->get();
		$milkCollectionModuleArr = [];
		foreach($milkCollectionArr as $millCollection)
		{
			$myArray  = Arr::add($millCollection, 'module_id', 17);
			array_push($milkCollectionModuleArr,$myArray);
		}
		
		$panjarpolArr = Panjarpol::select('panjarpol.id','user_code','registration_number','panjarpol_name','manager_name','mobile_number',
		'taluka','address','city_town','district','state','pincode','latitude','longitude','created_at','updated_at',
            DB::raw('(select image_name from panjarpol_images where panjarpol_id  = panjarpol.id order by id asc limit 1) as image_name'))
			->where('user_id', $user_id)->get();
		$panjarpolModuleArr = [];
		foreach($panjarpolArr as $panjarpol)
		{
			$myArray  = Arr::add($panjarpol, 'module_id', 13);
			array_push($panjarpolModuleArr,$myArray);
		}
		
		$poultryArr = PoultryHatchery::select('id','user_code','poultryhatchery_center_name','incharge_name','mobile_number','type',
		'taluka','address','city_town','district','state','pincode','latitude','longitude','created_at','updated_at',
            DB::raw('(select image_name from  poultryhatchery_center_images where poultryhatchery_center_id  = poultryhatchery_centers.id order by id asc limit 1) as image_name'))->where('user_id', $user_id)->get();
		$poultryModuleArr = [];
		foreach($poultryArr as $poultry)
		{
			$myArray  = Arr::add($poultry, 'module_id', 14);
			array_push($poultryModuleArr,$myArray);
		}
		
		$dogShelterArr = DogShelters::select('id','user_code','dogshelter_name','incharge_name','mobile_number',
		'registration_number','taluka','address','city_town','district','state','pincode','latitude','longitude','created_at','updated_at',
            DB::raw('(select image_name from  dog_shelter_images where dog_shelter_id  = dog_shelters.id order by id asc limit 1) as image_name'))
			->where('user_id', $user_id)->get();
		$dogShelterModuleArr = [];
		foreach($dogShelterArr as $dogShelter)
		{
			$myArray  = Arr::add($dogShelter, 'module_id', 15);
			array_push($dogShelterModuleArr,$myArray);
		}
		
		
		$animalArr = Animals::leftJoin('species', 'species.id', '=', 'animals.species')
			->select( 'animals.*','species.specie as species_name',
            DB::raw('(select image_name from add_animal_images where animal_id  =   animals.id order by id asc limit 1) as image_name'))
          ->where('user_id', $user_id)->get();
		$animalModuleArr = [];
		foreach($animalArr as $animal)
		{
			$myArray  = Arr::add($animal, 'module_id', 21);
			array_push($animalModuleArr,$myArray);
		}
		
		$labArr = Labs::select('labs.id','user_code','lab_name','owner_name','mobile_number','education','type','svc_registration_number',
		'taluka','address','city_town','district','state','pincode','latitude','longitude','created_at','updated_at',
            DB::raw('(select image_name from  labs_images where lab_id  = labs.id order by id asc limit 1) as image_name'))
			->where('user_id', $user_id)->get();
		$labModuleArr = [];
		foreach($labArr as $lab)
		{
			$myArray  = Arr::add($lab, 'module_id', 18);
			array_push($labModuleArr,$myArray);
		}
		$ngoArr = Ngo::select('ngo.id','user_code','registration_number','ngo_name','manager_name','mobile_number',
		'taluka','address','city_town','district','state','pincode','latitude','longitude','created_at','updated_at',
            DB::raw('(select image_name from ngo_images where ngo_id  = ngo.id order by id asc limit 1) as image_name'))
			->where('user_id', $user_id)->get();
		$ngoModuleArr = [];
		foreach($ngoArr as $ngo)
		{
			$myArray  = Arr::add($ngo, 'module_id', 20);
			array_push($ngoModuleArr,$myArray);
		}
		
		$easycareArr = Easycares::leftJoin('users', 'users.id', '=', 'easy_cares.user_id')
		   ->select('easy_cares.*','users.full_name',
            DB::raw('(select image_name from easycares_images where easycare_id  = easy_cares.id order by id asc limit 1) as image_name'))
			->where('user_id', $user_id)->get();
		$easycareModuleArr = [];
		foreach($easycareArr as $easycare)
		{
			$myArray  = Arr::add($easycare, 'module_id', 19);
			array_push($easycareModuleArr,$myArray);
		}
		
		$shopArr = Shops::select('shops.id','shop_name','user_code','shop_owner_name','mobile_number',
		'taluka','address','city_town','district','state','pincode','latitude','longitude','created_at','updated_at',
            DB::raw('(select image_name from  shop_images where shop_id  = shops.id order by id asc limit 1) as image_name'))
			->where('user_id', $user_id)->get();
		$shopModuleArr = [];
		foreach($shopArr as $shop)
		{
			$myArray  = Arr::add($shop, 'module_id', 12);
			array_push($shopModuleArr,$myArray);
		}
		
		$farmArr = Farms::select('farms.id','user_code','farm_name','incharge_name','mobile_number','taluka','address','city_town','district','state','pincode','latitude','longitude','created_at','updated_at',
			DB::raw('(select image_name from farm_images where farm_id  = farms.id order by id asc limit 1) as image_name'))
			->where('user_id', $user_id)->get();
		$farmModuleArr = [];
		foreach($farmArr as $farm)
		{
			$myArray  = Arr::add($farm, 'module_id', 10);
			array_push($farmModuleArr,$myArray);
		}
		
		$trainingCenterArr = TrainingCenters::select('training_centers.id','training_centers.user_code','training_center_name','incharge_name','mobile_number','duration','type','fees',
		'registration_number','taluka','address','city_town','district','state','pincode','latitude','longitude','created_at','updated_at',
            DB::raw('(select image_name from  training_center_images where training_center_id  = training_centers.id order by id asc limit 1) as image_name'))
			->where('user_id', $user_id)->get();
		$trainingCenterModuleArr = [];
		foreach($trainingCenterArr as $training)
		{
			$myArray  = Arr::add($training, 'module_id', 11);
			array_push($trainingCenterModuleArr,$myArray);
		}
		
		$institutionArr = Institutions::select('institutions.id','user_code','institution_name','incharge_name','mobile_number','type',
		'registration_number','taluka','address','city_town','district','state','pincode','latitude','longitude','created_at','updated_at',
            DB::raw('(select image_name from  institutions_images where institution_id  = institutions.id order by id asc limit 1) as image_name'))
			->where('user_id', $user_id)->get();
		$institutionModuleArr = [];
		foreach($institutionArr as $institution)
		{
			$myArray  = Arr::add($institution, 'module_id', 16);
			array_push($institutionModuleArr,$myArray);
		}
		
		$chemistArr 	  = Chemist::select( 'chemists.id','chemists.shop_name','chemists.owner_name','chemists.mobile_number',
		'chemists.city_town','chemists.latitude','chemists.user_code','chemists.longitude','chemists.created_at','chemists.updated_at',
		DB::raw('(select image_name from chemist_shop_images where chemist_id  =   chemists.id order by id asc limit 1) as image_name'))
			->where('user_id', $user_id)->get();
		$chemistModuleArr = [];
		foreach($chemistArr as $chemist)
		{
			$myArray  = Arr::add($chemist, 'module_id', 5);
			array_push($chemistModuleArr,$myArray);
		}
		
		$transporterCount = Transporters::select('transporters.user_code','transporters.id','transporters.transporter_name','transporters.vehicle_name',
            'transporters.mobile_number','transporters.city_town','transporters.latitude','transporters.longitude','transporters.created_at','transporters.updated_at',
			DB::raw('(select image_name from  vehicle_images where transporter_id  = transporters.id order by id asc limit 1) as image_name'))
			->where('user_id', $user_id)->get();
		$institutionModuleArr = [];
		foreach($institutionModuleArr as $institution)
		{
			$myArray  = Arr::add($institution, 'module_id', 4);
			array_push($institutionModuleArr,$myArray);
		}
		
		$productSaleArr = ProductForSale::select('product_for_sales.*',
            DB::raw('(select image_name from product_images where product_sale_id  = product_for_sales.id order by id asc limit 1) as image_name'))
			->where('user_id', $user_id)->get();
		$productSaleModuleArr = [];
		foreach($productSaleArr as $product)
		{
			$myArray  = Arr::add($product, 'module_id', 8);
			array_push($productSaleModuleArr,$myArray);
		}
		
		$animalSaleArr = AnimalForSale::select('animal_for_sales.*',
			DB::raw('(select image_name from animal_images where animal_sale_id  =   animal_for_sales.id order by id asc limit 1) as image_name'))
		->where('user_id', $user_id)->get();
		$animalSaleModuleArr = [];
		foreach($animalSaleArr as $animalsale)
		{
			$myArray  = Arr::add($animalsale, 'module_id', 2);
			array_push($animalSaleModuleArr,$myArray);
		}
		
		$breederArr = Breeder::leftJoin('species', 'species.id', '=', 'breeders.species')
		->select('breeders.user_code','breeders.id','breeders.breeder_name','breeders.animal_breed','breeders.age','breeders.expected_price','breeders.mobile_number',
		'breeders.latitude','breeders.longitude','breeders.created_at','breeders.updated_at',
				'species.specie as species_name',DB::raw('(select image_name from  breeder_images where breeder_id  = breeders.id order by id asc limit 1) as image_name'))
			->where('user_id', $user_id)->get();
		$breederModuleArr = [];
		foreach($breederArr as $breeder)
		{
			$myArray  = Arr::add($breeder, 'module_id', 3);
			array_push($breederModuleArr,$myArray);
		}
		
		$supplierArr = Suppliers::select('suppliers.id','suppliers.user_code','suppliers.supplier_name','suppliers.mobile_number','suppliers.sub_category',
		'suppliers.address','suppliers.city_town','suppliers.district','suppliers.taluka','suppliers.user_code','suppliers.latitude',
		'suppliers.longitude','suppliers.created_at','suppliers.updated_at',DB::raw('(select image_name from  supplier_product_images where supplier_id  = suppliers.id order by id asc limit 1) as image_name'))
			->where('user_id', $user_id)->get();
		$supplierModuleArr = [];
		foreach($supplierArr as $supplier)
		{
			$myArray  = Arr::add($supplier, 'module_id', 9);
			array_push($supplierModuleArr,$myArray);
		}
		
		$collection = collect([$milkCollectionModuleArr,$hospitalModuleArr,$panjarpolModuleArr,$poultryModuleArr,
		$dogShelterModuleArr,$animalModuleArr,$labModuleArr,$ngoModuleArr,$easycareModuleArr,$shopModuleArr,
		$farmModuleArr,$trainingCenterModuleArr,$institutionModuleArr,$supplierModuleArr,
		$productSaleModuleArr,$chemistModuleArr,$breederModuleArr,$animalSaleModuleArr])->sortBy('created_at');
		$collapsed = $collection->collapse();
		
		$response['results']= $collapsed;
		return $this->sendResponse($response,"",200);
	}
}