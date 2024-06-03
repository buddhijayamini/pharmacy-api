<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\MedicationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Medication Routes
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/medications', [MedicationController::class, 'index']);
    Route::get('/medications/{medication}', [MedicationController::class, 'show']);
    Route::middleware('role:Owner')->group(function () {
        Route::post('/medications', [MedicationController::class, 'store']);
        Route::delete('/medications/{medication}', [MedicationController::class, 'destroy']);
        Route::patch('/medications/{medication}/restore', [MedicationController::class, 'restore']);
    });
    Route::middleware('role:Owner,Manager')->group(function () {
        Route::put('/medications/{medication}', [MedicationController::class, 'update']);
        Route::delete('/medications/{medication}/soft', [MedicationController::class, 'softDelete']);
    });
    Route::middleware('role:Owner,Manager,Cashier')->group(function () {
        Route::patch('/medications/{medication}', [MedicationController::class, 'update']);
    });
});

// Customer Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/customers', [CustomerController::class, 'index']);
    Route::get('/customers/{customer}', [CustomerController::class, 'show']);
    Route::middleware('role:Owner')->group(function () {
        Route::post('/customers', [CustomerController::class, 'store']);
        Route::delete('/customers/{customer}', [CustomerController::class, 'destroy']);
        Route::patch('/customers/{customer}/restore', [CustomerController::class, 'restore']);
    });
    Route::middleware('role:Owner,Manager')->group(function () {
        Route::put('/customers/{customer}', [CustomerController::class, 'update']);
        Route::delete('/customers/{customer}/soft', [CustomerController::class, 'softDelete']);
    });
    Route::middleware('role:Owner,Manager,Cashier')->group(function () {
        Route::patch('/customers/{customer}', [CustomerController::class, 'update']);
    });
});
