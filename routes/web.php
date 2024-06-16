<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'permission:view users'])->group(function () {
    Route::get('users', [ProductController::class, 'getUserList'])->name('users.index');
});

Route::middleware(['auth', 'permission:view products'])->group(function () {
    Route::get('products', [ProductController::class, 'index'])->name('products.index');
    Route::get('products/{product}', [ProductController::class, 'show'])->name('products.show');
});

Route::middleware(['auth', 'permission:manage products'])->group(function () {
    Route::post('products', [ProductController::class, 'store'])->name('products.store');
    Route::get('product/create', [ProductController::class, 'create'])->name('products.create');
    Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::get('products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
});

Route::middleware(['auth', 'permission:manage categories'])->group(function () {
    Route::get('categories/add', [CategoryController::class, 'add'])->name('categories.add');
    Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    Route::get('categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
});

Route::middleware(['auth', 'permission:view categories'])->group(function () {
    Route::get('categories/products', [CategoryController::class, 'getProductsByCategory'])->name('categories.products');
    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
