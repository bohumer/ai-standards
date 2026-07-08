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

    // Settings
    Route::get('/settings', \App\Livewire\Admin\Setting\Index::class)->name('setting.index');
    Route::get('/settings/create', \App\Livewire\Admin\Setting\Create::class)->name('setting.create');
    Route::get('/settings/{setting}', \App\Livewire\Admin\Setting\Show::class)->name('setting.show');
    Route::get('/settings/{setting}/edit', \App\Livewire\Admin\Setting\Edit::class)->name('setting.edit');
    
});
