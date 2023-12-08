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

Route::group(['middleware' => ['cors']], function () {
	 
	Route::get('/get-species', [CommonController::class,'getSpecies']); 
	Route::get('/get-breeds', [CommonController::class,'getBreeds']);
	Route::get('/get-library', [AuthController::class,'getLibrary']);
	
	 Route::group(['middleware' => ['api-token']], function () {
	
	/* Animal sale*/
	Route::post('/add-animalsale', [AnimalsaleController::class,'addAnimalForSale']); 

	/* Breeder */
	Route::post('/add-breeder', [BreederController::class,'addBreeder']);
	
	/* Rxreminder */
	Route::post('/add-rxreminder', [RxreminderController::class,'addRxreminder']);
	Route::get('/get-animalowner-list', [RxreminderController::class,'getAnimalOwnerList']);
	Route::get('/get-animal-list', [RxreminderController::class,'getAnimalNameList']);
	Route::get('/get-animal-history-list', [RxreminderController::class,'getAnimalsHistory']);
	Route::get('/get-animalwise-history', [RxreminderController::class,'getAnimalWiseHistory']);
	
	/* payment */
	Route::post('/get-orderid', [PaymentController::class,'generatePaymentOrderId']);
	Route::post('/create-payment', [PaymentController::class,'addPayments']);
	Route::get('/get-config', [PaymentController::class,'getConfig']);
	
	/* Add animal*/
	Route::post('/add-animal', [AddanimalController::class,'addAnimal']); 
	Route::get('/setting', [CommonController::class,'getSetting']); 
    
	});
	
	Route::get('/breeder-list', [BreederController::class,'getBreederList']);
	Route::get('/breeder-detail', [BreederController::class,'breederDetail']);
	
	Route::get('/animalsale-list', [AnimalsaleController::class,'getAnimalSaleList']);
	Route::get('/animalsale-detail', [AnimalsaleController::class,'animalSaleDetail']);
	
	Route::get('/animal-list', [AddanimalController::class,'getAnimalList']);
	Route::get('/animal-detail', [AddanimalController::class,'animalDetail']);
	
	/* Nearest Pashumitra*/
	Route::get('/nearest-pashumitra', [PashumitraController::class,'nearestPashumitraList']);
	Route::get('/pashumitra-detail', [PashumitraController::class,'pashumitraDetail']);
	
	/* Nearest registeredvet*/
	Route::get('/nearest-registeredvet', [RegisteredvetController::class,'nearestRegisteredVetList']);
	Route::get('/registeredvet-detail', [RegisteredvetController::class,'registeredVetDetail']);
}); 