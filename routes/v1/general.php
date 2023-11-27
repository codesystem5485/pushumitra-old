<?php
use App\Http\Controllers\Api\CommonController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AnimalsaleController;
use App\Http\Controllers\Api\PaymentController;

Route::group(['middleware' => ['cors']], function () {
	
	Route::get('/get-species', [CommonController::class,'getSpecies']); 
	Route::get('/get-breeds', [CommonController::class,'getBreeds']);
	Route::get('/get-library', [AuthController::class,'getLibrary']);
	
	/* Animal sale*/
	Route::post('/add-animalsale', [AnimalsaleController::class,'addAnimalForSale']); 
	Route::get('/animalsale-list', [AnimalsaleController::class,'getAnimalSaleList']);
	Route::get('/animalsale-detail', [AnimalsaleController::class,'animalSaleDetail']);	
	
	/* payment */
	Route::post('/get-orderid', [PaymentController::class,'generatePaymentOrderId']);
	Route::post('/create-payment', [PaymentController::class,'addPayments']);
	Route::get('/get-config', [PaymentController::class,'getConfig']);
	
	Route::group(['middleware' => ['api-token']], function () {
        Route::get('/setting', [CommonController::class,'getSetting']); 
    });
}); 