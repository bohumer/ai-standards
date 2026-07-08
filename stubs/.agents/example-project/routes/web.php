<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application.
|
*/

// Public routes here...

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard or Admin home
    // Route::get('/', \App\Livewire\Admin\Dashboard::class)->name('dashboard');

    // Products
    Route::get('/products', \App\Livewire\Admin\Product\Index::class)->name('product.index');
    Route::get('/products/create', \App\Livewire\Admin\Product\Create::class)->name('product.create');
    Route::get('/products/{product}', \App\Livewire\Admin\Product\Show::class)->name('product.show');
    Route::get('/products/{product}/edit', \App\Livewire\Admin\Product\Edit::class)->name('product.edit');

    // Settings / Dictionaries
    Route::get('/settings', \App\Livewire\Admin\Setting\Index::class)->name('settings.index');
    Route::get('/settings/{modelName}', \App\Livewire\Admin\Setting\Index::class)->name('settings.model');
    Route::get('/settings/{modelName}/{category}', \App\Livewire\Admin\Setting\Index::class)->name('settings.show');
    
});
