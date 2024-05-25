<?php

namespace App\Http\Controllers\Crons;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Session;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use App\Repositories\Interfaces\Rxreminder\RxreminderRepositoryInterface;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\User;
use App\Models\Notifications;
use Carbon\Carbon;
use App\Models\AnimalForSale;
use App\Models\Breeder;
use App\Models\Transporters;
use App\Models\ProductForSale;
use App\Models\Chemist;
use App\Models\Veterinaryhospitals;
use App\Models\Labs;
use App\Models\Suppliers;
use App\Models\TrainingCenters;
use App\Models\Institutions;
use App\Models\Shops;
use App\Models\PoultryHatchery;
use App\Models\Panjarpol;
use App\Models\MilkCollections;
use App\Models\Farms;
use App\Models\DogShelters;


class SubscriptionController extends BaseController
{
	protected $userRepository;
    protected $rxreminderRepo;
	
    public function __construct(RxreminderRepositoryInterface $rxreminderRepo, UserRepositoryInterface $userRepository){

        $this->userRepo = $userRepository;
       
        $this->rxreminderRepo = $rxreminderRepo;
    }
	
	//animal sale
	public function sendAnimalSaleNotifications()
	{
		$todayDate = date("Y-m-d");
		$todayDate1 = Carbon::createFromFormat('Y-m-d', $todayDate);
		$date = date("Y-m-d",strtotime($todayDate1->addDays(2))); 
		 
		$results = AnimalForSale::leftJoin('species', 'species.id', '=', 'animal_for_sales.species')
								 ->leftJoin('users', 'users.id', '=', 'animal_for_sales.user_id')
								->select('animal_for_sales.*','users.fcm_id','species.specie as species_name')
								->where( 'animal_for_sales.subscriptionEndDate', '=', $date)
								->where( 'animal_for_sales.status', 1)
								->get();
								
		$module = 'Animal For Sale';
		$title = 'Reminder: Animal For Sale Listing Expiry';
		if($results)
		{
			foreach($results as $row)
			{ 
				$breed =  $row->breed;
				$species_name =  $row->species_name;
				$age =  $row->age;	
$uidnumber = $row->UID_number;				
				$send_message = 'Your animal for sale listing '.$uidnumber.' is expiring in 2 days. Renew your subscription now to continue enjoying our services.
  Animal Details:
 *species: '.$species_name.'
 *Age: '.$age.'
 *Breed: '.$breed;
 
				$insertArray[] = '';
			    $user_id =  $row->user_id; 
				$insertArray['userFcmToken'] = $row->fcm_id;
				
				$insertArray['message'] = $send_message;
				$insertArray['title'] =$title;
				
				$insertArray['scheduled_date']=$todayDate;
				$insertArray['sender_user_id']=$row->user_id;
				$insertArray['type']=5;
				$insertArray['send_flag']=1;
				$insertArray['rx_reminder_id']=0;
				$insertArray['send_date']=$todayDate;
			 
				$notifications = $this->userRepo->sendRenewReminderNotifications($insertArray);
			}
		}
	}
	
	public function sendBreederNotifications()
	{
		$todayDate = date("Y-m-d");
		$todayDate1 = Carbon::createFromFormat('Y-m-d', $todayDate);
		$date = date("Y-m-d",strtotime($todayDate1->addDays(2))); 
		 
		$results = Breeder::leftJoin('species', 'species.id', '=', 'breeders.species')
							->leftJoin('users', 'users.id', '=', 'breeders.user_id')
								->select('breeders.*','users.fcm_id','species.specie as species_name')
								//->where( 'breeders.subscriptionEndDate', '=', $date)
								 ->where(DB::raw("(DATE_FORMAT(breeders.subscriptionEndDate,'%Y-%m-%d'))"), "=", $date)
								->where( 'breeders.status',1)
								->get();
								
		$module = 'Breeder';
		$title = 'Reminder: Breeder Listing Expiry';
		if($results)
		{
			foreach($results as $row)
			{
				$breeder_name = $row->breeder_name;
				$breed = $row->animal_breed;
				$species_name =  $row->species_name;
				$age =  $row->age;	
				$send_message = 'Your Breeder listing for '.$breeder_name.' is expiring in 2 days. Renew your subscription now to continue enjoying our services.
  Breeder Details:
 *species: '.$species_name.'
 *Age: '.$age.'
 *Breed: '.$breed;
 
				$insertArray[] = '';
			    $user_id =  $row->user_id; 
				$insertArray['userFcmToken'] = $row->fcm_id;
				
				$insertArray['message'] = $send_message;
				$insertArray['title'] =$title;
				$insertArray['scheduled_date']=$todayDate;
				$insertArray['sender_user_id']=$row->user_id;
				$insertArray['type']=5;
				$insertArray['send_flag']=1;
				$insertArray['rx_reminder_id']=0;
				$insertArray['send_date']=$todayDate;
			 
				$notifications = $this->userRepo->sendRenewReminderNotifications($insertArray);
			}
		}
	}
	
	public function sendTransporterNotifications()
	{
		$todayDate = date("Y-m-d");
		$todayDate1 = Carbon::createFromFormat('Y-m-d', $todayDate);
		$date = date("Y-m-d",strtotime($todayDate1->addDays(2))); 
		 
		$results = Transporters::leftJoin('users', 'users.id', '=', 'transporters.user_id')
								->select('transporters.*','users.fcm_id')
								->where( 'transporters.subscriptionEndDate', '=', $date)
								 //->where(DB::raw("(DATE_FORMAT(transporters.subscriptionEndDate,'%Y-%m-%d'))"), "=", $date)
								->where( 'transporters.status',1)
								->get();
								
		$module = 'Transporter';
		$title = 'Reminder: Transporter Listing Expiry';
		if($results)
		{
			foreach($results as $row)
			{
				$transporter_name = $row->transporter_name;
				$vehicle_name= $row->vehicle_name;
				$send_message = 'Your Transporter listing for '.$transporter_name.' is expiring in 2 days. Renew your subscription now to continue enjoying our services.
  Transporter Details:
 *Name: '.$transporter_name.'
 *Vehicle name: '.$vehicle_name;
 
				$insertArray[] = '';
			    $user_id =  $row->user_id; 
				$insertArray['userFcmToken'] = $row->fcm_id;
				
				$insertArray['message'] = $send_message;
				$insertArray['title'] =$title;
				$insertArray['scheduled_date']=$todayDate;
				$insertArray['sender_user_id']=$row->user_id;
				$insertArray['type']=5;
				$insertArray['send_flag']=1;
				$insertArray['rx_reminder_id']=0;
				$insertArray['send_date']=$todayDate;
			 
				$notifications = $this->userRepo->sendRenewReminderNotifications($insertArray);
			}
		}
	}
	
	public function sendProductSaleNotifications()
	{
		$todayDate = date("Y-m-d");
		$todayDate1 = Carbon::createFromFormat('Y-m-d', $todayDate);
		$date = date("Y-m-d",strtotime($todayDate1->addDays(2))); 
		 
		$results = ProductForSale::leftJoin('users', 'users.id', '=', 'product_for_sales.user_id')
								->select('product_for_sales.*','users.fcm_id')
								->where( 'product_for_sales.subscriptionEndDate', '=', $date)
								->where( 'product_for_sales.status',1)
								->get();
								
		$module = 'Product For Sale';
		$title = 'Reminder: Product For Sale Listing Expiry';
		if($results)
		{
			foreach($results as $row)
			{
				$insertArray[] = '';
				$product_name = $row->product_name;
				$price = $row->price;
				$send_message = 'Your Product for sale listing for '.$product_name.' is expiring in 2 days. Renew your subscription now to continue enjoying our services.
  Product Details:
 *Product Name: '.$product_name.'
 *Price: '.$price.'
 *Product Owner Name: '.$row->contact_number_of_owner.'
 *Product Owner Contact: '.$row->contact_name_of_owner;
			    $user_id =  $row->user_id; 
				$insertArray['userFcmToken'] = $row->fcm_id;
				
				$insertArray['message'] =$send_message;
				$insertArray['title'] =$title;
				$insertArray['scheduled_date']=$todayDate;
				$insertArray['sender_user_id']=$row->user_id;
				$insertArray['type']=5;
				$insertArray['send_flag']=1;
				$insertArray['rx_reminder_id']=0;
				$insertArray['send_date']=$todayDate;
			 
				$notifications = $this->userRepo->sendRenewReminderNotifications($insertArray);
			}
		}
	}
	
	public function sendChemistNotifications()
	{
		$todayDate = date("Y-m-d");
		$todayDate1 = Carbon::createFromFormat('Y-m-d', $todayDate);
		$date = date("Y-m-d",strtotime($todayDate1->addDays(2))); 
		 
		$results = Chemist::leftJoin('users', 'users.id', '=', 'chemists.user_id')
								->select('chemists.*','users.fcm_id')
								->where( 'chemists.subscriptionEndDate', '=', $date)
								->where( 'chemists.status',1)
								->get();
								
		 
		$title = 'Reminder: Chemist Listing Expiry';
		if($results)
		{
			foreach($results as $row)
			{
				$insertArray[] = '';
				 $name = $row->shop_name; 
				$owner_name = $row->owner_name;
				$send_message = 'Your Chemist listing for '.$name.' is expiring in 2 days. Renew your subscription now to continue enjoying our services.
 Shop Details:
 *Shop Name: '.$name.'
 *Shop Owner Name: '.$row->owner_name.'
 *Shop Owner Contact: '.$row->mobile_number;
			    $user_id =  $row->user_id; 
				$insertArray['userFcmToken'] = $row->fcm_id;
				
				$insertArray['message'] =$send_message;
				$insertArray['title'] =$title;
				$insertArray['scheduled_date']=$todayDate;
				$insertArray['sender_user_id']=$row->user_id;
				$insertArray['type']=5;
				$insertArray['send_flag']=1;
				$insertArray['rx_reminder_id']=0;
				$insertArray['send_date']=$todayDate;
			 
				$notifications = $this->userRepo->sendRenewReminderNotifications($insertArray);
			}
		}
	}

	public function sendVetHospitalsNotifications()
	{
		$todayDate = date("Y-m-d");
		$todayDate1 = Carbon::createFromFormat('Y-m-d', $todayDate);
		$date = date("Y-m-d",strtotime($todayDate1->addDays(2))); 
		 
		$results = Veterinaryhospitals::leftJoin('users', 'users.id', '=', 'veterinary_hospitals.user_id')
								->leftJoin('subcategories', 'subcategories.id', '=', 'veterinary_hospitals.sub_category')
								->select('subcategories.name as subcategory_name','veterinary_hospitals.id','veterinary_hospitals.hospital_name',
								'veterinary_hospitals.veterinary_owner_name','veterinary_hospitals.mobile_number','users.fcm_id')
								->where( 'veterinary_hospitals.subscriptionEndDate', '=', $date)
								->where( 'veterinary_hospitals.status',1)
								->where( 'labs.type','Private')
								->get();
								
		 
		$title = 'Reminder: Vet Hospitals Listing Expiry';
		if($results)
		{
			foreach($results as $row)
			{
				$insertArray[] = '';
				$name = $row->hospital_name; 
				$owner_name = $row->veterinary_owner_name;
				$send_message = 'Your Vet Hospitals listing for '.$name.' is expiring in 2 days. Renew your subscription now to continue enjoying our services.
 Vet Hospitals Details:
 *Vet Hospital Name: '.$name.'
 *Type: '.$row->subcategory_name.'
 *Owner Name: '.$owner_name.'
 *Owner Contact: '.$row->mobile_number;
			    $user_id =  $row->user_id; 
				$insertArray['userFcmToken'] = $row->fcm_id;
				
				$insertArray['message'] =$send_message;
				$insertArray['title'] =$title;
				$insertArray['scheduled_date']=$todayDate;
				$insertArray['sender_user_id']=$row->user_id;
				$insertArray['type']=5;
				$insertArray['send_flag']=1;
				$insertArray['rx_reminder_id']=0;
				$insertArray['send_date']=$todayDate;
			 
				$notifications = $this->userRepo->sendRenewReminderNotifications($insertArray);
			}
		}
	}
	
	public function sendLabNotifications()
	{
		$todayDate = date("Y-m-d");
		$todayDate1 = Carbon::createFromFormat('Y-m-d', $todayDate);
		$date = date("Y-m-d",strtotime($todayDate1->addDays(2))); 
		 
		$results = Labs::leftJoin('users', 'users.id', '=', 'labs.user_id')
								->leftJoin('subcategories', 'subcategories.id', '=', 'labs.sub_category')
								->select('subcategories.name as subcategory_name','labs.id','labs.lab_name',
								'labs.owner_name','labs.mobile_number','users.fcm_id')
								->where( 'labs.subscriptionEndDate', '=', $date)
								->where( 'labs.status',1)
								->where( 'labs.type','Private')
								->get();
								
		 
		$title = 'Reminder: Labs Listing Expiry';
		if($results)
		{
			foreach($results as $row)
			{
				$insertArray[] = '';
				$name = $row->lab_name; 
				$owner_name = $row->owner_names ;
				$send_message = 'Your Labs listing for '.$name.' is expiring in 2 days. Renew your subscription now to continue enjoying our services.
 Lab Details:
 *Lab Name: '.$name.'
 *Type: '.$row->subcategory_name.'
 *Owner Name: '.$owner_name.'
 *Owner Contact: '.$row->mobile_number;
			    $user_id =  $row->user_id; 
				$insertArray['userFcmToken'] = $row->fcm_id;
				
				$insertArray['message'] =$send_message;
				$insertArray['title'] =$title;
				$insertArray['scheduled_date']=$todayDate;
				$insertArray['sender_user_id']=$row->user_id;
				$insertArray['type']=5;
				$insertArray['send_flag']=1;
				$insertArray['rx_reminder_id']=0;
				$insertArray['send_date']=$todayDate;
			 
				$notifications = $this->userRepo->sendRenewReminderNotifications($insertArray);
			}
		}
	}
	
	public function sendSupplierNotifications()
	{
		$todayDate = date("Y-m-d");
		$todayDate1 = Carbon::createFromFormat('Y-m-d', $todayDate);
		$date = date("Y-m-d",strtotime($todayDate1->addDays(2))); 
		 
		$results = Suppliers::leftJoin('users', 'users.id', '=', 'suppliers.user_id')
								->leftJoin('subcategories', 'subcategories.id', '=', 'suppliers.sub_category')
								->select('subcategories.name as subcategory_name','suppliers.id','suppliers.supplier_name',
								'suppliers.mobile_number','users.fcm_id')
								->where( 'suppliers.subscriptionEndDate', '=', $date)
								->where( 'suppliers.status',1)
								->get();
								
		 
		$title = 'Reminder: Suppliers Listing Expiry';
		if($results)
		{
			foreach($results as $row)
			{
				$insertArray[] = '';
				$name = $row->supplier_name; 
				$send_message = 'Your Supplier listing for '.$name.' is expiring in 2 days. Renew your subscription now to continue enjoying our services.
 Supplier Details:
 *Supplier Name: '.$name.'
 *Type: '.$row->subcategory_name.'
 *Suppliers Contact: '.$row->mobile_number;
			    $user_id =  $row->user_id; 
				$insertArray['userFcmToken'] = $row->fcm_id;
				
				$insertArray['message'] =$send_message;
				$insertArray['title'] =$title;
				$insertArray['scheduled_date']=$todayDate;
				$insertArray['sender_user_id']=$row->user_id;
				$insertArray['type']=5;
				$insertArray['send_flag']=1;
				$insertArray['rx_reminder_id']=0;
				$insertArray['send_date']=$todayDate;
			 
				$notifications = $this->userRepo->sendRenewReminderNotifications($insertArray);
			}
		}
	}
	
	public function sendtraingNotifications()
	{
		$todayDate = date("Y-m-d");
		$todayDate1 = Carbon::createFromFormat('Y-m-d', $todayDate);
		$date = date("Y-m-d",strtotime($todayDate1->addDays(2))); 
		 
		$results = TrainingCenters::leftJoin('users', 'users.id', '=', 'training_centers.user_id')
								->leftJoin('subcategories', 'subcategories.id', '=', 'training_centers.sub_category')
								->select('subcategories.name as subcategory_name','training_centers.id','training_centers.training_center_name',
								'training_centers.incharge_name','training_centers.mobile_number','users.fcm_id')
								->where( 'training_centers.subscriptionEndDate', '=', $date)
								->where( 'training_centers.status',1)
								->where( 'training_centers.type','Private')
								->get();
								
		 
		$title = 'Reminder: Training Centre Listing Expiry';
		if($results)
		{
			foreach($results as $row)
			{
				$insertArray[] = '';
				$name = $row->training_center_name; 
				$owner_name = $row->incharge_name ;
				$send_message = 'Your Training Centre listing for '.$name.' is expiring in 2 days. Renew your subscription now to continue enjoying our services.
 Training Centre Details:
 *Training Centre Name: '.$name.'
 *Type: '.$row->subcategory_name.'
 *Incharge Name: '.$owner_name.'
 *Incharge Contact: '.$row->mobile_number;
			    $user_id =  $row->user_id; 
				$insertArray['userFcmToken'] = $row->fcm_id;
				
				$insertArray['message'] =$send_message;
				$insertArray['title'] =$title;
				$insertArray['scheduled_date']=$todayDate;
				$insertArray['sender_user_id']=$row->user_id;
				$insertArray['type']=5;
				$insertArray['send_flag']=1;
				$insertArray['rx_reminder_id']=0;
				$insertArray['send_date']=$todayDate;
			 
				$notifications = $this->userRepo->sendRenewReminderNotifications($insertArray);
			}
		}
	}
	
	public function sendInstitutionNotifications()
	{
		$todayDate = date("Y-m-d");
		$todayDate1 = Carbon::createFromFormat('Y-m-d', $todayDate);
		$date = date("Y-m-d",strtotime($todayDate1->addDays(2))); 
		 
		$results = Institutions::leftJoin('users', 'users.id', '=', 'institutions.user_id')
								->leftJoin('subcategories', 'subcategories.id', '=', 'institutions.sub_category')
								->select('subcategories.name as subcategory_name','institutions.id','institutions.institution_name',
								'institutions.incharge_name','institutions.mobile_number','users.fcm_id')
								->where( 'institutions.subscriptionEndDate', '=', $date)
								->where( 'institutions.status',1)
								->where( 'institutions.type','Private')
								->get();
								
		 
		$title = 'Reminder: Institution Listing Expiry';
		if($results)
		{
			foreach($results as $row)
			{ 
				$insertArray[] = '';
				$name = $row->institution_name; 
				$owner_name = $row->incharge_name ;
				$send_message = 'Your Institution listing for '.$name.' is expiring in 2 days. Renew your subscription now to continue enjoying our services.
 Institution Details:
 *Institution Name: '.$name.'
 *Type: '.$row->subcategory_name.'
 *Incharge Name: '.$owner_name.'
 *Incharge Contact: '.$row->mobile_number;
			    $user_id =  $row->user_id; 
				$insertArray['userFcmToken'] = $row->fcm_id;
				
				$insertArray['message'] =$send_message;
				$insertArray['title'] =$title;
				$insertArray['scheduled_date']=$todayDate;
				$insertArray['sender_user_id']=$row->user_id;
				$insertArray['type']=5;
				$insertArray['send_flag']=1;
				$insertArray['rx_reminder_id']=0;
				$insertArray['send_date']=$todayDate;
			 
				$notifications = $this->userRepo->sendRenewReminderNotifications($insertArray);
			}
		}
	}
	
	public function sendshopsNotifications()
	{
		$todayDate = date("Y-m-d");
		$todayDate1 = Carbon::createFromFormat('Y-m-d', $todayDate);
		$date = date("Y-m-d",strtotime($todayDate1->addDays(2))); 
		 
		$results = Shops::leftJoin('users', 'users.id', '=', 'shops.user_id')
								->leftJoin('subcategories', 'subcategories.id', '=', 'shops.sub_category')
								->select('subcategories.name as subcategory_name','shops.id','shops.shop_name',
								'shops.shop_owner_name','shops.mobile_number','users.fcm_id')
								->where( 'shops.subscriptionEndDate', '=', $date)
								->where( 'shops.status',1)
								->get();
								
		 
		$title = 'Reminder: Shop Listing Expiry';
		if($results)
		{
			foreach($results as $row)
			{ 
				$insertArray[] = '';
				$name = $row->shop_name; 
				$owner_name = $row->shop_owner_name ;
				$send_message = 'Your Shop listing for '.$name.' is expiring in 2 days. Renew your subscription now to continue enjoying our services.
 Shop Details:
 *Shop Name: '.$name.'
 *Type: '.$row->subcategory_name.'
 *Shop Name: '.$owner_name.'
 *Shop Contact: '.$row->mobile_number;
			    $user_id =  $row->user_id; 
				$insertArray['userFcmToken'] = $row->fcm_id;
				
				$insertArray['message'] =$send_message;
				$insertArray['title'] =$title;
				$insertArray['scheduled_date']=$todayDate;
				$insertArray['sender_user_id']=$row->user_id;
				$insertArray['type']=5;
				$insertArray['send_flag']=1;
				$insertArray['rx_reminder_id']=0;
				$insertArray['send_date']=$todayDate;
			 
				$notifications = $this->userRepo->sendRenewReminderNotifications($insertArray);
			}
		}
	}
	
	public function sendPoultryhatcheryNotifications()
	{
		$todayDate = date("Y-m-d");
		$todayDate1 = Carbon::createFromFormat('Y-m-d', $todayDate);
		$date = date("Y-m-d",strtotime($todayDate1->addDays(2))); 
		 
		$results = PoultryHatchery::leftJoin('users', 'users.id', '=', 'poultryhatchery_centers.user_id')
								->select('poultryhatchery_centers.id','poultryhatchery_centers.poultryhatchery_center_name',
								'poultryhatchery_centers.incharge_name','poultryhatchery_centers.mobile_number','users.fcm_id')
								->where( 'poultryhatchery_centers.subscriptionEndDate', '=', $date)
								->where( 'poultryhatchery_centers.status',1)
								->where( 'poultryhatchery_centers.type','Private')
								->get();
								
		 
		$title = 'Reminder: Poultry Hatchery Listing Expiry';
		if($results)
		{
			foreach($results as $row)
			{ 
				$insertArray[] = '';
				$name = $row->poultryhatchery_center_name; 
				$owner_name = $row->incharge_name ;
				$send_message = 'Your Poultry Hatchery listing for '.$name.' is expiring in 2 days. Renew your subscription now to continue enjoying our services.
 Poultry Hatchery Details:
 *Poultry Hatchery Name: '.$name.'
 *Incharge Name: '.$owner_name.'
 *Incharge Contact: '.$row->mobile_number;
			    $user_id =  $row->user_id; 
				$insertArray['userFcmToken'] = $row->fcm_id;
				
				$insertArray['message'] =$send_message;
				$insertArray['title'] =$title;
				$insertArray['scheduled_date']=$todayDate;
				$insertArray['sender_user_id']=$row->user_id;
				$insertArray['type']=5;
				$insertArray['send_flag']=1;
				$insertArray['rx_reminder_id']=0;
				$insertArray['send_date']=$todayDate;
			 
				$notifications = $this->userRepo->sendRenewReminderNotifications($insertArray);
			}
		}
	}
	
	public function sendPanjarpolNotifications()
	{
		$todayDate = date("Y-m-d");
		$todayDate1 = Carbon::createFromFormat('Y-m-d', $todayDate);
		$date = date("Y-m-d",strtotime($todayDate1->addDays(2))); 
		 
		$results = Panjarpol::leftJoin('users', 'users.id', '=', 'panjarpol.user_id')
								->select('panjarpol.id','panjarpol.panjarpol_name',
								'panjarpol.manager_name','panjarpol.mobile_number','users.fcm_id')
								->where( 'panjarpol.subscriptionEndDate', '=', $date)
								->where( 'panjarpol.status',1)
								->get();
								
		 
		$title = 'Reminder: Panjarpol Listing Expiry';
		if($results)
		{
			foreach($results as $row)
			{ 
				$insertArray[] = '';
				$name = $row->panjarpol_name; 
				$owner_name = $row->manager_name ;
				$send_message = 'Your Panjarpol listing for '.$name.' is expiring in 2 days. Renew your subscription now to continue enjoying our services.
 Panjarpol Details:
 *Panjarpol Name: '.$name.'
 *Manager Name: '.$owner_name.'
 *Manager Contact: '.$row->mobile_number;
			    $user_id =  $row->user_id; 
				$insertArray['userFcmToken'] = $row->fcm_id;
				
				$insertArray['message'] =$send_message;
				$insertArray['title'] =$title;
				$insertArray['scheduled_date']=$todayDate;
				$insertArray['sender_user_id']=$row->user_id;
				$insertArray['type']=5;
				$insertArray['send_flag']=1;
				$insertArray['rx_reminder_id']=0;
				$insertArray['send_date']=$todayDate;
			 
				$notifications = $this->userRepo->sendRenewReminderNotifications($insertArray);
			}
		}
	}

	public function sendMilkNotifications()
	{
		$todayDate = date("Y-m-d");
		$todayDate1 = Carbon::createFromFormat('Y-m-d', $todayDate);
		$date = date("Y-m-d",strtotime($todayDate1->addDays(2))); 
		 
		$results = MilkCollections::leftJoin('users', 'users.id', '=', 'milkcollection_centers.user_id')
								->select('milkcollection_centers.id','milkcollection_centers.milkcollection_center_name',
								'milkcollection_centers.incharge_name','milkcollection_centers.mobile_number','users.fcm_id')
								->where( 'milkcollection_centers.subscriptionEndDate', '=', $date)
								->where( 'milkcollection_centers.status',1)
								->get();
								
		 
		$title = 'Reminder: Milk Collection Centre Listing Expiry';
		if($results)
		{
			foreach($results as $row)
			{ 
				$insertArray[] = '';
				$name = $row->milkcollection_center_name; 
				$owner_name = $row->incharge_name ;
				$send_message = 'Your Milk Collection Centre listing for '.$name.' is expiring in 2 days. Renew your subscription now to continue enjoying our services.
 Milk Collection Centre Details:
 *Milk Collection Centre Name: '.$name.'
 *Incharge Name: '.$owner_name.'
 *Incharge Contact: '.$row->mobile_number;
			    $user_id =  $row->user_id; 
				$insertArray['userFcmToken'] = $row->fcm_id;
				
				$insertArray['message'] =$send_message;
				$insertArray['title'] =$title;
				$insertArray['scheduled_date']=$todayDate;
				$insertArray['sender_user_id']=$row->user_id;
				$insertArray['type']=5;
				$insertArray['send_flag']=1;
				$insertArray['rx_reminder_id']=0;
				$insertArray['send_date']=$todayDate;
			 
				$notifications = $this->userRepo->sendRenewReminderNotifications($insertArray);
			}
		}
	}

	public function sendfarmsNotifications()
	{
		$todayDate = date("Y-m-d");
		$todayDate1 = Carbon::createFromFormat('Y-m-d', $todayDate);
		$date = date("Y-m-d",strtotime($todayDate1->addDays(2))); 
		 
		$results = Farms::leftJoin('users', 'users.id', '=', 'farms.user_id')
								->select('farms.id','farms.farm_name',
								'farms.incharge_name','farms.mobile_number','users.fcm_id')
								->where( 'farms.subscriptionEndDate', '=', $date)
								->where( 'farms.status',1)
								->where( 'farms.type','Private')
								->get();
								
		 
		$title = 'Reminder: Farm Listing Expiry';
		if($results)
		{
			foreach($results as $row)
			{ 
				$insertArray[] = '';
				$name = $row->farm_name; 
				$owner_name = $row->incharge_name ;
				$send_message = 'Your Farm listing for '.$name.' is expiring in 2 days. Renew your subscription now to continue enjoying our services.
  Farm Details:
 *Farm Name: '.$name.'
 *Incharge Name: '.$owner_name.'
 *Incharge Contact: '.$row->mobile_number;
			    $user_id =  $row->user_id; 
				$insertArray['userFcmToken'] = $row->fcm_id;
				
				$insertArray['message'] =$send_message;
				$insertArray['title'] =$title;
				$insertArray['scheduled_date']=$todayDate;
				$insertArray['sender_user_id']=$row->user_id;
				$insertArray['type']=5;
				$insertArray['send_flag']=1;
				$insertArray['rx_reminder_id']=0;
				$insertArray['send_date']=$todayDate;
			 
				$notifications = $this->userRepo->sendRenewReminderNotifications($insertArray);
			}
		}
	}
	
	public function sendsheltersNotifications()
	{
		$todayDate = date("Y-m-d");
		$todayDate1 = Carbon::createFromFormat('Y-m-d', $todayDate);
		$date = date("Y-m-d",strtotime($todayDate1->addDays(2))); 
		 
		$results = DogShelters::leftJoin('users', 'users.id', '=', 'dog_shelters.user_id')
								->select('dog_shelters.id','dog_shelters.dogshelter_name',
								'dog_shelters.incharge_name','dog_shelters.mobile_number','users.fcm_id')
								->where( 'dog_shelters.subscriptionEndDate', '=', $date)
								->where( 'dog_shelters.status',1)
								->get();
								
		 
		$title = 'Reminder: Shelters Listing Expiry';
		if($results)
		{
			foreach($results as $row)
			{ 
			 
				$insertArray[] = '';
				$name = $row->dogshelter_name; 
				$owner_name = $row->incharge_name ;
				$send_message = 'Your Shelter listing for '.$name.' is expiring in 2 days. Renew your subscription now to continue enjoying our services.
  Shelter Details:
 *Shelter Name: '.$name.'
 *Incharge Name: '.$owner_name.'
 *Incharge Contact: '.$row->mobile_number;
			    $user_id =  $row->user_id; 
				$insertArray['userFcmToken'] = $row->fcm_id;
				
				$insertArray['message'] =$send_message;
				$insertArray['title'] =$title;
				$insertArray['scheduled_date']=$todayDate;
				$insertArray['sender_user_id']=$row->user_id;
				$insertArray['type']=5;
				$insertArray['send_flag']=1;
				$insertArray['rx_reminder_id']=0;
				$insertArray['send_date']=$todayDate;
			 
				$notifications = $this->userRepo->sendRenewReminderNotifications($insertArray);
			}
		}
	}
	
	public function userPashumitraSubscriptionEnd()
	{
		$input[] ='';
		$input['sRoleName'] ='Pashumitra';
		$results = $this->userRepo->getUsersFcmIds();
		
		$todayDate = date("Y-m-d");
		$todayDate1 = Carbon::createFromFormat('Y-m-d', $todayDate);
		$date = date("Y-m-d",strtotime($todayDate1->addDays(2))); 
		 
		$results = DogShelters::leftJoin('users', 'users.id', '=', 'dog_shelters.user_id')
								->select('dog_shelters.id','dog_shelters.dogshelter_name',
								'dog_shelters.incharge_name','dog_shelters.mobile_number','users.fcm_id')
								->where( 'dog_shelters.subscriptionEndDate', '=', $date)
								->where( 'dog_shelters.status',1)
								->get();
								
		 
		$title = 'Reminder: Shelters Listing Expiry';
		if($results)
		{
			foreach($results as $row)
			{ 
			 
				$insertArray[] = '';
				$name = $row->dogshelter_name; 
				$owner_name = $row->incharge_name ;
				$send_message = 'Your Shelter listing for '.$name.' is expiring in 2 days. Renew your subscription now to continue enjoying our services.
  Shelter Details:
 *Shelter Name: '.$name.'
 *Incharge Name: '.$owner_name.'
 *Incharge Contact: '.$row->mobile_number;
			    $user_id =  $row->user_id; 
				$insertArray['userFcmToken'] = $row->fcm_id;
				
				$insertArray['message'] =$send_message;
				$insertArray['title'] =$title;
				$insertArray['scheduled_date']=$todayDate;
				$insertArray['sender_user_id']=$row->user_id;
				$insertArray['type']=5;
				$insertArray['send_flag']=1;
				$insertArray['rx_reminder_id']=0;
				$insertArray['send_date']=$todayDate;
			 
				$notifications = $this->userRepo->sendRenewReminderNotifications($insertArray);
			}
		}
		
	}
	
	public function userRegVetSubscriptionEnd()
	{
		$input[] ='';
		$input['sRoleName'] ='Registered-vet';
		$users = $this->userRepo->getUsersFcmIds($input);
		
	}

}
