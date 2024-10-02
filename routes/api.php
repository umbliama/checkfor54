<?php

use App\Http\Controllers\Api\EquipmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CityController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/cities', [CityController::class, 'index']);



Route::get('/equipment',[EquipmentController::class,'getEquipment']);
Route::get('/equipment/{categoryId}',[EquipmentController::class,'getEquipmentById']);


Route::get('/equip/repair',[EquipmentController::class,'getFilteredRepairs']);
Route::get('/equip/report',[EquipmentController::class,'getFilteredReports']);
Route::get('/equip/tests',[EquipmentController::class,'getFilteredTests']);

