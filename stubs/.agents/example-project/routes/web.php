<?php

use Illuminate\Support\Facades\Route;

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Products CRUD
    Route::get('/products', \App\Livewire\Admin\Product\Index::class)->name('product.index');
    Route::get('/products/create', \App\Livewire\Admin\Product\Create::class)->name('product.create');
    Route::get('/products/{product}', \App\Livewire\Admin\Product\Show::class)->whereNumber('product')->name('product.show');
    Route::get('/products/{product}/edit', \App\Livewire\Admin\Product\Edit::class)->whereNumber('product')->name('product.edit');

    // Settings / Dictionaries CRUD
    Route::get('/settings', \App\Livewire\Admin\Setting\Index::class)->name('setting.index');
    Route::get('/settings/create/{flag?}/{key?}', \App\Livewire\Admin\Setting\Create::class)->name('setting.create');
    Route::get('/settings/{setting}', \App\Livewire\Admin\Setting\Show::class)->whereNumber('setting')->name('setting.show');
    Route::get('/settings/{setting}/edit', \App\Livewire\Admin\Setting\Edit::class)->whereNumber('setting')->name('setting.edit');

    // Static UI Translations CRUD
    Route::get('/translations', \App\Livewire\Admin\Translation\Index::class)->name('translation.index');
    Route::get('/translations/create', \App\Livewire\Admin\Translation\Create::class)->name('translation.create');
    Route::get('/translations/{key}', \App\Livewire\Admin\Translation\Show::class)->where('key', '.+')->name('translation.show');
    Route::get('/translations/{key}/edit', \App\Livewire\Admin\Translation\Edit::class)->where('key', '.+')->name('translation.edit');
});
