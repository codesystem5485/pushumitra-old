<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\UserController; 
use App\Http\Controllers\Backend\LoginController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\Backend\RegisteredvetController;
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
Route::get('/test', [TestController::class, 'test'])->name('test'); 
Route::get('/update-permission', [TestController::class, 'updatePermission']); 
Route::get('/', function () {
    return redirect('/auth/login');
});
Route::get('/clear-cache', function() {
    Artisan::call('optimize:clear');
    echo Artisan::output();
});
Route::get('/get-cities', [CommonController::class, 'get_cities'])->name('get-cities'); 

//Login
Route::group([
  'prefix' => 'auth',
  'as' => 'auth.',
  ],function () { 
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login'); 
    Route::post('/login', [LoginController::class, 'checkCredential']); 
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout'); 
     
});

Route::middleware(['auth'])->group(function () {//,'check.role'
  Route::get('/dashboard', [HomeController::class, 'index'])->name('home'); 
	Route::get('/profile', [HomeController::class, 'profile'])->name('profile');
	Route::post('/update/{id?}/profile', [HomeController::class, 'updateProfile'])->name('update.profile');
	Route::post('/change-password', [HomeController::class, 'changePassword'])->name('change.password');
  Route::post('/mobile-verify', [HomeController::class, 'mobileVerify'])->name('mobile.verify');
  Route::post('/update-mobile', [HomeController::class, 'updateMobile'])->name('update.mobile');
  ##Activity Logs
  Route::get('/logs', [HomeController::class, 'getLogs'])->name('logs');
  Route::get('/ajax-data', [HomeController::class, 'ajaxData'])->name('log.ajax'); 
     
    //Role module
    Route::group([
        'prefix' => 'role',
        'as' => 'role.',
      ], function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::get('/create', [RoleController::class, 'create'])->name('create');
        Route::post('/store', [RoleController::class, 'store'])->name('store'); 
        Route::get('/{id?}/edit', [RoleController::class, 'edit'])->name('edit'); 
        Route::post('/{id?}/update', [RoleController::class, 'update'])->name('update'); 
        Route::get('/{id?}/delete', [RoleController::class, 'delete'])->name('delete'); 
    });
    //User module
    Route::group([
        'prefix' => 'user',
        'as' => 'user.', 
      ], function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/pashumitra', [UserController::class, 'pashumitra'])->name('pashumitra');
        Route::get('/pet-owner', [UserController::class, 'petowner'])->name('petowner');
        Route::get('/registered-vet', [UserController::class, 'registeredvet'])->name('registered-vet');
        Route::get('/user-list', [UserController::class, 'getAjaxUser'])->name('list');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::get('/create/pet-owner', [UserController::class, 'create_petowner'])->name('create-petowner');
        Route::get('/create/pashumitra', [UserController::class, 'create_pashumitra'])->name('create-pashumitra');
        Route::get('/create/registered-vet', [UserController::class, 'create_registeredvet'])->name('create-registered-vet');
        Route::post('/store', [UserController::class, 'store'])->name('store');
        Route::get('/{id?}/edit', [UserController::class, 'edit'])->name('edit'); 
        Route::post('/{id?}/update', [UserController::class, 'update'])->name('update'); 
        Route::get('/{id?}/delete', [UserController::class, 'delete'])->name('delete');  
        Route::get('/{id?}/detail', [UserController::class, 'userDetail'])->name('detail');
        Route::get('/get-role-user', [UserController::class, 'getRoleWiseUser'])->name('role');
    });
    
    //Registered-vet
    Route::group([
        'prefix' => 'user',
        'as' => 'user.', 
      ], function () {
        Route::get('/registered-vet', [RegisteredvetController::class, 'index'])->name('index');
        Route::get('/user-list', [UserController::class, 'getAjaxUser'])->name('list');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/store', [UserController::class, 'store'])->name('store');
        Route::get('/{id?}/edit', [UserController::class, 'edit'])->name('edit'); 
        Route::post('/{id?}/update', [UserController::class, 'update'])->name('update'); 
        Route::get('/{id?}/delete', [UserController::class, 'delete'])->name('delete');  
        Route::get('/{id?}/detail', [UserController::class, 'userDetail'])->name('detail');
        Route::get('/get-role-user', [UserController::class, 'getRoleWiseUser'])->name('role');
    });
});

    