<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\AnimalController;
use App\Http\Controllers\Backend\BreedController;
use App\Http\Controllers\Backend\BookController;
use App\Http\Controllers\Backend\ChemistController;
use App\Http\Controllers\Backend\TransporterController;
use App\Http\Controllers\Backend\SpeciesController;
use App\Http\Controllers\Backend\CharacteristicsController;
use App\Http\Controllers\Backend\UserController; 
use App\Http\Controllers\Backend\LoginController;
use App\Http\Controllers\Backend\AnimalownerController;
use App\Http\Controllers\Backend\PashumitraController;
use App\Http\Controllers\Backend\RegisteredvetController;
use App\Http\Controllers\Backend\AnimalsaleController;
use App\Http\Controllers\Backend\ProductsaleController;
use App\Http\Controllers\Backend\ProductController;
use App\Http\Controllers\Backend\AddanimalController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\Front\FrontPagesController;
use App\Http\Controllers\Backend\ContentManagementController;
use App\Http\Controllers\Crons\NotificationController;
use App\Http\Controllers\Backend\FeesController;
use App\Http\Controllers\Backend\BreederController;
use App\Http\Controllers\Backend\AdvertisementController;
use App\Http\Controllers\Backend\PaymentReportController;
use App\Http\Controllers\Backend\TestimonialController;
use App\Http\Controllers\Front\InvoiceController;
use App\Http\Controllers\Backend\RatingsController;
use App\Http\Controllers\Backend\SuppliersController;
use App\Http\Controllers\Backend\VetHospitalsController;
use App\Http\Controllers\Backend\TrainingCentersController; 
use App\Http\Controllers\Backend\FarmsController;
use App\Http\Controllers\Backend\InstitutionsController;
use App\Http\Controllers\Backend\DogShelterController;
use App\Http\Controllers\Backend\PanjarpolController;
use App\Http\Controllers\Backend\MilkCollectionController;
use App\Http\Controllers\Backend\PoultryHatcheryController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



/*
Front Website

*/

Route::get('notifications/getNotificationsToSend', [NotificationController::class, 'getNotificationsToSend'])->name('getNotificationsToSend');

Route::get('/home', [FrontPagesController::class, 'index'])->name('index');
Route::get('/about-us', [FrontPagesController::class, 'aboutus'])->name('about-us');
Route::get('/contact-us', [FrontPagesController::class, 'contactus'])->name('contact-us');
Route::get('/library', [FrontPagesController::class, 'library'])->name('library');
Route::get('/terms-conditions', [FrontPagesController::class, 'termsConditions'])->name('terms-conditions');
Route::get('/privacy-policy', [FrontPagesController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('/government-schemes', [FrontPagesController::class, 'governmentSchemes'])->name('government-schemes');
Route::get('/csr-activities', [FrontPagesController::class, 'csrActivities'])->name('csr-activities');

//receipt download from mobile app notification
Route::get('invoice/download/{uid}/{id}', [InvoiceController::class, 'downloadReceipt'])->name('downloadReceipt');


//Route::get('/test', [TestController::class, 'test'])->name('test'); 
Route::get('/update-permission', [TestController::class, 'updatePermission']); 

Route::get('/', [FrontPagesController::class, 'index'])->name('index');

Route::get('/{file_id?}/library-download', [FrontPagesController::class, 'getDownload'])->name('library.download');

Route::get('/changeFiles', [FrontPagesController::class, 'changeFiles'])->name('changeFiles');

/*Route::get('/', function () {
    //return redirect('/auth/login');
	return redirect('/home');
});*/


Route::get('/pashumitra', function () {
    return redirect('pashumitra/auth/login');
	
});

Route::get('/clear-cache', function() {
    Artisan::call('optimize:clear');
    echo Artisan::output();
});
Route::get('pashumitra/get-cities', [CommonController::class, 'get_cities'])->name('get-cities'); 

//Login
Route::group([
  'prefix' => 'pashumitra/auth',
  'as' => 'auth.',
  ],function () { 
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login'); 
    Route::post('/login', [LoginController::class, 'checkCredential']); 
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout'); 
     
});

Route::middleware(['auth'])->group(function () {//,'check.role'
  Route::get('pashumitra/dashboard', [HomeController::class, 'index'])->name('home'); 
	Route::get('pashumitra/profile', [HomeController::class, 'profile'])->name('profile');
	Route::post('/update/{id?}/profile', [HomeController::class, 'updateProfile'])->name('update.profile');
	Route::post('/change-password', [HomeController::class, 'changePassword'])->name('change.password');
  Route::post('/mobile-verify', [HomeController::class, 'mobileVerify'])->name('mobile.verify');
  Route::post('/update-mobile', [HomeController::class, 'updateMobile'])->name('update.mobile');
  ##Activity Logs
  Route::get('logs', [HomeController::class, 'getLogs'])->name('logs');
  Route::get('/ajax-data', [HomeController::class, 'ajaxData'])->name('log.ajax'); 
     
    //Role module
    Route::group([
        'prefix' => 'pashumitra/role',
        'as' => 'role.',
      ], function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::get('/create', [RoleController::class, 'create'])->name('create');
        Route::post('/store', [RoleController::class, 'store'])->name('store'); 
        Route::get('/{id?}/edit', [RoleController::class, 'edit'])->name('edit'); 
        Route::post('/{id?}/update', [RoleController::class, 'update'])->name('update'); 
        Route::get('/{id?}/delete', [RoleController::class, 'delete'])->name('delete'); 
    });

    //Animal type module
    Route::group([
        'prefix' => 'pashumitra/animal',
        'as' => 'animal.',
      ], function () {
        Route::get('/', [AnimalController::class, 'index'])->name('index-type');
        Route::get('/create-type', [AnimalController::class, 'create'])->name('create-type');
        Route::post('/store-type', [AnimalController::class, 'store'])->name('store-type'); 
        Route::get('/{id?}/edit-type', [AnimalController::class, 'edit'])->name('edit-type'); 
        Route::post('/{id?}/update-type', [AnimalController::class, 'update'])->name('update-type'); 
        Route::get('/{id?}/delete-type', [AnimalController::class, 'delete'])->name('delete-type'); 
    });

    //Animal Breed module
    Route::group([
        'prefix' => 'pashumitra/breed',
        'as' => 'breed.',
      ], function () {
        Route::get('/', [BreedController::class, 'index'])->name('index');
        Route::get('/create', [BreedController::class, 'create'])->name('create');
        Route::post('/store', [BreedController::class, 'store'])->name('store'); 
        Route::get('/{id?}/edit', [BreedController::class, 'edit'])->name('edit'); 
        Route::post('/{id?}/update', [BreedController::class, 'update'])->name('update'); 
        Route::get('/{id?}/delete', [BreedController::class, 'delete'])->name('delete'); 
    });

    //Animal species module
    Route::group([
        'prefix' => 'pashumitra/species',
        'as' => 'species.',
      ], function () {
        Route::get('/', [SpeciesController::class, 'index'])->name('index');
        Route::get('/create', [SpeciesController::class, 'create'])->name('create');
        Route::post('/store', [SpeciesController::class, 'store'])->name('store'); 
        Route::get('/{id?}/edit', [SpeciesController::class, 'edit'])->name('edit'); 
        Route::post('/{id?}/update', [SpeciesController::class, 'update'])->name('update'); 
        Route::get('/{id?}/delete', [SpeciesController::class, 'delete'])->name('delete'); 
    });

    //Animal Characterestics module
    Route::group([
        'prefix' => 'pashumitra/characteristics',
        'as' => 'characteristics.',
      ], function () {
        Route::get('/', [CharacteristicsController::class, 'index'])->name('index');
        Route::get('/create', [CharacteristicsController::class, 'create'])->name('create');
        Route::post('/store', [CharacteristicsController::class, 'store'])->name('store'); 
        Route::get('/{id?}/edit', [CharacteristicsController::class, 'edit'])->name('edit'); 
        Route::post('/{id?}/update', [CharacteristicsController::class, 'update'])->name('update'); 
        Route::get('/{id?}/delete', [CharacteristicsController::class, 'delete'])->name('delete'); 
    });

    //Chemist module
    Route::group([
        'prefix' => 'pashumitra/chemist',
        'as' => 'chemist.',
      ], function () {
        Route::get('/', [ChemistController::class, 'index'])->name('index');
        Route::get('/create', [ChemistController::class, 'create'])->name('create');
        Route::get('/{id?}/detail', [ChemistController::class, 'detail'])->name('detail');
        Route::post('/store', [ChemistController::class, 'store'])->name('store'); 
        Route::get('/{id?}/edit', [ChemistController::class, 'edit'])->name('edit'); 
        Route::post('/{id?}/update', [ChemistController::class, 'update'])->name('update'); 
        Route::get('/{id?}/delete', [ChemistController::class, 'delete'])->name('delete'); 
        Route::get('/{id?}/remove', [ChemistController::class, 'removeImage'])->name('remove'); 
    });
	
	 //Supplier module
    Route::group([
        'prefix' => 'pashumitra/suppliers',
        'as' => 'suppliers.',
      ], function () {
        Route::get('/', [SuppliersController::class, 'index'])->name('index');
        Route::get('/create', [SuppliersController::class, 'create'])->name('create');
        Route::get('/{id?}/detail', [SuppliersController::class, 'detail'])->name('detail');
        Route::post('/store', [SuppliersController::class, 'store'])->name('store'); 
        Route::get('/{id?}/edit', [SuppliersController::class, 'edit'])->name('edit'); 
        Route::post('/{id?}/update', [SuppliersController::class, 'update'])->name('update'); 
        Route::get('/{id?}/delete', [SuppliersController::class, 'delete'])->name('delete'); 
        Route::get('/{id?}/remove', [SuppliersController::class, 'removeImage'])->name('remove');
		Route::get('/suppliers-list', [SuppliersController::class, 'getAjaxList'])->name('list');		
    });
	
	//hospitals module
    Route::group([
        'prefix' => 'pashumitra/hospitals',
        'as' => 'hospitals.',
      ], function () {
        Route::get('/', [VetHospitalsController::class, 'index'])->name('index');
        Route::get('/create', [VetHospitalsController::class, 'create'])->name('create');
        Route::get('/{id?}/detail', [VetHospitalsController::class, 'detail'])->name('detail');
        Route::post('/store', [VetHospitalsController::class, 'store'])->name('store'); 
        Route::get('/{id?}/edit', [VetHospitalsController::class, 'edit'])->name('edit'); 
        Route::post('/{id?}/update', [VetHospitalsController::class, 'update'])->name('update'); 
        Route::get('/{id?}/delete', [VetHospitalsController::class, 'delete'])->name('delete'); 
        Route::get('/{id?}/remove', [VetHospitalsController::class, 'removeImage'])->name('remove');
		Route::get('/hospitals-list', [VetHospitalsController::class, 'getAjaxList'])->name('list');		
    });
	
	//training center module
    Route::group([
        'prefix' => 'pashumitra/trainingcenters',
        'as' => 'trainingcenters.',
      ], function () {
        Route::get('/', [TrainingCentersController::class, 'index'])->name('index');
        Route::get('/create', [TrainingCentersController::class, 'create'])->name('create');
        Route::get('/{id?}/detail', [TrainingCentersController::class, 'detail'])->name('detail');
        Route::post('/store', [TrainingCentersController::class, 'store'])->name('store'); 
        Route::get('/{id?}/edit', [TrainingCentersController::class, 'edit'])->name('edit'); 
        Route::post('/{id?}/update', [TrainingCentersController::class, 'update'])->name('update'); 
        Route::get('/{id?}/delete', [TrainingCentersController::class, 'delete'])->name('delete'); 
        Route::get('/{id?}/remove', [TrainingCentersController::class, 'removeImage'])->name('remove');
		Route::get('/trainingcenters-list', [TrainingCentersController::class, 'getAjaxList'])->name('list');		
    });
	
	//panjarpols module
    Route::group([
        'prefix' => 'pashumitra/panjarpols',
        'as' => 'panjarpols.',
      ], function () {
        Route::get('/', [PanjarpolController::class, 'index'])->name('index');
        Route::get('/create', [PanjarpolController::class, 'create'])->name('create');
        Route::get('/{id?}/detail', [PanjarpolController::class, 'detail'])->name('detail');
        Route::post('/store', [PanjarpolController::class, 'store'])->name('store'); 
        Route::get('/{id?}/edit', [PanjarpolController::class, 'edit'])->name('edit'); 
        Route::post('/{id?}/update', [PanjarpolController::class, 'update'])->name('update'); 
        Route::get('/{id?}/delete', [PanjarpolController::class, 'delete'])->name('delete'); 
        Route::get('/{id?}/remove', [PanjarpolController::class, 'removeImage'])->name('remove');
		Route::get('/panjarpols-list', [PanjarpolController::class, 'getAjaxList'])->name('list');		
    });
	
	//milkcollection module
    Route::group([
        'prefix' => 'pashumitra/milkcollections',
        'as' => 'milkcollections.',
      ], function () {
        Route::get('/', [MilkCollectionController::class, 'index'])->name('index');
        Route::get('/create', [MilkCollectionController::class, 'create'])->name('create');
        Route::get('/{id?}/detail', [MilkCollectionController::class, 'detail'])->name('detail');
        Route::post('/store', [MilkCollectionController::class, 'store'])->name('store'); 
        Route::get('/{id?}/edit', [MilkCollectionController::class, 'edit'])->name('edit'); 
        Route::post('/{id?}/update', [MilkCollectionController::class, 'update'])->name('update'); 
        Route::get('/{id?}/delete', [MilkCollectionController::class, 'delete'])->name('delete'); 
        Route::get('/{id?}/remove', [MilkCollectionController::class, 'removeImage'])->name('remove');
		Route::get('/milkcollections-list', [MilkCollectionController::class, 'getAjaxList'])->name('list');		
    });
	
	//hatchery module
    Route::group([
        'prefix' => 'pashumitra/poultryhatchery',
        'as' => 'poultryhatchery.',
      ], function () {
        Route::get('/', [PoultryHatcheryController::class, 'index'])->name('index');
        Route::get('/create', [PoultryHatcheryController::class, 'create'])->name('create');
        Route::get('/{id?}/detail', [PoultryHatcheryController::class, 'detail'])->name('detail');
        Route::post('/store', [PoultryHatcheryController::class, 'store'])->name('store'); 
        Route::get('/{id?}/edit', [PoultryHatcheryController::class, 'edit'])->name('edit'); 
        Route::post('/{id?}/update', [PoultryHatcheryController::class, 'update'])->name('update'); 
        Route::get('/{id?}/delete', [PoultryHatcheryController::class, 'delete'])->name('delete'); 
        Route::get('/{id?}/remove', [PoultryHatcheryController::class, 'removeImage'])->name('remove');
		Route::get('/poultryhatchery-list', [PoultryHatcheryController::class, 'getAjaxList'])->name('list');		
    });
	
	//dogshelters module
    Route::group([
        'prefix' => 'pashumitra/dogshelters',
        'as' => 'dogshelters.',
      ], function () {
        Route::get('/', [DogShelterController::class, 'index'])->name('index');
        Route::get('/create', [DogShelterController::class, 'create'])->name('create');
        Route::get('/{id?}/detail', [DogShelterController::class, 'detail'])->name('detail');
        Route::post('/store', [DogShelterController::class, 'store'])->name('store'); 
        Route::get('/{id?}/edit', [DogShelterController::class, 'edit'])->name('edit'); 
        Route::post('/{id?}/update', [DogShelterController::class, 'update'])->name('update'); 
        Route::get('/{id?}/delete', [DogShelterController::class, 'delete'])->name('delete'); 
        Route::get('/{id?}/remove', [DogShelterController::class, 'removeImage'])->name('remove');
		Route::get('/dogshelters-list', [DogShelterController::class, 'getAjaxList'])->name('list');		
    });
	//Institutions module
    Route::group([
        'prefix' => 'pashumitra/institutions',
        'as' => 'institutions.',
      ], function () {
        Route::get('/', [InstitutionsController::class, 'index'])->name('index');
        Route::get('/create', [InstitutionsController::class, 'create'])->name('create');
        Route::get('/{id?}/detail', [InstitutionsController::class, 'detail'])->name('detail');
        Route::post('/store', [InstitutionsController::class, 'store'])->name('store'); 
        Route::get('/{id?}/edit', [InstitutionsController::class, 'edit'])->name('edit'); 
        Route::post('/{id?}/update', [InstitutionsController::class, 'update'])->name('update'); 
        Route::get('/{id?}/delete', [InstitutionsController::class, 'delete'])->name('delete'); 
        Route::get('/{id?}/remove', [InstitutionsController::class, 'removeImage'])->name('remove');
		Route::get('/institutions-list', [InstitutionsController::class, 'getAjaxList'])->name('list');		
    });
	
	Route::group([
        'prefix' => 'pashumitra/farms',
        'as' => 'farms.',
      ], function () {
        Route::get('/', [FarmsController::class, 'index'])->name('index');
        Route::get('/create', [FarmsController::class, 'create'])->name('create');
        Route::get('/{id?}/detail', [FarmsController::class, 'detail'])->name('detail');
        Route::post('/store', [FarmsController::class, 'store'])->name('store'); 
        Route::get('/{id?}/edit', [FarmsController::class, 'edit'])->name('edit'); 
        Route::post('/{id?}/update', [FarmsController::class, 'update'])->name('update'); 
        Route::get('/{id?}/delete', [FarmsController::class, 'delete'])->name('delete'); 
        Route::get('/{id?}/remove', [FarmsController::class, 'removeImage'])->name('remove');
		Route::get('/farms-list', [FarmsController::class, 'getAjaxList'])->name('list');		
    });
    
    //Transporter module
    Route::group([
      'prefix' => 'pashumitra/transporter',
      'as' => 'transporter.',
    ], function () {
      Route::get('/', [TransporterController::class, 'index'])->name('index');
      Route::get('/create', [TransporterController::class, 'create'])->name('create');
      Route::post('/store', [TransporterController::class, 'store'])->name('store'); 
      Route::get('/{id?}/detail', [TransporterController::class, 'detail'])->name('detail');
        Route::get('/{id?}/edit', [TransporterController::class, 'edit'])->name('edit'); 
        Route::post('/{id?}/update', [TransporterController::class, 'update'])->name('update'); 
        Route::get('/{id?}/delete', [TransporterController::class, 'delete'])->name('delete'); 
        Route::get('/{id?}/remove', [TransporterController::class, 'removeImage'])->name('remove');
		Route::get('/transporters-list', [TransporterController::class, 'getAjaxList'])->name('list');		
    });

    //Library module
    Route::group([
        'prefix' => 'pashumitra/book',
        'as' => 'book.',
      ], function () {
        Route::get('/', [BookController::class, 'index'])->name('index');
        Route::get('/create', [BookController::class, 'create'])->name('create');
        Route::post('/store', [BookController::class, 'store'])->name('store'); 
        Route::get('/{id?}/edit', [BookController::class, 'edit'])->name('edit'); 
        Route::post('/{id?}/update', [BookController::class, 'update'])->name('update'); 
        Route::get('/{id?}/delete', [BookController::class, 'delete'])->name('delete'); 
        Route::get('/{file_name?}/download', [BookController::class, 'getDownload'])->name('download'); 
        
    });
	
	 //fees module
    Route::group([
        'prefix' => 'pashumitra/fees',
        'as' => 'fees.',
      ], function () {
        Route::get('/', [FeesController::class, 'index'])->name('index');
        Route::get('/create', [FeesController::class, 'create'])->name('create');
        Route::post('/store', [FeesController::class, 'store'])->name('store'); 
        Route::get('/{id?}/edit', [FeesController::class, 'edit'])->name('edit'); 
        Route::post('/{id?}/update', [FeesController::class, 'update'])->name('update'); 
        Route::get('/{id?}/delete', [FeesController::class, 'delete'])->name('delete'); 
       
        
    });
	
	//advertisements module
    Route::group([
        'prefix' => 'pashumitra/advertisements',
        'as' => 'advertisements.',
      ], function () {
        Route::get('/', [AdvertisementController::class, 'index'])->name('index');
        Route::get('/create', [AdvertisementController::class, 'create'])->name('create');
        Route::post('/store', [AdvertisementController::class, 'store'])->name('store'); 
        Route::get('/{id?}/edit', [AdvertisementController::class, 'edit'])->name('edit'); 
        Route::post('/{id?}/update', [AdvertisementController::class, 'update'])->name('update'); 
        Route::get('/{id?}/delete', [AdvertisementController::class, 'delete'])->name('delete'); 
		 Route::get('/{id?}/detail', [AdvertisementController::class, 'detail'])->name('detail');
       
        
    });
	
	//testimonials module
    Route::group([
        'prefix' => 'pashumitra/testimonials',
        'as' => 'testimonials.',
      ], function () {
        Route::get('/', [TestimonialController::class, 'index'])->name('index');
        Route::get('/create', [TestimonialController::class, 'create'])->name('create');
        Route::post('/store', [TestimonialController::class, 'store'])->name('store'); 
        Route::get('/{id?}/edit', [TestimonialController::class, 'edit'])->name('edit'); 
        Route::post('/{id?}/update', [TestimonialController::class, 'update'])->name('update'); 
        Route::get('/{id?}/delete', [TestimonialController::class, 'delete'])->name('delete');
    });
	
	//rating module
    Route::group([
        'prefix' => 'pashumitra/ratings',
        'as' => 'ratings.',
      ], function () {
        Route::get('/', [RatingsController::class, 'index'])->name('index');
        Route::get('/{id?}/delete', [RatingsController::class, 'delete'])->name('delete');
    });

    //Animal Owner module
    Route::group([
        'prefix' => 'pashumitra/animal-owner',
        'as' => 'animal-owner.',
      ], function () {
        Route::get('/', [AnimalownerController::class, 'index'])->name('index');
        Route::get('/create', [AnimalownerController::class, 'create'])->name('create');
        Route::post('/store', [AnimalownerController::class, 'store'])->name('store'); 
        Route::get('/animal-owner-list', [AnimalownerController::class, 'getAjaxUser'])->name('list');
        Route::get('/{id?}/edit', [AnimalownerController::class, 'edit'])->name('edit'); 
        Route::post('/{id?}/update', [AnimalownerController::class, 'update'])->name('update'); 
        Route::get('/{id?}/delete', [AnimalownerController::class, 'delete'])->name('delete'); 
        Route::get('/{id?}/detail', [AnimalownerController::class, 'userDetail'])->name('detail');        
      });
      
      //Animal Owner module
      Route::group([
        'prefix' => 'pashumitra/pashumitra',
        'as' => 'pashumitra.',
      ], function () {
        Route::get('/', [PashumitraController::class, 'index'])->name('index');
        Route::get('/create', [PashumitraController::class, 'create'])->name('create');
        Route::post('/store', [PashumitraController::class, 'store'])->name('store'); 
        Route::get('/pashumitra-list', [PashumitraController::class, 'getAjaxUser'])->name('list');
        Route::get('/{id?}/edit', [PashumitraController::class, 'edit'])->name('edit'); 
        Route::post('/{id?}/update', [PashumitraController::class, 'update'])->name('update'); 
        Route::get('/{id?}/delete', [PashumitraController::class, 'delete'])->name('delete'); 
        Route::get('/{id?}/detail', [PashumitraController::class, 'userDetail'])->name('detail');
		Route::get('/{id?}/pashumitra-verify', [PashumitraController::class, 'pashumitraVerify'])->name('pashumitra-verify');        
    });

    //Animal Owner module
    Route::group([
        'prefix' => 'pashumitra/registered-vet',
        'as' => 'registered-vet.',
      ], function () {
        Route::get('/', [RegisteredvetController::class, 'index'])->name('index');
        Route::get('/create', [RegisteredvetController::class, 'create'])->name('create');
        Route::get('/list', [RegisteredvetController::class, 'getAjaxUser'])->name('list');
        Route::post('/store', [RegisteredvetController::class, 'store'])->name('store'); 
        Route::get('/{id?}/edit', [RegisteredvetController::class, 'edit'])->name('edit'); 
        Route::post('/{id?}/update', [RegisteredvetController::class, 'update'])->name('update'); 
        Route::get('/{id?}/delete', [RegisteredvetController::class, 'delete'])->name('delete'); 
        Route::get('/{id?}/detail', [RegisteredvetController::class, 'userDetail'])->name('detail');
		Route::get('/{id?}/registeredvet-verify', [RegisteredvetController::class, 'registeredvetVerify'])->name('registeredvet-verify');        
    });

    //User module
    Route::group([
        'prefix' => 'pashumitra/user',
        'as' => 'user.', 
      ], function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::get('/user-list', [UserController::class, 'getAjaxUser'])->name('list');
        Route::get('/get-role-user', [UserController::class, 'getRoleWiseUser'])->name('role');
        Route::post('/store', [UserController::class, 'store'])->name('store');
        Route::get('/{id?}/edit', [UserController::class, 'edit'])->name('edit'); 
        Route::post('/{id?}/update', [UserController::class, 'update'])->name('update'); 
        Route::get('/{id?}/delete', [UserController::class, 'delete'])->name('delete');  
        Route::get('/{id?}/detail', [UserController::class, 'userDetail'])->name('detail');        
    });
    

    //Animal for sale module
    Route::group([
        'prefix' => 'pashumitra/animal-sale',
        'as' => 'animal-sale.', 
      ], function () {
        Route::get('/', [AnimalsaleController::class, 'index'])->name('index');
        Route::get('/create', [AnimalsaleController::class, 'create'])->name('create');
        Route::get('/user-list', [AnimalsaleController::class, 'getAjaxUser'])->name('list');
        Route::get('/get-role-user', [AnimalsaleController::class, 'getRoleWiseUser'])->name('role');
        Route::post('/store', [AnimalsaleController::class, 'store'])->name('store');
        Route::get('/{id?}/edit', [AnimalsaleController::class, 'edit'])->name('edit'); 
        Route::post('/{id?}/update', [AnimalsaleController::class, 'update'])->name('update'); 
        Route::get('/{id?}/delete', [AnimalsaleController::class, 'delete'])->name('delete');  
        Route::get('/{id?}/detail', [AnimalsaleController::class, 'detail'])->name('detail');        
        Route::get('/{id?}/remove', [AnimalsaleController::class, 'removeImage'])->name('remove');        
    });

    //Animal for sale module
    Route::group([
        'prefix' => 'pashumitra/product-sale',
        'as' => 'product-sale.', 
      ], function () {
        Route::get('/', [ProductsaleController::class, 'index'])->name('index');
        Route::get('/create', [ProductsaleController::class, 'create'])->name('create');
        Route::get('/user-list', [ProductsaleController::class, 'getAjaxUser'])->name('list');
        Route::get('/get-role-user', [ProductsaleController::class, 'getRoleWiseUser'])->name('role');
        Route::post('/store', [ProductsaleController::class, 'store'])->name('store');
        Route::get('/{id?}/edit', [ProductsaleController::class, 'edit'])->name('edit'); 
        Route::post('/{id?}/update', [ProductsaleController::class, 'update'])->name('update'); 
        Route::get('/{id?}/delete', [ProductsaleController::class, 'delete'])->name('delete');  
        Route::get('/{id?}/detail', [ProductsaleController::class, 'detail'])->name('detail');        
        Route::get('/{id?}/remove', [ProductsaleController::class, 'removeImage'])->name('remove');
		Route::get('/product-sale-list', [ProductsaleController::class, 'getAjaxList'])->name('list');        
    });

    //Animal add module
    Route::group([
        'prefix' => 'pashumitra/add-animal',
        'as' => 'add-animal.', 
      ], function () {
        Route::get('/', [AddanimalController::class, 'index'])->name('index');
        Route::get('/create', [AddanimalController::class, 'create'])->name('create');
        Route::get('/user-list', [AddanimalController::class, 'getAjaxUser'])->name('list');
        Route::get('/get-role-user', [AddanimalController::class, 'getRoleWiseUser'])->name('role');
        Route::post('/store', [AddanimalController::class, 'store'])->name('store');
        Route::get('/{id?}/edit', [AddanimalController::class, 'edit'])->name('edit'); 
        Route::post('/{id?}/update', [AddanimalController::class, 'update'])->name('update'); 
        Route::get('/{id?}/delete', [AddanimalController::class, 'delete'])->name('delete');  
        Route::get('/{id?}/detail', [AddanimalController::class, 'detail'])->name('detail');        
        Route::get('/{id?}/remove', [AddanimalController::class, 'removeImage'])->name('remove');        
    });

    //Animal add module
    Route::group([
        'prefix' => 'pashumitra/add-product',
        'as' => 'add-product.', 
      ], function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::get('/user-list', [ProductController::class, 'getAjaxUser'])->name('list');
        Route::get('/get-role-user', [ProductController::class, 'getRoleWiseUser'])->name('role');
        Route::post('/store', [ProductController::class, 'store'])->name('store');
        Route::get('/{id?}/edit', [ProductController::class, 'edit'])->name('edit'); 
        Route::post('/{id?}/update', [ProductController::class, 'update'])->name('update'); 
        Route::get('/{id?}/delete', [ProductController::class, 'delete'])->name('delete');  
        Route::get('/{id?}/detail', [ProductController::class, 'userDetail'])->name('detail');        
        Route::get('/{id?}/remove', [ProductController::class, 'removeImage'])->name('remove');        
    });
	
	//Content management module
    Route::group([
        'prefix' => 'pashumitra/content-management',
        'as' => 'content-management.',
      ], function () {
        Route::get('/', [ContentManagementController::class, 'index'])->name('index');
        Route::get('/create', [ContentManagementController::class, 'create'])->name('create');
        Route::post('/store', [ContentManagementController::class, 'store'])->name('store'); 
        Route::get('/{id?}/edit', [ContentManagementController::class, 'edit'])->name('edit'); 
        Route::post('/{id?}/update', [ContentManagementController::class, 'update'])->name('update'); 
        Route::get('/{id?}/delete', [ContentManagementController::class, 'delete'])->name('delete');
    });
	
	//Breeder module
    Route::group([
        'prefix' => 'pashumitra/breeders',
        'as' => 'breeders.', 
      ], function () {
        Route::get('/', [BreederController::class, 'index'])->name('index');
        Route::get('/create', [BreederController::class, 'create'])->name('create');
        Route::get('/user-list', [BreederController::class, 'getAjaxUser'])->name('list');
        Route::get('/get-role-user', [BreederController::class, 'getRoleWiseUser'])->name('role');
        Route::post('/store', [BreederController::class, 'store'])->name('store');
        Route::get('/{id?}/edit', [BreederController::class, 'edit'])->name('edit'); 
        Route::post('/{id?}/update', [BreederController::class, 'update'])->name('update'); 
        Route::get('/{id?}/delete', [BreederController::class, 'delete'])->name('delete');  
        Route::get('/{id?}/detail', [BreederController::class, 'detail'])->name('detail');        
        Route::get('/{id?}/remove', [BreederController::class, 'removeImage'])->name('remove');        
    });
	
	//payment report module
    Route::group([
        'prefix' => 'pashumitra/paymentreport',
        'as' => 'paymentreport.', 
      ], function () {
        Route::get('/', [PaymentReportController::class, 'index'])->name('index');
		Route::get('/regPaymentReport', [PaymentReportController::class, 'registrationPaymentReport'])->name('regPaymentReport');
        Route::get('/{id?}/detail', [PaymentReportController::class, 'detail'])->name('detail');        
                
    });
    
});

    