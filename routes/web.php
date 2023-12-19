<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function() { return redirect()->route('auth.login'); });

Route::get('/login', function() { return Inertia::render('Auth/Login'); })->name('auth.login');
Route::get('/register', function() { return Inertia::render('Auth/Register'); })->name('auth.register');
