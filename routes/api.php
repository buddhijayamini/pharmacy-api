<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\MedicationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::group(['prefix' => 'auth', 'as' => 'auth.', 'controller' => AuthController::class], function () {
    Route::post('/register', 'register')->name('register');
    Route::post('/login', 'login')->name('login');
});

// Authenticated Routes
Route::middleware('auth:sanctum')->group(function () {
    // User Routes
    Route::group(['prefix' => 'user', 'as' => 'user.', 'controller' => AuthController::class], function () {
        Route::get('/', 'getUser')->name('get');
        Route::post('/logout', 'logout')->name('logout');
    });

    // Medication Routes
    Route::group(['prefix' => 'medications', 'as' => 'medications.'], function () {
        // Define routes individually and assign controller methods
        Route::get('/', [MedicationController::class, 'index'])->name('index');
        Route::get('/{medication}', [MedicationController::class, 'show'])->name('show');

        // Owner Routes
        Route::middleware('role:owner')->group(function () {
            Route::post('/', [MedicationController::class, 'store'])->name('store');
            Route::put('/{medication}', [MedicationController::class, 'update'])->name('update');
            Route::delete('/{medication}', [MedicationController::class, 'destroy'])->name('destroy');
            Route::patch('/{medication}/restore', [MedicationController::class, 'restore'])->name('restore');
        });

        // Manager Routes
        Route::middleware('role:manager')->group(function () {
            Route::delete('/{medication}/soft', [MedicationController::class, 'softDelete'])->name('softDelete');
        });

        // Cashier Routes
        Route::middleware('role:cashier')->group(function () {
            Route::patch('/{medication}', [MedicationController::class, 'update'])->name('cashierUpdate');
        });
    });

    // Customer Routes
    Route::group(['prefix' => 'customers', 'as' => 'customers.'], function () {
        // Define routes individually and assign controller methods
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('/{customer}', [CustomerController::class, 'show'])->name('show');

        // Owner Routes
        Route::middleware('role:owner')->group(function () {
            Route::post('/', [CustomerController::class, 'store'])->name('store');
            Route::delete('/{customer}', [CustomerController::class, 'destroy'])->name('destroy');
            Route::patch('/{customer}/restore', [CustomerController::class, 'restore'])->name('restore');
        });

        // Owner and Manager Routes
        Route::middleware('role:owner,manager')->group(function () {
            Route::put('/{customer}', [CustomerController::class, 'update'])->name('update');
            Route::delete('/{customer}/soft', [CustomerController::class, 'softDelete'])->name('softDelete');
        });

        // Owner, Manager, and Cashier Routes
        Route::middleware('role:owner,manager,cashier')->group(function () {
            Route::patch('/{customer}', [CustomerController::class, 'update'])->name('cashierUpdate');
        });
    });

});
