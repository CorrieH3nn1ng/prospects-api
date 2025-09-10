<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CallController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TransporterController;
use App\Http\Controllers\InterestLevelController;
use App\Http\Controllers\ClientMoodController;
use App\Http\Controllers\ClientSatisfactionLevelController;
use App\Http\Controllers\PotentialValueController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Authentication routes
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'me']);
});

// Protected routes
Route::middleware('auth:api')->group(function () {
    
    // System management routes
    Route::apiResource('users', UserController::class);
    Route::put('users/{id}/password', [UserController::class, 'updatePassword']);
    Route::apiResource('companies', CompanyController::class);
    Route::get('companies/{id}/stats', [CompanyController::class, 'stats']);
    Route::apiResource('branches', BranchController::class);
    Route::get('branches/{id}/stats', [BranchController::class, 'stats']);
    
    // Application modules routes
    Route::apiResource('calls', CallController::class);
    Route::apiResource('quotes', QuoteController::class);
    Route::apiResource('clients', ClientController::class);
    Route::apiResource('suppliers', SupplierController::class);
    Route::apiResource('transporters', TransporterController::class);
    
    // Settings routes
    Route::apiResource('interest-levels', InterestLevelController::class);
    Route::apiResource('client-moods', ClientMoodController::class);
    Route::apiResource('client-satisfaction-levels', ClientSatisfactionLevelController::class);
    Route::apiResource('potential-values', PotentialValueController::class);
});