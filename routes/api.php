<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkShiftController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [UserController::class, 'login']);
Route::get('/logout', [UserController::class, 'logout']);

Route::get('/user', [UserController::class, 'getAll']);
Route::post('/user', [UserController::class, 'create']);

Route::prefix('/work-shift')->group(function() {
    Route::post('/', [WorkShiftController::class, 'create']);
    Route::get('/{id}/close', [WorkShiftController::class, 'close']);
    Route::get('/{id}/open', [WorkShiftController::class, 'open']);
    Route::post('/{code}/user', [WorkShiftController::class, 'addUser']);
    Route::get('/{id}/order', [OrderController::class, 'orders']);
});

Route::prefix('/order')->group(function() {
    Route::post('/', [OrderController::class, 'create']);
    Route::get('/{id}', [OrderController::class, 'lookup']);
    Route::patch('/{id}/change-status', [OrderController::class, 'changeStatusAsWaiter'])->name('change-status.as-waiter');
    Route::patch('/{id}/change-status', [OrderController::class, 'changeStatusAsCook'])->name('change-status.as-cook');
    Route::post('/{id}/position', [OrderController::class, 'addPosition']);
});
