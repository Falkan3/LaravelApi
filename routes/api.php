<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;

Route::prefix('companies')->group(function () {
    Route::get('/', [CompanyController::class, 'index']);
    Route::get('{id}', [CompanyController::class, 'show']);
    Route::post('create', [CompanyController::class, 'create']);
    Route::match(['put', 'patch'], '{id}/update', [CompanyController::class, 'update']);
    Route::delete('{id}/destroy', [CompanyController::class, 'destroy']);
    Route::post('link-employee', [CompanyController::class, 'linkEmployee']);
    Route::post('unlink-employee', [CompanyController::class, 'unlinkEmployee']);
});

Route::prefix('employees')->group(function () {
    Route::get('/', [EmployeeController::class, 'index']);
    Route::get('{id}', [EmployeeController::class, 'show']);
    Route::post('create', [EmployeeController::class, 'create']);
    Route::match(['put', 'patch'], '{id}/update', [EmployeeController::class, 'update']);
    Route::delete('{id}/destroy', [EmployeeController::class, 'destroy']);
});
