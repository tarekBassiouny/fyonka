<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\LoginController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\StoreController;
use App\Http\Controllers\Web\TransactionSubtypeController;
use App\Http\Controllers\Web\TransactionController;
use App\Http\Controllers\Web\FileConverterController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [LoginController::class, 'index'])->name('login.index');
Route::get('/login', [LoginController::class, 'show'])->name('login.show');
Route::post('/login/authenticate', [LoginController::class, 'authenticate'])->name('login.authenticate');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::get('lang/{locale}', function ($locale) {
    if (! in_array($locale, ['en', 'de'])) {
        abort(400);
    }
    session(['locale' => $locale]);
    app()->setLocale($locale);
    return back();
})->name('lang.switch');

Route::middleware('auth')->prefix('/')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('dashboard.index');
    Route::get('/cards', [HomeController::class, 'cards'])->name('dashboard.cards');
    Route::get('/charts', [HomeController::class, 'charts'])->name('dashboard.charts');
    Route::get('/transactions/data', [HomeController::class, 'transactions'])->name('dashboard.transactions');
    Route::get('/report/pdf', [HomeController::class, 'generatePdfReport'])->name('dashboard.report.pdf');


    Route::post('/import', [HomeController::class, 'uploadTransactions'])->name('transactions.import');
    Route::resource('stores', StoreController::class)->except(['show', 'create', 'edit']);
    Route::resource('subtypes', TransactionSubtypeController::class)->except(['show', 'create', 'edit']);

    Route::resource('transactions', TransactionController::class)->except(['show']);
    Route::post('/transactions/bulk-approve', [TransactionController::class, 'bulkApprove'])->name('transactions.bulkApprove');
    Route::post('/transactions/bulk-reject', [TransactionController::class, 'bulkReject'])->name('transactions.bulkReject');
    Route::post('/transactions/{transaction}/approve', [TransactionController::class, 'approve'])->name('transactions.approve');
    Route::delete('/transactions/{transaction}/reject', [TransactionController::class, 'reject'])->name('transactions.reject');

    Route::get('/subtypes/by-type/{type}', [TransactionSubtypeController::class, 'byType'])->name('subtypes.byType');

    Route::get('/convert', [FileConverterController::class, 'index'])->name('convert.index');
    Route::post('/convert/excel', [FileConverterController::class, 'convert'])->name('convert.excel');
});
