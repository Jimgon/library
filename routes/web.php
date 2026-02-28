<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\BorrowController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UtilitiesController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\AdminSetupController;
use Illuminate\Support\Facades\Auth;

// ------------------ LOGIN ROUTES ------------------
Route::get('/', fn() => redirect()->route('login'));
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/create-admin', [AdminSetupController::class, 'showCreateForm'])->name('admin.create');
Route::post('/create-admin', [AdminSetupController::class, 'create'])->name('admin.store');

// ------------------ AUTHENTICATED ROUTES ------------------
Route::middleware(['auth'])->group(function () {
    // Reports page
    Route::get('reports', [\App\Http\Controllers\DashboardController::class, 'reports'])->name('reports');
    Route::get('teachers', [\App\Http\Controllers\TeacherController::class, 'index'])->name('teachers.index');
    Route::get('teachers/create', [\App\Http\Controllers\TeacherController::class, 'create'])->name('teachers.create');
    Route::post('teachers', [\App\Http\Controllers\TeacherController::class, 'store'])->name('teachers.store');
    Route::get('teachers/import', [\App\Http\Controllers\TeacherController::class, 'importForm'])->name('teachers.import.form');
    Route::post('teachers/import', [\App\Http\Controllers\TeacherController::class, 'import'])->name('teachers.import');
    Route::get('teachers/{teacher}/edit', [\App\Http\Controllers\TeacherController::class, 'edit'])->name('teachers.edit');
    Route::put('teachers/{teacher}', [\App\Http\Controllers\TeacherController::class, 'update'])->name('teachers.update');
    Route::patch('teachers/{teacher}/remark', [\App\Http\Controllers\TeacherController::class, 'updateRemark'])->name('teachers.updateRemark');
    Route::delete('teachers/{teacher}', [\App\Http\Controllers\TeacherController::class, 'destroy'])->name('teachers.destroy');

    // Dashboard (all authenticated users)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Books & Students (all authenticated users)
    Route::get('books/import', [BookController::class, 'showImportForm'])->name('books.import');
    Route::post('books/import', [BookController::class, 'import'])->name('books.import.post');
    Route::get('books/catalog', [BookController::class, 'catalog'])->name('books.catalog');
    // Route::get('books/distribute/create', [BookController::class, 'distributeCreate'])->name('books.distribute.create');
    // Route::post('books/distribute', [BookController::class, 'distributeStore'])->name('books.distribute.store');
    // Route::post('books/distribute/import', [BookController::class, 'distributeImport'])->name('books.distribute.import.post');

    // Distributed book show / edit / update (parameter routes defined after create/import)
    Route::get('books/distribute/{id}', [BookController::class, 'distributeShow'])->name('books.distribute.show');
    Route::get('books/distribute/{id}/edit', [BookController::class, 'distributeEdit'])->name('books.distribute.edit');
    Route::put('books/distribute/{id}', [BookController::class, 'distributeUpdate'])->name('books.distribute.update');

    Route::delete('books/distribute/{id}', [BookController::class, 'distributeDestroy'])->name('books.distribute.destroy');
    Route::get('books/print', [BookController::class, 'printAll'])->name('books.print');
    Route::post('books/{book}/add-copies', [BookController::class, 'addCopies'])->name('books.addCopies');
    Route::resource('books', BookController::class);
    Route::get('books/api/next-control-base', [BookController::class, 'getNextControlBase'])->name('books.api.nextControlBase');
    Route::get('books/catalog', [BookController::class, 'catalog'])->name('books.catalog');
    // Original controller route
    Route::post('users/bulk-delete', [UserController::class, 'bulkDelete'])->name('users.bulkDelete');
    Route::resource('users', UserController::class);
    // Add named routes for students and teachers lists
    Route::get('users/teachers', [UserController::class, 'teachers'])->name('users.teachers');
    Route::get('users/students', [UserController::class, 'students'])->name('users.students');

    // Utilities backup download/list routes
    Route::get('utilities/backups', [\App\Http\Controllers\UtilitiesController::class, 'listBackups'])->name('utilities.backups');
    Route::get('utilities/download-backup/{filename}', [\App\Http\Controllers\UtilitiesController::class, 'downloadBackup'])->name('utilities.downloadBackup');
    // ...existing code...
    Route::post('users/import', [UserController::class, 'import'])->name('users.import');
    Route::patch('users/{user}/remark', [UserController::class, 'updateRemark'])->name('users.updateRemark');
    

    // Borrow routes
    Route::prefix('borrow')->group(function () {
        Route::get('create', [BorrowController::class, 'create'])->name('borrow.create');
        Route::post('/', [BorrowController::class, 'store'])->name('borrow.store');
        Route::get('distribute', [BorrowController::class, 'createForDistribute'])->name('borrow.distribute');
        Route::post('distribute', [BorrowController::class, 'storeForDistribute'])->name('borrow.distribute.store');
            // Teacher borrow routes
            Route::get('teacher/create', [\App\Http\Controllers\TeacherBorrowController::class, 'create'])->name('borrow.teacher.create');
            Route::post('teacher', [\App\Http\Controllers\TeacherBorrowController::class, 'store'])->name('borrow.teacher.store');
        Route::get('return', [BorrowController::class, 'returnIndex'])->name('borrow.return.index');
        Route::post('return/{borrow}', [BorrowController::class, 'processReturn'])->name('borrow.return.process'); 
        Route::get('{borrow}/receipt', [BorrowController::class, 'receipt'])->name('borrow.receipt');
        Route::get('receipt/{borrow}/print', [BorrowController::class, 'printReceipt'])->name('borrow.receipt.print');
    });

    // Admin-only staff management routes
    Route::prefix('staff')->group(function () {
        Route::get('/', [UserManagementController::class, 'index'])->name('staff.index');
        Route::get('/create', [UserManagementController::class, 'create'])->name('staff.create');
        Route::post('/', [UserManagementController::class, 'store'])->name('staff.store');
        Route::get('/{user}/edit', [UserManagementController::class, 'edit'])->name('staff.edit');
        Route::put('/{user}', [UserManagementController::class, 'update'])->name('staff.update');
        Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('staff.destroy');
    });

    // Utilities routes
    Route::prefix('utilities')->group(function () {
        Route::get('/', [UtilitiesController::class, 'index'])->name('utilities.index');
        Route::get('/logs', [UtilitiesController::class, 'logs'])->name('utilities.logs');

        // Archive routes (GET to show, PATCH/DELETE for actions)
        Route::get('/archive', [UtilitiesController::class, 'archive'])->name('utilities.archive'); 

        // Restore single item
        Route::patch('/archive/restore/{model}/{id}', [UtilitiesController::class, 'restore'])->name('utilities.restore');

        // Restore all items of a model
        Route::patch('/archive/restore-all/{model}', [UtilitiesController::class, 'restoreAll'])->name('utilities.restoreAll');

        // Delete single item permanently
        Route::delete('/archive/delete/{model}/{id}', [UtilitiesController::class, 'delete'])->name('utilities.delete');

        // Delete all items of a model permanently
        Route::delete('/archive/delete-all/{model}', [UtilitiesController::class, 'deleteAll'])->name('utilities.deleteAll');

        Route::post('/backup', [UtilitiesController::class, 'backup'])->name('utilities.backup');
    });

});
