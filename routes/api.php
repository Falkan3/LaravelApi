<?php

use App\Http\Controllers\CompanyController;
use Illuminate\Support\Facades\Route;

Route::prefix('companies')->group(function () {
    Route::get('/', [CompanyController::class, 'index']);
    Route::get('{id}', [CompanyController::class, 'view']);
    Route::post('create', [CompanyController::class, 'create']);
    Route::put('{id}/update', [CompanyController::class, 'update']);
    Route::delete('{id}/destroy', [CompanyController::class, 'destroy']);
});
