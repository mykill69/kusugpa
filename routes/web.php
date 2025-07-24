<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginAuthController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\UpdatesController;
use App\Http\Controllers\UploadController;
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

Route::group(['middleware'=>['guest']],function(){
    Route::get('/', function () {
        return view('login.login');
    });

    //login
    Route::get('/login', [LoginAuthController::class, 'getLogin'])->name('getLogin');
    Route::post('/login', [LoginAuthController::class, 'postLogin'])->name('postLogin');

});

    Route::group(['middleware'=>['login_auth']],function(){
       //logout
    Route::get('/logout', [LoginAuthController::class,'logout'])->name('logout');

    //dashboard
    Route::get('/dashboard', [MenuController::class,'dashboard'])->name('dashboard'); 


    Route::post('/updates/add-crop-year', [UpdatesController::class, 'addCropYear'])->name('updates.addCropYear');
    Route::post('/updates/add-week-number', [UpdatesController::class, 'addWeekNumber'])->name('updates.addWeekNumber');
    Route::post('/updates/add-quedan-price', [UpdatesController::class, 'addQuedanPrice'])->name('updates.addQuedanPrice');
    Route::post('/updates/add-molasses-price', [UpdatesController::class, 'addMolassesPrice'])->name('updates.addMolassesPrice');

    //upload summary
    Route::post('/upload-summary', [UploadController::class, 'uploadCSV'])->name('summary.upload');


 });