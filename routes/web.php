<?php

use App\Http\Controllers\Checks\BankController;
use App\Http\Controllers\Checks\CheckController;
use App\Http\Controllers\Checks\DocumentController;
use App\Http\Controllers\Checks\PDFController;
use App\Http\Controllers\Checks\SupplierController;
use App\Http\Controllers\CurrentAccount\ReciboController;
use App\Http\Controllers\CurrentAccount\CuentaController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ExcelController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MunicipalityController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes(['verify' => true, 'remember_me'=>false, 'register' => false]);

Route::group(['middleware'=> ['auth', 'verified', 'throttle:web']], function () {
    Route::group(['middleware'=>['has.role:Administrador']], function () {
        // Apis
        Route::resource('/api/web/department', DepartmentController::class);
        Route::resource('/api/web/municipality', MunicipalityController::class);
        Route::resource('/api/web/user', UserController::class);
        Route::resource('/api/web/role', RoleController::class);

        // Views
        Route::get('/departments', function () {
            return view('department.index');
        });

        Route::get('/municipalities', function () {
            return view('municipality.index');
        });

        Route::get('/users', function () {
            return view('user.index');
        });
    });

    //Reports
    Route::get('generate-pdf', [PDFController::class, 'generatePDF']);

    //Excel
    Route::get('export', [ExcelController::class, 'export']);

    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::get('generateCheck/{id}', [PDFController::class, 'generateCheck']);
    Route::get('generateSummary/{id}', [PDFController::class, 'generateSummary']);
    Route::get('downloadReceipt/{id}', [ReciboController::class, 'downloadReceipt']);

    // Cheques
    Route::resource('api/supplier', SupplierController::class);
    Route::resource('api/document', DocumentController::class);
    Route::resource('api/bank', BankController::class);
    Route::resource('api/web/receipt', ReciboController::class);
    Route::resource('api/web/account', CuentaController::class);
    Route::get('api/check/summary', [CheckController::class, 'summary']);
    Route::resource('api/check', CheckController::class);

    Route::get('/checks', function () {
        return view('checks.check.index');
    });


    Route::get('/', [HomeController::class, 'index']);

    Route::get('/suppliers', function () {
        return view('checks.supplier.index');
    });

    Route::get('/documents', function () {
        return view('checks.document.index');
    });

    Route::get('/banks', function () {
        return view('checks.bank.index');
    });

    Route::get('/reports', function () {
        return view('checks.reports');
    });

    Route::get('/summary', function () {
        return view('checks.summary.index');
    });
    // Fin Cheques

    // Current accounts
    Route::get('/receipts', function () {
        return view('currentAccounts.recibo.index');
    });

    Route::get('/accounts', function () {
        return view('currentAccounts.account.index');
    });


    Route::get('/profile', function () {
        return view('checks.checks.profile');
    });

    Route::get('/usuarioActual', [UserController::class, 'usuarioActual']);
    Route::put('/api/user/{user}', [UserController::class, 'update']);
});

Route::post('import', [ExcelController::class, 'import']);
