<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ColiController;
use App\Http\Controllers\UpdateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//AuthController
Route::post('/login',[AuthController::class,'login']);
Route::any('/register',[AuthController::class,'register']);
Route::post('/code',[AuthController::class,'code']);
Route::post('/checkcode',[AuthController::class,'checkcode']);
Route::post('/getUser',[AuthController::class,'getUser']);

//ColiController
Route::post('/colis',[ColiController::class,'colis']);
Route::post('/alertEmplois',[ColiController::class,'alertEmplois']);
Route::post('/getColiEtat2et3Livreur',[ColiController::class,'getColiEtat2et3Livreur']);
Route::post('/getColiEtat2et3Client',[ColiController::class,'getColiEtat2et3Client']);
Route::post('/UpdateLivreurEtat',[ColiController::class,'UpdateLivreurEtat']);
Route::post('/rejeterLivreurEtat',[ColiController::class,'rejeterLivreurEtat']);
Route::post('/getAdresseColis',[ColiController::class,'getAdresseColis']);
Route::post('/updatePrix',[ColiController::class,'updatePrix']);
Route::post('/negociePrix',[ColiController::class,'negociePrix']);
Route::post('/EnattenteLivreur',[ColiController::class,'EnattenteLivreur']);
Route::post('/EnattenteClient',[ColiController::class,'EnattenteClient']);
Route::post('/AccepteColiClient',[ColiController::class,'AccepteColiClient']);
Route::post('/RefuseColiClient',[ColiController::class,'RefuseColiClient']);
Route::post('/notifierClient',[ColiController::class,'notifierClient']);
Route::post('/publieClient',[ColiController::class,'publieClient']);
Route::post('/historiqueAffichage',[ColiController::class,'historiqueAffichage']);
Route::post('/getportfeuille',[ColiController::class,'getportfeuille']);
Route::post('/Notficationnn',[ColiController::class,'Notficationnn']);
Route::post('/getColirejeterClient',[ColiController::class,'getColirejeterClient']);
Route::post('/getColirejeterLivreur',[ColiController::class,'getColirejeterLivreur']);
Route::post('/getColiLivrerLivreur',[ColiController::class,'getColiLivrerLivreur']);
Route::post('/getColiLivrerClient',[ColiController::class,'getColiLivrerClient']);
Route::post('/donnerAvis',[ColiController::class,'donnerAvis']);
Route::post('/republierClient',[ColiController::class,'republierClient']);
Route::post('/supprimerColiClient',[ColiController::class,'supprimerColiClient']);
Route::post('/problemClient',[ColiController::class,'problemClient']);
Route::post('/shownotiClient',[ColiController::class,'shownotiClient']);
Route::post('/shownotiLivreur',[ColiController::class,'shownotiLivreur']);


//UpdateController
Route::post('/updateMail',[UpdateController::class,'updateMail']);
Route::post('/updatePhotoProfile',[UpdateController::class,'updatePhotoProfile']);
Route::post('/updatePhotoProfileLiv',[UpdateController::class,'updatePhotoProfileLiv']);
Route::post('/updatePassword',[UpdateController::class,'updatePassword']);
Route::post('/updatePhone',[UpdateController::class,'updatePhone']);
Route::post('/updatePhoneCheck',[UpdateController::class,'updatePhoneCheck']);
Route::post('/checkforgetPassword',[UpdateController::class,'checkforgetPassword']);
Route::post('/newpassorgetPassword',[UpdateController::class,'newpassorgetPassword']);


