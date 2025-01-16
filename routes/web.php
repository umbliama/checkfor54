<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ContragentsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DirectoryController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckIfApproved;

use Inertia\Inertia;

Route::get('/welcome', function () {
    return Inertia::render('Welcome');
});

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/login');
    } else {
        return redirect('/login');
    }
});

Route::middleware(['auth'])->group(function () {
    Route::get('/notallowed', function () {
        return Inertia::render('NotAllowed/Index');
    })->name('notallowed.index');
});

Route::middleware(['auth', CheckIfApproved::class])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');

    Route::get('/equip', [EquipmentController::class, 'index'])->name('equip.index');
    Route::get('/equip/report', [EquipmentController::class, 'report'])->name('equip.report');


    Route::get('/equip/tests', [EquipmentController::class, 'tests'])->name('equip.tests');
    Route::post('/equip/tests', [EquipmentController::class, 'storeTest'])->name('equip.storeTest');
    Route::post('/equip/repair', [EquipmentController::class, 'storeRepair'])->name('equip.storeRepair');
    Route::delete('/equip/repair/delete/{id}', [EquipmentController::class, 'destroyRepair'])->name('equip.destroyRepair');


    Route::get('/equip/price', [EquipmentController::class, 'price'])->name('equip.price');
    Route::post('/equip/price', [EquipmentController::class, 'storePrice'])->name('equip.storePrice');



    Route::get('/equip/repair', [EquipmentController::class, 'repair'])->name('equip.repair');
    Route::get('/equip/repair/create', [EquipmentController::class, 'createRepair'])->name('equip.createRepair');
    Route::post('/equip/repair', [EquipmentController::class, 'storeRepair'])->name(name: 'equip.storeRepair');



    Route::get('/equip/create', [EquipmentController::class, 'create'])->middleware(['auth', 'verified'])->name('equip.create');
    Route::get('/equip/edit/{id}', [EquipmentController::class, 'edit'])->middleware(['auth', 'verified'])->name('equip.edit');
    Route::put('/equip/update/{id}', [EquipmentController::class, 'update'])->middleware(['auth', 'verified'])->name('equip.update');
    Route::delete('/equip/delete/{id}', [EquipmentController::class, 'destroy'])->middleware(['auth', 'verified'])->name('equip.destroy');


    Route::put('/equip/repair/update/{id}', [EquipmentController::class, 'updateRepair'])->middleware(['auth', 'verified'])->name('equip.updateRepair');
    Route::put('/equip/tests/update/{id}', [EquipmentController::class, 'updateTest'])->middleware(['auth', 'verified'])->name('equip.updateTest');
    Route::put('/equip/price/update/{id}', [EquipmentController::class, 'updatePrice'])->middleware(['auth', 'verified'])->name('equip.updatePrice');

    Route::delete('/equip/repair/delete/{id}', [EquipmentController::class, 'destroyRepair'])->middleware(['auth', 'verified'])->name('repair.destroy');
    Route::delete('/equip/tests/delete/{id}', [EquipmentController::class, 'destroyTest'])->middleware(['auth', 'verified'])->name('tests.destroy');
    Route::delete('/equip/price/delete/{id}', [EquipmentController::class, 'destroyPrice'])->middleware(['auth', 'verified'])->name('price.destroy');

    Route::post('/equip', [EquipmentController::class, 'store'])->name('equip.store');
    Route::post('/equip/location', [EquipmentController::class, 'storeLocation'])->name('equip.storeLocation');
    Route::delete('/equip/location/delete/{id}', action: [EquipmentController::class, 'deleteLocation'])->name('equip.deleteLocation');
    Route::post('/equipment/{id}/hyperlink', [EquipmentController::class, 'storeHyperLink']);
    Route::post('/equipment/repair/{id}/hyperlink', action: [EquipmentController::class, 'storeRepairHyperLink']);
    Route::post('/equipment/tests/{id}/hyperlink', action: [EquipmentController::class, 'storeTestHyperLink']);
    Route::post('/equipment/service/{id}/hyperlink', action: [ServiceController::class, 'storeHyperLink']);
    Route::post('/equipment/price/{id}/hyperlink', action: [EquipmentController::class, 'storeHyperlinkPrice']);


    Route::get('/contragents', [ContragentsController::class, 'index'])->name('contragents.index');
    Route::get('/contragents/create', [ContragentsController::class, 'create'])->name('contragents.create');
    Route::get('/contragents/edit/{id}', [ContragentsController::class, 'edit'])->name('contragents.edit');
    Route::get('/contragents/show/{id}', [ContragentsController::class, 'show'])->name('contragents.show');
    Route::patch('/contragents/update/{id}', [ContragentsController::class, 'update'])->name('contragents.update');
    Route::post('/contragents', [ContragentsController::class, 'store'])->name('contragents.store');
    Route::delete('/contragents/delete/{id}', [ContragentsController::class, 'destroy'])->name('contragents.destroy');
    Route::get('/contragents/{id}', [ContragentsController::class, 'show'])->name('contragents.show');

    Route::prefix('/constructor')->group(function () {
        Route::get('/', [IncidentController::class, 'index'])->name('constructor.index');

        Route::post('/column', [IncidentController::class, 'createColumn'])->name('constructor.columnCreate');
        Route::delete('/column/{column}', [IncidentController::class, 'deleteColumn'])->name('constructor.deleteColumn');
        Route::post('/columns/reorder', [IncidentController::class, 'reorderColumns'])->name('constructor.reorderColumns');
        Route::put('/columns/{column}', [IncidentController::class, 'archiveColumn'])->name('constructor.archiveColumn');

        Route::post('/column/{column}/block', [IncidentController::class, 'createBlock'])->name('constructor.createBlock');
        Route::delete('/block/{block}', [IncidentController::class, 'deleteBlock'])->name('constructor.deleteBlock');
        Route::post('/column/{column}/blocks/reorder', [IncidentController::class, 'reorderBlocks'])->name('constructor.reorderBlocks');

        Route::post('/block/{block}/save', [IncidentController::class, 'saveBlockInfo'])->name('constructor.saveBlock');
    });

    Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
    Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
    Route::get('/services/create', [ServiceController::class, 'create'])->name('services.create');
    Route::get('/services/edit/{id}', [ServiceController::class, 'edit'])->name('services.edit');
    Route::put('/services/{service}', [ServiceController::class, 'update'])->name('services.update');
    Route::delete('/services/delete/{id}', [ServiceController::class, 'destroy'])->name('services.destroy');
    Route::post('/services/createIncident/{id}', [ServiceController::class, 'createIncident'])->name('services.createIncident');
    Route::get('/incident', [IncidentController::class, 'index'])->name('incident.index');
    Route::get('/incident/history', [IncidentController::class, 'history'])->name('incident.history');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notification.index');


    Route::get('/sale', [SaleController::class, 'index'])->name('sale.index');
    Route::get('/sale/create', [SaleController::class, 'create'])->name('sale.create');
    Route::post('/sales', [SaleController::class, 'store'])->name('sale.store');
    Route::delete('/sales/delete/{id}', [SaleController::class, 'destroy'])->name('sale.destroy');
    Route::get('/sale/edit/{id}', [SaleController::class, 'edit'])->name('sale.edit');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    Route::get('/directory/{type}/{id}', [DirectoryController::class, 'index'])->name('directory.index');
    Route::post('/directory/{type}/{id}', [DirectoryController::class, 'store'])->name('directory.store');

    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::post('/approve/{id}', [AdminController::class, 'approveUser'])->name('admin.approve');


    Route::get('/search', [SearchController::class, 'search']);
    Route::get('/search-results', [SearchController::class, 'index'])->name('search.index');

    Route::post('/changeLocation', [EquipmentController::class,'changeLocation'])->name('location.change');
});



require __DIR__ . '/auth.php';
