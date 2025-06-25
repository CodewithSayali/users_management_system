<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Usercontroller;
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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::post('/store-users', [UserController::class, 'store'])->name('users.store');
Route::get('/get-states/{country_id}', [UserController::class, 'getStates']);
Route::get('/get-cities/{state_id}', [UserController::class, 'getCities']);
