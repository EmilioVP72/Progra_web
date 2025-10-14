<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Views_Admin\AdminVController;

/*Route::get('/', function () {
    return view('welcome');
});*/

Route::prefix('admin/clientes')->group(function () {
    Route::get('/{id}/editar', [AdminVController::class, 'edit'])->name('admin.clients.edit');
    Route::get('/', [AdminVController::class, 'index'])->name('admin.clients.index');
    Route::get('/crear', [AdminVController::class, 'create'])->name('admin.clients.create');
});
