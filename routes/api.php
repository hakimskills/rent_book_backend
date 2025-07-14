<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\RentController;

// 🔓 Public Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// 🔐 Protected Routes (any authenticated user)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // 📚 Book Routes (only for book owners)
    Route::middleware('role:book_owner')->group(function () {
        Route::get('/books', [BookController::class, 'index']);
        Route::post('/books', [BookController::class, 'store']);
        Route::put('/books/{book}', [BookController::class, 'update']);
        Route::delete('/books/{book}', [BookController::class, 'destroy']);
    });

    // 📦 Rent Routes (only for readers)
    Route::middleware('role:reader')->group(function () {
        Route::get('/rents', [RentController::class, 'index']);
        Route::post('/rents', [RentController::class, 'store']);
        Route::post('/rents/{id}/return', [RentController::class, 'returnBook']);
    });

    // 🛡️ Admin-only routes (if needed)
    // Route::middleware('role:admin')->group(function () {
    //     // Add admin-only endpoints here
    // });
});
