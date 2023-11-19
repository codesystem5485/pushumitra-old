<?php
use App\Http\Controllers\Api\CommonController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\Api\AnimalsaleController;

Route::group(['middleware' => ['cors']], function () {
	
	Route::get('/get-species', [CommonController::class,'getSpecies']); 
	Route::get('/get-breeds', [CommonController::class,'getBreeds']);
	
	
		/* Animal sale*/
	Route::post('/add-animalsale', [AnimalsaleController::class,'addAnimalForSale']); 
	Route::get('/animalsale-list', [AnimalsaleController::class,'getAnimalSaleList']); 
	

    Route::group(['middleware' => ['api-token']], function () {
        Route::get('/setting', [CommonController::class,'getSetting']); 
    });
}); 