<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/',[AdminController::class,'dashboard']);
Route::get('/colisinfo',[AdminController::class,'getEtatColis']);
Route::get('/livreur',[AdminController::class,'getLivreurs'])->name('livreur.show');
Route::get('/client',[AdminController::class,'getClients'])->name('client.show');
Route::get('/etat=/{id?}',[AdminController::class,'getDetailEtatColi'])->where('id', '[1-6]');
Route::get('/users/{id}',[AdminController::class,'show'])->name('users.show');
Route::get('/check/{id}',[AdminController::class,'check'])->name('check.show');
Route::get('/uncheck/{id}',[AdminController::class,'uncheck'])->name('uncheck.show');


