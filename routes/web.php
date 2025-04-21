<?php

use App\Events\NotificationCountUpdated;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ChatController;
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
        return redirect('/dashboard/analysis');
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
    Route::get('/dashboard/rent', [DashboardController::class, 'rent'])->middleware(['auth', 'verified'])->name('rent');
    Route::get('/dashboard/free', [DashboardController::class, 'free'])->middleware(['auth', 'verified'])->name('free');
    Route::get('/dashboard/dashequip', [DashboardController::class, 'dashequip'])->middleware(['auth', 'verified'])->name('dashequip');
    Route::get('/dashboard/serviced', [DashboardController::class, 'serviced'])->middleware(['auth', 'verified'])->name('serviced');
    Route::get('/dashboard/analysis', [DashboardController::class, 'analysis'])->middleware(['auth', 'verified'])->name('analysis');
    Route::get('/dashboard/commercial', [DashboardController::class, 'commercial'])->middleware(['auth', 'verified'])->name('commercial');
    Route::put('/dashboard/commercial/editKP', [ContragentsController::class, 'editKP'])->middleware(['auth', 'verified'])->name('editKP');


    Route::get('/equip', [EquipmentController::class, 'index'])->name('equip.index');
    Route::get('/equip/archive', [EquipmentController::class, 'archive'])->name('equip.archive');
    Route::get('/equip/report', [EquipmentController::class, 'report'])->name('equip.report');

    Route::get('/equip/move', [EquipmentController::class, 'move'])->name('equip.move');
    Route::post('/equip/move', [EquipmentController::class, 'storeMove'])->name('equip.storeMove');
    Route::put('/equip/move/update/{id}', [EquipmentController::class, 'updateMove'])->name('equip.updateMove');
    Route::delete('/equip/move/delete/{id}', [EquipmentController::class, 'destroyMove'])->name('equip.destroyMove');
    Route::post('/equip/move/close', [EquipmentController::class, 'closeMove'])->name('equip.closeMove');




    Route::get('/equip/tests', [EquipmentController::class, 'tests'])->name('equip.tests');
    Route::post('/equip/tests', [EquipmentController::class, 'storeTest'])->name('equip.storeTest');
    Route::post('/equip/tests/close/{id}', [EquipmentController::class, 'closeTest'])->name('equip.closeTest');
    Route::post('/equip/repair', [EquipmentController::class, 'storeRepair'])->name('equip.storeRepair');
    Route::delete('/equip/repair/delete/{id}', [EquipmentController::class, 'destroyRepair'])->name('equip.destroyRepair');
    Route::post('/equip/repair/close/{id}', [EquipmentController::class, 'closeRepair'])->name('equip.closeRepair');


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
    Route::put('/equip/location', [EquipmentController::class, 'editLocation'])->name('equip.editLocation');
    Route::delete('/equip/location/delete/{id}', action: [EquipmentController::class, 'deleteLocation'])->name('equip.deleteLocation');
    Route::post('/equipment/{id}/hyperlink', [EquipmentController::class, 'storeHyperLink']);
    Route::post('/equipment/repair/{id}/hyperlink', [EquipmentController::class, 'storeRepairHyperLink']);
    Route::post('/equipment/tests/{id}/hyperlink', [EquipmentController::class, 'storeTestHyperLink']);
    Route::post('/equipment/service/{id}/hyperlink', [ServiceController::class, 'storeHyperLink']);
    Route::post('/equipment/price/{id}/hyperlink', [EquipmentController::class, 'storeHyperlinkPrice']);
    Route::post('/equipment/sale/{id}/hyperlink', [SaleController::class, 'storeHyperlink']);
    Route::post('/equipment/contragent/{id}/hyperlink', [ContragentsController::class, 'storeHyperlink']);
    Route::post('/equipment/equip/off/{id}', [EquipmentController::class,'fakeDestroy'])->name('equip.off');
    Route::delete('/equipment/contragent/{id}/hyperlink', [ContragentsController::class, 'deleteHyperLink']);

    Route::get('/contragents', [ContragentsController::class, 'index'])->name('contragents.index');
    Route::get('/contragents/create', [ContragentsController::class, 'create'])->name('contragents.create');
    Route::get('/contragents/edit/{id}', [ContragentsController::class, 'edit'])->name('contragents.edit');
    Route::get('/contragents/show/{id}', [ContragentsController::class, 'show'])->name('contragents.show');
    Route::post('/contragents/update/{id}', [ContragentsController::class, 'update'])->name('contragents.update');
    Route::post('/contragents', [ContragentsController::class, 'store'])->name('contragents.store');
    Route::delete('/contragents/delete/{id}', [ContragentsController::class, 'destroy'])->name('contragents.destroy');
    Route::get('/contragents/{id}', [ContragentsController::class, 'show'])->name('contragents.show');
    Route::delete('/contragents/file/delete', [ContragentsController::class, 'deleteDocumentFileByContragent'])->name('contragents.deleteFile');
    Route::delete('/contragents/deleteAvatar/{id}', [ContragentsController::class, 'destroyAvatar'])->name('contragents.destroyAvatar');

    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');


    Route::prefix('/constructor')->group(function () {
        Route::get('/', [IncidentController::class, 'index'])->name('constructor.index');

        Route::post('/column', [IncidentController::class, 'createColumn'])->name('constructor.columnCreate');
        Route::delete('/column/{column}', [IncidentController::class, 'deleteColumn'])->name('constructor.deleteColumn');
        Route::post('/columns/reorder', [IncidentController::class, 'reorderColumns'])->name('constructor.reorderColumns');
        Route::put('/columns/{column}', [IncidentController::class, 'archiveColumn'])->name('constructor.archiveColumn');
        Route::delete('/columns/{column}/block/{blockId}/image/{imageIndex}', [IncidentController::class, 'deleteImageFromBlock'])->name('constructor.deleteImage');
        Route::delete('/columns/{columnId}/blocks/{blockId}/delete-file/{fileIndex}', [IncidentController::class, 'deleteFile'])->name('constructor.deleteFile');

        Route::post('/column/{column}/block/{type}', [IncidentController::class, 'createBlock'])->name('constructor.createBlock');
        Route::delete('/block/{block}', [IncidentController::class, 'deleteBlock'])->name('constructor.deleteBlock');
        Route::post('/column/{column}/blocks/reorder', [IncidentController::class, 'reorderBlocks'])->name('constructor.reorderBlocks');

        Route::post('/block/{block}/save', [IncidentController::class, 'saveBlockInfo'])->name('constructor.saveBlock');
        Route::get('/search', [IncidentController::class, 'index'])->name('incidents.index');
    });

    Route::prefix('/chat')->group(function() {
        
        Route::get('/', [ChatController::class,'index'])->name('chat.index');
        Route::get('/getContacts', [ChatController::class,'getContacts'])->name('chat.getContacts');
        Route::get('/getGroups', [ChatController::class,'getGroups'])->name('chat.getGroups');
        Route::get('/getGroupInfo', [ChatController::class,'getGroupInfo'])->name('chat.getGroupInfo');
        Route::get('/searchContactByName', [ChatController::class,'searchContactByName'])->name('chat.searchContactByName');
        Route::get('/getUserInfo', [ChatController::class,'getUserInfo'])->name('chat.getUserInfo');
        Route::get('/getAllUsers', [ChatController::class,'getAllUsers'])->name('chat.getAllUsers');
        Route::post('/createGroup', [ChatController::class,'createGroup'])->name('chat.createGroup');
        Route::post('/sendMessage', [ChatController::class,'sendMessage'])->name('chat.send');
        Route::get('/user/{id}', [ChatController::class, 'getChat'])->name('chat.getChat');
        Route::get('/user/messages/{id}', [ChatController::class, 'getUserMessages'])->name('chat.getUserMessages');
        Route::get('/group/messages/{id}', [ChatController::class, 'getGroupMessages'])->name('chat.getGroupMessages');
    });

    Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
    Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
    Route::get('/services/create', [ServiceController::class, 'create'])->name('services.create');
    Route::get('/services/edit/{id}', [ServiceController::class, 'edit'])->name('services.edit');
    Route::put('/services/{service}', [ServiceController::class, 'update'])->name('services.update');
    Route::delete('/services/delete/{id}', [ServiceController::class, 'destroy'])->name('services.destroy');
    Route::post('/services/createIncident/{id}', [ServiceController::class, 'createIncident'])->name('services.createIncident');
    Route::delete('/service-equip/{id}', [ServiceController::class, 'destroyServiceEquip'])->name('services.destroyServiceEquip');
    Route::post('/service/setContragentServiceData', [ServiceController::class, 'setContragentServiceData'])->name('services.setContragentServiceData');

    Route::get('/incident', [IncidentController::class, 'index'])->name('incident.index');
    Route::get('/incident/history', [IncidentController::class, 'history'])->name('incident.history');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notification.index');


    Route::get('/sale', [SaleController::class, 'index'])->name('sale.index');
    Route::get('/sale/create', [SaleController::class, 'create'])->name('sale.create');
    Route::post('/sales', [SaleController::class, 'store'])->name('sale.store');
    Route::delete('/sale/delete/{id}', [SaleController::class, 'destroy'])->name('sale.destroy');
    Route::get('/sale/edit/{id}', [SaleController::class, 'edit'])->name('sale.edit');
    Route::put('/sale/update/{id}', [SaleController::class, 'update'])->name('sale.update');
    Route::post('/sale/setContragentServiceData', [SaleController::class, 'setContragentSaleData'])->name('sale.setContragentSaleData');
    Route::delete('/sale/deleteEquipment', [SaleController::class, 'deleteEquipment'])->name('sale.deleteEquipment');
    Route::delete('/sale/deleteSubEquipment', [SaleController::class, 'deleteSubEquipment'])->name('sale.deleteSubEquipment');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profileAvatar', [ProfileController::class, 'updateAvatar'])->name('profile.updateAvatar');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    Route::get('/directory/{type}/{id}', [DirectoryController::class, 'index'])->name('directory.index');
    Route::post('/directory/{type}/{id}', [DirectoryController::class, 'store'])->name('directory.store');
    Route::delete('/directory/{fileId}', [DirectoryController::class, 'deleteFile'])->name('directory.deleteFile');

    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::post('/approve/{id}', [AdminController::class, 'approveUser'])->name('admin.approve');


    Route::get('/search', [SearchController::class, 'search']);
    Route::get('/search-results', [SearchController::class, 'index'])->name('search.index');

    Route::post('/changeLocation', [EquipmentController::class, 'changeLocation'])->name('location.change');

    Route::post('/setContSale', [SaleController::class, 'setContSale'])->name('sale.contr');
});



require __DIR__ . '/auth.php';
