<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\TaskAttachmentController;
use App\Http\Controllers\Api\TaskTagController;

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
        Route::put('/{id}/due-date', [TaskController::class, 'updateDueDate'])->name('due-date');
        Route::put('/{id}/archive', [TaskController::class, 'archive'])->name('archive');
        Route::post('/{id}/tag', [TaskTagController::class, 'store'])->name('tag.add');
        Route::put('/{id}/tag', [TaskTagController::class, 'update'])->name('tag.update');
        Route::post('/{id}/attach', [TaskAttachmentController::class, 'store'])->name('attach');
    });

    Route::post('/archive-task/delete', [TaskController::class, 'deleteOldArchivedTasks'])->name('api.delete-archive');
});
