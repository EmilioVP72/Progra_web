<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\ClientController;


/*Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');*/

Route::get('/clients', [ClientController::class, 'All']);
Route::get('/clients/{id}', [ClientController::class, 'OneClient']);
Route::post('/clients', [ClientController::class, 'Store']);
Route::put('/clients/{id}', [ClientController::class, 'Update']);
Route::delete('/clients/{id}', [ClientController::class, 'Delete']);

