<?php
use App\Http\Controllers\Api\CommonController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AnimalsaleController;
use App\Http\Controllers\Api\PaymentController; 
use App\Http\Controllers\Api\BreederController;
use App\Http\Controllers\Api\PashumitraController;
use App\Http\Controllers\Api\RegisteredvetController;
use App\Http\Controllers\Api\AddanimalController;
use App\Http\Controllers\Api\RxreminderController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ChemistController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\AdvertisementController;
use App\Http\Controllers\Api\RatingsController;
use App\Http\Controllers\Api\TransporterController;
use App\Http\Controllers\Api\ProductsaleController;

Route::group(['middleware' => ['cors']], function () {
	 
	Route::get('/get-species', [CommonController::class,'getSpecies']); 
	Route::get('/get-breeds', [CommonController::class,'getBreeds']);
	Route::get('/get-library', [AuthController::class,'getLibrary']);
	
	 Route::group(['middleware' => ['api-token']], function () {
	
	/* Animal sale*/
	Route::post('/add-animalsale', [AnimalsaleController::class,'addAnimalForSale']); 

	/* Breeder */
	Route::post('/add-breeder', [BreederController::class,'addBreeder']);
	
	/* Add animal*/
	Route::post('/add-animal', [AddanimalController::class,'addAnimal']); 
	Route::post('/delete-animal', [AddanimalController::class,'deleteAnimal']); 
	
	/* Add chemist*/
	Route::post('/add-chemist', [ChemistController::class,'addChemist']); 
	
	/* Rxreminder */
	Route::post('/add-rxreminder', [RxreminderController::class,'addRxreminder']);
	Route::get('/get-user-info', [RxreminderController::class,'getUserInfoUsingMobile']);
	Route::get('/get-animalowner-list', [RxreminderController::class,'getAnimalOwnerList']);
	Route::get('/get-animal-list', [RxreminderController::class,'getAnimalNameList']);
	Route::get('/get-rxreminder-history', [RxreminderController::class,'getRxReminderHistory']);
	Route::get('/get-rxreminder-history-details', [RxreminderController::class,'getRxReminderHistoryDetails']);
	
	/* payment */
	Route::post('/get-orderid', [PaymentController::class,'generatePaymentOrderId']);
	Route::post('/create-payment', [PaymentController::class,'addPayments']);
	Route::get('/get-config', [PaymentController::class,'getConfig']);
	
	Route::get('/get-notifications', [NotificationController::class,'getNotificationList']); 
	Route::get('/get-notification-details', [NotificationController::class,'getNotificationDetails']); 
	
	Route::get('/setting', [CommonController::class,'getSetting']);
	
	/* ratings Details */
	Route::post('/add-ratings', [RatingsController::class,'addRatings']);
	/* Add Transporter */
	Route::post('/add-transporter', [TransporterController::class,'addTransporter']);
	/* Add Product sale */
	Route::post('/add-productsale', [ProductsaleController::class,'addProductSale']);
	});
	
	Route::get('/get-average-rating', [RatingsController::class,'getAverageRatings']);
	Route::get('/get-rating-details', [RatingsController::class,'showAllRatings']);
	
	Route::get('/breeder-list', [BreederController::class,'getBreederList']);
	Route::get('/breeder-detail', [BreederController::class,'breederDetail']);
	
	Route::get('/animalsale-list', [AnimalsaleController::class,'getAnimalSaleList']);
	Route::get('/animalsale-detail', [AnimalsaleController::class,'animalSaleDetail']); 
	
	Route::get('/chemist-list', [ChemistController::class,'getChemistList']);
	Route::get('/chemist-detail', [ChemistController::class,'chemistDetail']);
	
	Route::get('/animal-list', [AddanimalController::class,'getAnimalList']);
	Route::get('/animal-detail', [AddanimalController::class,'animalDetail']);
	
	/* Nearest Pashumitra*/
	Route::get('/nearest-pashumitra', [PashumitraController::class,'nearestPashumitraList']);
	Route::get('/pashumitra-detail', [PashumitraController::class,'pashumitraDetail']);
	
	/* Nearest registeredvet*/
	Route::get('/nearest-registeredvet', [RegisteredvetController::class,'nearestRegisteredVetList']);
	Route::get('/registeredvet-detail', [RegisteredvetController::class,'registeredVetDetail']);
	
	/* Search  */
	Route::get('/search-registeredvet', [SearchController::class,'searchRegisteredVetDetails']);
	Route::get('/search-animalsale', [SearchController::class,'searchAnimalForSalesDetails']);
	Route::get('/search-breeder', [SearchController::class,'searchBreederDetails']);
	Route::get('/search-library', [SearchController::class,'searchLibraryDetails']);
	Route::get('/search-chemist', [SearchController::class,'searchChemistDetails']);

	/* advertisement Details */
	Route::get('/advertisement-list', [AdvertisementController::class,'getAdvertisementList']);
	Route::get('/advertisement-detail', [AdvertisementController::class,'advertisementDetail']);
	
	Route::get('/transporter-list', [TransporterController::class,'getTransporterList']);
	Route::get('/transporter-detail', [TransporterController::class,'transporterDetail']);
	
	Route::get('/productsale-list', [ProductsaleController::class,'getProductsaleList']);
	Route::get('/productsale-detail', [ProductsaleController::class,'productsaleDetail']);
}); 