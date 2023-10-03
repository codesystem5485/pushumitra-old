<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'prefix' => 'v1',
    'as' => 'api.',
    'middleware' => ['auth:api']
], function () {
    //lists all users
    Route::post('/all-user', 'API\ V1\ApiController@allUsers')->name('all-user');
});

Route::get('/test', [AuthController::class,'test']);
Route::get('/encrypt/{id?}',function($id){
    echo base64_encode($id);
});  

Route::get('/decrypt/{id?}',function($id){
    echo base64_decode($id);
});  
