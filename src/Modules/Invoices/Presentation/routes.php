<?php declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Invoices\Presentation\Http\Controllers\InvoiceController;
use Ramsey\Uuid\Validator\GenericValidator;

Route::pattern('reference', (new GenericValidator)->getPattern());

Route::prefix('api')->name('api.')->group(function () {
    Route::prefix('v1')->name('v1.')->group(function () {
        Route::prefix('invoices')->group(function () {
            Route::get('/{reference}/view', [InvoiceController::class, 'view'])->name('invoice.view');
            Route::post('/create', [InvoiceController::class, 'create'])->name('invoice.create');
            Route::post('/{reference}/send', [InvoiceController::class, 'send'])->name('invoice.send');
        });
    });
});
