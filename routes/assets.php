<?php

use App\Enums\Department\Allowed;
use App\Http\Controllers\AssetController;

Route::middleware('department:' . Allowed::SYSTEM_TI->value)->group(function () {

    Route::get('/assets', [AssetController::class, 'renderView'])->name('assets');
    Route::get('/assets/users', [AssetController::class, 'renderUsers'])->name('assets.users');
    Route::get('/assets/departments', [AssetController::class, 'renderDepartments'])->name('assets.departments');
    Route::get('/assets/{asset}', [AssetController::class, 'renderAsset'])->name('assets.show');
    Route::get('/assets/{asset}/histories', [AssetController::class, 'renderHistories'])->name('assets.histories');

    Route::post('/assets', [AssetController::class, 'storeAsset'])->name('assets.store');
    Route::put('/assets/{asset}', [AssetController::class, 'updateAsset'])->name('assets.update');
    Route::post('/assets/assign', [AssetController::class, 'assignAsset'])->name('assets.assign');
    Route::post('/assets/devolve/{assignment}', [AssetController::class, 'devolveAsset'])->name('assets.devolve');
    Route::put('/assets/status/{asset}', [AssetController::class, 'changeAssetStatus'])->name('assets.changeStatus');
    Route::get('/assets/generate-laptop-assignment-doc/{assignment}', [AssetController::class, 'generateLaptopAssignmentDocument'])->name('assets.generateLaptopAssignmentDocument');
    Route::get('/assets/generate-phone-assignment-doc/{assignment}', [AssetController::class, 'generateCellphoneAssignmentDocument'])->name('assets.generatePhoneAssignmentDocument');
    Route::get('/assets/generate-return-doc/{assignment}', [AssetController::class, 'generateReturnDocument'])->name('assets.generateReturnDocument');
    Route::post('/assets/delivery-records/{assignment}', [AssetController::class, 'uploadDeliveryRecord'])->name('assets.uploadDeliveryRecord');
    Route::post('/assets/invoice-documents/{asset}', [AssetController::class, 'uploadInvoiceDocument'])->name('assets.uploadInvoiceDocument');

    Route::post('/assets/types', [AssetController::class, 'registerType'])->name('assets.types.register');
    Route::delete('/assets/types', [AssetController::class, 'deleteType'])->name('assets.types.delete');
});
