<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CountriesController;
use App\Http\Controllers\StatesController;
use App\Http\Controllers\CitiesController;
use App\Http\Controllers\ConfigurationsController;
use App\Http\Controllers\EmployeesController;
use App\Http\Controllers\ProvidersController;
use App\Http\Controllers\ArticlesController;
use App\Http\Controllers\StocksController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\InvoicesController;

// middleware for VUE
route::get('/checkinstall', [ConfigurationsController::class, 'checkinstall']);

// Instalation routes
Route::post('/install', [ConfigurationsController::class, 'install'])->middleware('checkinstall');
Route::post('/register', [AuthController::class, 'register'])->middleware('checkinstall');

// Protected user data
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
  return $request->user();
});

// Public Auth routes
Route::post('/login', [AuthController::class, 'login']);

// Protected Auth routes
Route::group(['middleware' => ['auth:sanctum']], function () {
  Route::post('/logout', [AuthController::class, 'logout']);
});

// Public Country routes
Route::get('/countries', [CountriesController::class, 'index']);

// Public States routes
Route::get('/states/{id}', [StatesController::class, 'index']);
// Public State route
Route::get('/state/{id}', [StatesController::class, 'state']);

// Public Cities routes
Route::get('/cities/{id}', [CitiesController::class, 'index']);
// Public City route
Route::get('/city/{id}', [CitiesController::class, 'city']);

// Protected Configuraion routes
Route::group(['middleware' => ['auth:sanctum']], function () {
  Route::get('configuration', [ConfigurationsController::class, 'index']);
  Route::post('configuration', [ConfigurationsController::class, 'update']);
});

// Protected Employees routes
Route::group(['middleware' => ['auth:sanctum']], function () {
  Route::get('/employees', [EmployeesController::class, 'index']);
	Route::get('/employee/{id}', [EmployeesController::class, 'query']);
	Route::post('/employee', [EmployeesController::class, 'store']);
	Route::put('/employee/{id}', [EmployeesController::class, 'update']);
	Route::delete('/employee/{id}', [EmployeesController::class, 'delete']);
});

// Protected Providers routes
Route::group(['middleware' => ['auth:sanctum']], function () {
  Route::get('/providers', [ProvidersController::class, 'index']);
  Route::get('/provider/{id}', [ProvidersController::class, 'query']);
  Route::post('/provider', [ProvidersController::class, 'store']);
  Route::put('/provider/{id}', [ProvidersController::class, 'update']);
  Route::delete('/provider/{id}', [ProvidersController::class, 'delete']);
});

// Protected article routes
Route::get('/article/{barcode}', [ArticlesController::class, 'query'])->middleware('auth:sanctum');

// Protected stock routes
Route::group(['middleware' => ['auth:sanctum']], function () {
  Route::get('/stocks', [StocksController::class, 'index']);
  Route::get('/stock/{id}', [StocksController::class, 'query']);
  Route::post('/stock', [StocksController::class, 'store']);
  Route::put('/stock/{id}', [StocksController::class, 'update']);
  Route::put('/stock/buy/{id}', [StocksController::class, 'buy']);
  Route::delete('/stock/{id}', [StocksController::class, 'delete']);
}); 

// Protected  customers routes
Route::group(['middleware' => ['auth:sanctum']], function () {
  Route::get('/customers', [CustomersController::class, 'index']);
  Route::get('/customer/{id}', [CustomersController::class, 'query']);
  Route::post('/customer', [CustomersController::class, 'store']);
  Route::put('/customer/{id}', [CustomersController::class, 'update']);
  Route::delete('/customer/{id}', [CustomersController::class, 'delete']);
});

// Protected order routes
Route::group(['middleware' => ['auth:sanctum']], function () {
  Route::get('/orders/{order_number}', [OrdersController::class, 'query']);
  Route::get('/order/getcode', [OrdersController::class, 'get_code']);
  Route::get('/order/setcode', [OrdersController::class, 'set_code']);
  Route::put('/order/{barcode}', [OrdersController::class, 'generate_order']);


  Route::put('/order/quantity/{article_id}', [OrdersController::class, 'quantity']);
  Route::get('/order/delete/{order_number}', [OrdersController::class, 'delete']);
  Route::delete('/order/cancel/{order_number}', [OrdersController::class, 'cancel']);
  Route::get('/order/payment/{order_number}', [OrdersController::class, 'payment']);
  Route::get('/order/discount/{order_number}', [OrdersController::class, 'discount']);
  Route::get('/order/customer/{order_number}', [OrdersController::class, 'customer']);
});

// Invoices protected routes
Route::group(['middleware' => ['auth:sanctum']], function () {
  Route::get('/invoice/{order_number}', [InvoicesController::class, 'query']);
});