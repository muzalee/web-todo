<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;

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

Route::prefix('auth')->name('api.auth.')->group(function() {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
});

Route::middleware('auth:api')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('api.auth.logout');

    Route::group(['prefix' => 'task', 'as' => 'api.task.'], function () {
        Route::get('/{id}', [TaskController::class, 'show'])->name('detail');
        Route::get('/', [TaskController::class, 'index'])->name('list');
        Route::post('/', [TaskController::class, 'store'])->name('create');
        Route::put('/{id}', [TaskController::class, 'update'])->name('update');
        Route::delete('/{id}', [TaskController::class, 'delete'])->name('delete');
        Route::put('/{id}/complete', [TaskController::class, 'complete'])->name('complete');
    });
});
