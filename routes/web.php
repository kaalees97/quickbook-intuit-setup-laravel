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


Route::get('/edit-customer/{id}', [App\Http\Controllers\HomeController::class, 'edit_customer_form'])->name('editcustomerform');

Route::get('/update-token-access', [App\Http\Controllers\HomeController::class, 'updated_access_tokens'])->name('tokenaccess');

Route::post('/update-customer',[App\Http\Controllers\HomeController::class, 'update_customer_form'])->name('updates');

Route::get('delete-customer/{id}', [App\Http\Controllers\HomeController::class, 'delete_customer'])->name('deletecust');

Route::get('/view-customer', [App\Http\Controllers\HomeController::class, 'view_customer_form'])->name('view_cust');

Route::post('/save-customer', [App\Http\Controllers\HomeController::class, 'savecustomer'])->name('savecustomer');


// Expenses List

Route::get('/expense-list', [App\Http\Controllers\HomeController::class, 'viewexpenses_list'])->name('expenses');

// Supplier List

Route::get('/supplier-list', [App\Http\Controllers\HomeController::class, 'supplier_details'])->name('details.supplier');

// Report List

Route::get('/report-list', [App\Http\Controllers\HomeController::class, 'report_detail'])->name('reportlisting');