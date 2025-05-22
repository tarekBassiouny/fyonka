<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StoreController;
use App\Http\Controllers\Api\TransactionTypeController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\DropdownController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [LoginController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/dropdowns', [DropdownController::class, 'index']);
    Route::get('/store', [StoreController::class, 'index']);
    Route::get('/type', [TransactionTypeController::class, 'index']);
    Route::get('/type/{id}/subtype', [TransactionTypeController::class, 'subtype']);
    Route::post('/transaction', [TransactionController::class, 'store']);
});
