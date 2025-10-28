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

// Server info page (shows uptime, memory, disk)
Route::get('/server', [\App\Http\Controllers\ServerController::class, 'index'])->name('server.info');

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

// Secure Login routes
Route::middleware(['web', App\Http\Middleware\LoginSecurityMiddleware::class])->group(function () {
    Route::get('/login', [App\Http\Controllers\Auth\SecureLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [App\Http\Controllers\Auth\SecureLoginController::class, 'login'])->name('login.submit');
});

// Logout routes (separate from security middleware to avoid issues)
Route::post('/logout', [App\Http\Controllers\Auth\SecureLoginController::class, 'logout'])->name('logout')->middleware('web');
Route::get('/logout', [App\Http\Controllers\Auth\SecureLoginController::class, 'logout'])->name('logout.get')->middleware('web');

// Registration routes
Route::middleware(['web', App\Http\Middleware\LoginSecurityMiddleware::class])->group(function () {
    Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);
});

// Route::get('/category', function () {
//     return view('category.index');
// })->name('category.index');

Route::resource('products', \App\Http\Controllers\ProductController::class);
Route::get('/list', [\App\Http\Controllers\ProductController::class, 'list'])->name('products.list');
Route::resource('categories', \App\Http\Controllers\CategoryController::class);
Route::post('/products/search', [\App\Http\Controllers\ProductController::class, 'searchProduct'])->name('products.search');
Route::post('/categories/search', [\App\Http\Controllers\CategoryController::class, 'searchCategory'])->name('categories.search');

