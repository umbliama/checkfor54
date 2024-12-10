<?php

use App\Http\Controllers\Api\EquipmentController;
use App\Http\Controllers\NotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\CityController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/cities', [CityController::class, 'index']);



Route::get('/equipment',[EquipmentController::class,'getEquipment']);
Route::get('/equipment/getCount',[EquipmentController::class,'getEquipmentCount']);
Route::get('/equipment/repair/getCount',[EquipmentController::class,'getEquipmentRepairCount']);
Route::get('/equipment/test/getCount',[EquipmentController::class,'getEquipmentTestCount']);
Route::get('/equipment/categories',[EquipmentController::class,'getEquipmentCategories']);
Route::get('/equipment/categories/count',[EquipmentController::class,'getEquipmentCategoriesCount']);
Route::get('/equipment/sizes',[EquipmentController::class,'getEquipmentSizes']);
Route::get('/equipment/sizes/{categoryId}',[EquipmentController::class,'getEquipmentSizesByCategoryId']);
Route::get('/equipment/sizes/count',[EquipmentController::class,'getEquipmentSizesCount']);
Route::get('/equipment/{id}',[EquipmentController::class,'getEquipmentByID']);
Route::get('/equipment/{categoryId}/{sizeId}', [EquipmentController::class, 'getEquipmentByCategoryAndBySize']);


Route::get('/equip/repair',[EquipmentController::class,'getFilteredRepairs']);
Route::get('/equip/report',[EquipmentController::class,'getFilteredReports']);
Route::get('/equip/tests',[EquipmentController::class,'getFilteredTests']);


Route::get('/getNotificationsByUserId/{id}',[NotificationController::class,'getNotificationsByUserId']);
Route::post('/notifications/read/{id}/{userId}', [NotificationController::class, 'markAsRead']);
Route::post('/notifications/read-all/{id}', [NotificationController::class, 'markAllAsRead']);

Route::middleware('auth:sanctum')->get('/check-auth', function () {
    return response()->json(['user' => auth()->user()]);
});