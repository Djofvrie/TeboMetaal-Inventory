<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\MutationController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

// Public catalog (no login required)
Route::get('/catalogus', [CatalogController::class, 'index'])->name('catalog');

// Frontend routes require login
Route::middleware(['auth'])->group(function () {
    Route::get('/', [PublicController::class, 'index'])->name('home');
    Route::get('/categorie/{slug}', [PublicController::class, 'category'])->name('category.show');
    Route::get('/product/{product}', [PublicController::class, 'product'])->name('product.show');
    Route::get('/variant/{variant}', [PublicController::class, 'variant'])->name('variant.show');
    Route::get('/zoeken', [PublicController::class, 'search'])->name('search');
    Route::post('/variant/{variant}/afboeken', [PublicController::class, 'deduct'])
        ->middleware('throttle:30,1')
        ->name('variant.deduct');
});

// Dashboard redirect (Breeze auth redirect target)
Route::get('/dashboard', fn() => redirect()->route('admin.dashboard'))->middleware('auth')->name('dashboard');

// Admin routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [StockController::class, 'dashboard'])->name('dashboard');
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::get('/stock', [StockController::class, 'index'])->name('stock.index');
    Route::get('/stock/{variant}', fn(\App\Models\ProductVariant $variant) => redirect()->route('admin.stock.edit', $variant));
    Route::get('/stock/{variant}/edit', [StockController::class, 'edit'])->name('stock.edit');
    Route::put('/stock/{variant}', [StockController::class, 'update'])->name('stock.update');
    Route::post('/stock/{variant}/inventory', [StockController::class, 'inventory'])->name('stock.inventory');
    Route::put('/stock/{variant}/settings', [StockController::class, 'settings'])->name('stock.settings');
    Route::get('/import', [StockController::class, 'importForm'])->name('stock.import');
    Route::post('/import', [StockController::class, 'import'])->name('stock.import.process');
    Route::get('/mutaties', [MutationController::class, 'index'])->name('mutations.index');
    Route::resource('users', UserController::class)->except(['show']);
});

// Profile routes (Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
