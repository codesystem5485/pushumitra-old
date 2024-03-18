<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CommonController;
use App\Http\Controllers\Api\AnimalsaleController;


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

	Route::post('/generate-otp', [AuthController::class,'addOtpMobileVerification']); 
	Route::post('/verify-mobile-otp', [AuthController::class,'verifyMobileNumberWithOtp']);
	
    Route::post('/signup', [AuthController::class,'signUp']); 
    // Route::post('/signin', [AuthController::class,'signIn']); 
    Route::post('/signin', [AuthController::class,'login']);  
	Route::post('/guest-signin', [AuthController::class,'guestLogin']);
	Route::post('/guest-verify-otp', [AuthController::class,'guestVerifyOtp']);

    Route::post('/verify-mobile-number', [AuthController::class,'verifyPhoneNumber']); 
	Route::post('/verify-otp', [AuthController::class,'verifyOtp']); 
    Route::get('/get-states', [CommonController::class,'getStates']); 
    Route::get('/get-cities/{id?}', [CommonController::class,'getCities']);
	Route::post('/forgot-password', [AuthController::class,'forgotPassword']);
	Route::post('/resend-otp', [AuthController::class,'resendOtp']); 
	
	//Route::get('/get-profile', [AuthController::class,'getProfile']);
	//Route::post('/update-profile', [AuthController::class,'updateProfile']);
	
	
	
	Route::get('/getDownload', [AuthController::class, 'getDownload']);
	
   
    Route::group(['middleware' => ['api-token']], function () {
		
		Route::get('/get-profile', [AuthController::class,'getProfile']);
		Route::post('/update-generalprofile', [AuthController::class,'updateGeneralProfile']);
		Route::post('/update-bankprofile', [AuthController::class,'updateBankProfile']);
		Route::post('/update-otherprofile', [AuthController::class,'updateOtherProfile']);
		
		Route::post('/change-password', [AuthController::class,'changeProfilePassword']);
        Route::get('/logout', [AuthController::class,'logout']);
        
        Route::post('/upload-profile-pic', [AuthController::class,'updateProfilePic']); 
		Route::get('/delete-account', [AuthController::class,'deleteUserAccount']);
    });

});  
   