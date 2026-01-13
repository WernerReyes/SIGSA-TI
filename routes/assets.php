<?php

use App\Enums\Department\Allowed;
use App\Http\Controllers\AssetController;

Route::middleware('department:' . Allowed::SYSTEM_TI->value)->group(function () {

    Route::prefix('assets')->group(function () {

    Route::get('/', [AssetController::class, 'renderView'])->name('assets');
        Route::get('/accessories', [AssetController::class, 'renderAccessories'])->name('assets.accessories');
        Route::get('/{asset}', [AssetController::class, 'renderAsset'])->name('assets.show');
        Route::get('/{asset}/histories', [AssetController::class, 'renderHistories'])->name('assets.histories');

        Route::post('/', [AssetController::class, 'storeAsset'])->name('assets.store');
        Route::put('/{asset}', [AssetController::class, 'updateAsset'])->name('assets.update');
        Route::post('/assign', [AssetController::class, 'assignAsset'])->name('assets.assign');
        Route::post('/devolve/{assignment}', [AssetController::class, 'devolveAsset'])->name('assets.devolve');
        Route::put('/status/{asset}', [AssetController::class, 'changeAssetStatus'])->name('assets.changeStatus');
        Route::get('/generate-laptop-assignment-doc/{assignment}', [AssetController::class, 'generateLaptopAssignmentDocument'])->name('assets.generateLaptopAssignmentDocument');
        Route::get('/generate-phone-assignment-doc/{assignment}', [AssetController::class, 'generateCellphoneAssignmentDocument'])->name('assets.generatePhoneAssignmentDocument');
        Route::get('/generate-accessory-assignment-doc/{assignment}', [AssetController::class, 'generateAccessoryAssignmentDocument'])->name('assets.generateAccessoryAssignmentDocument');
        Route::get('/generate-return-doc/{assignment}', [AssetController::class, 'generateReturnDocument'])->name('assets.generateReturnDocument');
        Route::post('/delivery-records/{assignment}', [AssetController::class, 'uploadDeliveryRecord'])->name('assets.uploadDeliveryRecord');
        Route::post('/invoice-documents/{asset}', [AssetController::class, 'uploadInvoiceDocument'])->name('assets.uploadInvoiceDocument');

        // Route::post('/types', [AssetController::class, 'registerType'])->name('assets.types.register');
        // Route::delete('/types', [AssetController::class, 'deleteType'])->name('assets.types.delete');
    });
});
