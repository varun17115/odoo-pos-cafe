<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FloorController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Products
    Route::resource('products', ProductController::class);
    Route::delete('/products-bulk', [ProductController::class, 'bulkDelete'])->name('products.bulk-delete');

    // Floors & Tables
    Route::get('/floors', [FloorController::class, 'index'])->name('floors.index');
    Route::post('/floors', [FloorController::class, 'store'])->name('floors.store');
    Route::put('/floors/{floor}', [FloorController::class, 'update'])->name('floors.update');
    Route::delete('/floors/{floor}', [FloorController::class, 'destroy'])->name('floors.destroy');

    Route::post('/floors/{floor}/tables', [FloorController::class, 'storeTable'])->name('floors.tables.store');
    Route::put('/floors/{floor}/tables/{table}', [FloorController::class, 'updateTable'])->name('floors.tables.update');
    Route::delete('/floors/{floor}/tables/{table}', [FloorController::class, 'destroyTable'])->name('floors.tables.destroy');
    Route::delete('/floors/{floor}/tables-bulk', [FloorController::class, 'bulkDestroyTables'])->name('floors.tables.bulk-destroy');
    Route::post('/floors/{floor}/tables-bulk-status', [FloorController::class, 'bulkUpdateStatus'])->name('floors.tables.bulk-status');
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::post('/categories/reorder', [CategoryController::class, 'reorder'])->name('categories.reorder');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
});

require __DIR__.'/auth.php';
