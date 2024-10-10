<?php

use App\Http\Controllers\ContragentsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/login');
    } else {
        return redirect('/login');
    }
});

Route::get('/dashboard', [DashboardController::class,'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/equip', [EquipmentController::class,'index'])->name('equip.index');
Route::get('/equip/report', [EquipmentController::class,'report'])->name('equip.report');


Route::get('/equip/tests', [EquipmentController::class,'tests'])->name('equip.tests');
Route::post('/equip/tests', [EquipmentController::class,'storeTest'])->name('equip.storeTest');
Route::post('/equip/repair', [EquipmentController::class,'storeRepair'])->name( 'equip.storeRepair');


Route::get('/equip/price', [EquipmentController::class,'price'])->name('equip.price');
Route::post('/equip/price', [EquipmentController::class,'storePrice'])->name('equip.storePrice');



Route::get('/equip/repair', [EquipmentController::class,'repair'])->name('equip.repair');
Route::get('/equip/repair/create', [EquipmentController::class,'createRepair'])->name('equip.createRepair');
Route::post('/equip/repair', [EquipmentController::class,'storeRepair'])->name(name: 'equip.storeRepair');



Route::get('/equip/create', [EquipmentController::class,'create'])->middleware(['auth', 'verified'])->name('equip.create');
Route::get('/equip/edit/{id}', [EquipmentController::class,'edit'])->middleware(['auth', 'verified'])->name('equip.edit');
Route::delete('/equip/delete/{id}', [EquipmentController::class,'destroy'])->middleware(['auth', 'verified'])->name('equip.destroy');

Route::post('/equip', [EquipmentController::class,'store'])->name( 'equip.store');
Route::post('/equip/location', [EquipmentController::class,'store'])->name( 'equip.storeLocation');
Route::delete('/equip/location/delete/{id}', action: [EquipmentController::class,'deleteLocation'])->name( 'equip.deleteLocation');

Route::get('/contragents', [ContragentsController::class,'index'])->name('contragents.index');
Route::get('/contragents/create', [ContragentsController::class,'create'])->name('contragents.create');
Route::get('/contragents/edit/{id}', [ContragentsController::class,'edit'])->name('contragents.edit');
Route::get('/contragents/show/{id}', [ContragentsController::class,'show'])->name('contragents.show');
Route::patch('/contragents/update/{id}', [ContragentsController::class,'update'])->name('contragents.update');
Route::post('/contragents', [ContragentsController::class,'store'])->name('contragents.store');
Route::delete( '/contragents/delete/{id}', [ContragentsController::class,'destroy'])->name('contragents.destroy');
Route::get('/contragents/{id}', [ContragentsController::class,'show'])->name('contragents.show');

Route::get('/constructor', [IncidentController::class, 'index']);
Route::post('/constructor/column', [IncidentController::class, 'createColumn'])->name('constructor.columnCreate');
Route::delete('/constructor/column/{column}', [IncidentController::class, 'deleteColumn']);
Route::post('/constructor/column/{column}/block', [IncidentController::class, 'createBlock']);
Route::delete('/constructor/block/{block}', [IncidentController::class, 'deleteBlock']);
Route::post('/constructor/columns/reorder', [IncidentController::class, 'reorderColumns']);
Route::post('/constructor/column/{column}/blocks/reorder', [IncidentController::class, 'reorderBlocks']);


Route::get('/services', [ServiceController::class,'index'])->name('services.index');
Route::post('/services', [ServiceController::class,'store'])->name('services.store');
Route::get('/services/create', [ServiceController::class,'create'])->name('services.create');
Route::get('/services/edit/{id}', [ServiceController::class,'edit'])->name('services.edit');
Route::put('/services/{service}', [ServiceController::class, 'update'])->name('services.update');




Route::get('/sale', [SaleController::class,'index'])->name('sale.index');
Route::get('/sale/create', [SaleController::class,'create'])->name('sale.create');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
