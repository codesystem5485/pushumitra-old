<?php
use App\Http\Controllers\Api\CommonController;

Route::group(['middleware' => ['cors']], function () {
    Route::group(['middleware' => ['api-token']], function () {
        Route::get('/setting', [CommonController::class,'getSetting']); 
    });
}); 