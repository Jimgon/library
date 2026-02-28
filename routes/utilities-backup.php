<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UtilitiesController;

Route::middleware(['auth'])->group(function () {
    // ...existing routes...
    Route::get('utilities/backups', [UtilitiesController::class, 'listBackups'])->name('utilities.backups');
    Route::get('utilities/download-backup/{filename}', [UtilitiesController::class, 'downloadBackup'])->name('utilities.downloadBackup');
});
