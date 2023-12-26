<?php

use Illuminate\Support\Facades\Route;

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

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/add-customer', [App\Http\Controllers\HomeController::class, 'add_customer_form'])->name('addcustomerform');
Route::post('/save-customer', [App\Http\Controllers\HomeController::class, 'savecustomer'])->name('savecustomer');
