<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\enigmaController;

Route::get('/enigma', [enigmaController::class, 'enigma']);
Route::post('/encrypt', [enigmaController::class, 'encrypt']);
Route::post('/decrypt', [enigmaController::class, 'decrypt']);
Route::get('/db', [enigmaController::class, 'db']);


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
