<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VisitorController;
use App\Models\Visitor;
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
    return view('welcome');
})->name('welcome');
Route::get('/visitor', function () {
    return view('visitor');
})->name('visitor');

Route::post('submit', [VisitorController::class, 'submit'])->name('submit');
Route::post('card/bonus', [VisitorController::class, 'cardBonus'])->name('cardBonus');
Route::get('/dashboard', [VisitorController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');
Route::post('/destory/{id}',[VisitorController::class, 'destroy'])->name('destroy');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
