<?php

use App\Http\Controllers\Hydra\AcceptConsent;
use App\Http\Controllers\Hydra\ConsentProvider;
use App\Http\Controllers\Hydra\Login;
use App\Http\Controllers\Hydra\LoginProvider;
use App\Http\Controllers\Hydra\RejectConsent;
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
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::get('/oauth2/login', LoginProvider::class)->name('oauth2.login');
Route::post('/oauth2/login', Login::class);

Route::get('/oauth2/consent', ConsentProvider::class)->name('oauth2.consent');
Route::post('/oauth2/consent/accept', AcceptConsent::class)->name('oauth2.consent.accept');
Route::post('/oauth2/consent/reject', RejectConsent::class)->name('oauth2.consent.reject');

require __DIR__.'/auth.php';
