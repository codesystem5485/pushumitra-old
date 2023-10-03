<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthController;
/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
|
| Here is where you can register user routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "user" middleware group. Now create something great!
|
*/
// Route::middleware(['auth'])->group(function () {
Route::group(['middleware' => ['cors']], function () {
    Route::post('/signup', [AuthController::class,'signUp']); 
    Route::post('/signin', [AuthController::class,'signIn']); 
    Route::post('/verify-otp', [AuthController::class,'verifyOtp']); 
    Route::post('/verify-mobile-number', [AuthController::class,'verifyPhoneNumber']); 
   
    Route::group(['middleware' => ['api-token']], function () {
        Route::get('/logout', [AuthController::class,'logout']); 
        Route::get('/get-profile', [AuthController::class,'getProfile']); 
        Route::put('/update-profile', [AuthController::class,'updateProfile']); 
        Route::post('/upload-profile-pic', [AuthController::class,'updateProfilePic']); 
    });

});  
   