<?php

use App\Http\Livewire\Admin\ShowProducts;
use Illuminate\Support\Facades\Route;
use App\Http\Livewire\Admin\CreateProduct;
use App\Http\Livewire\Admin\EditProduct;

Route::get('/', ShowProducts::class)->name('admin.index');

Route::get('products/{product}/edit', function () {
})->name('admin.products.edit');

Route::get('products/create', function () {
})->name('admin.products.create');

Route::get('products/create', CreateProduct::class)->name('admin.products.create');

Route::get('products/{product}/edit', EditProduct::class)->name('admin.products.edit');


