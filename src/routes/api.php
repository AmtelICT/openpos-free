<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CountriesController;
use App\Http\Controllers\StatesController;
use App\Http\Controllers\CitiesController;
use App\Http\Controllers\ConfigurationsController;
use App\Http\Controllers\EmployeesController;

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
// Public State routes
Route::get('/states/{id}', [StatesController::class, 'index']);
// Public City routes
Route::get('/cities/{id}', [CitiesController::class, 'index']);

// Protected Employees routes
Route::group(['middleware' => ['auth:sanctum']], function () {
  Route::get('/employees', [EmployeesController::class, 'index']);
	Route::get('/employees/{id}', [EmployeesController::class, 'query']);
	Route::post('/employees', [EmployeesController::class, 'store']);
	Route::put('/employees/{id}', [EmployeesController::class, 'update']);
	Route::delete('/employees/{id}', [EmployeesController::class, 'delete']);
});


