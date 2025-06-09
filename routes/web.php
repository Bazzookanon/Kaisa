<?php

use Illuminate\Support\Facades\Route;

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
    return redirect()->route('products.index');
})->name('home');

// Route::get('/category', function () {
//     return view('category.index');
// })->name('category.index');

Route::resource('products', \App\Http\Controllers\ProductController::class);
Route::resource('categories', \App\Http\Controllers\CategoryController::class);
Route::post('/products/search', [\App\Http\Controllers\ProductController::class, 'searchProduct'])->name('products.search');
Route::post('/categories/search', [\App\Http\Controllers\CategoryController::class, 'searchCategory'])->name('categories.search');

