<?php

use App\Http\Controllers\Hydra\AcceptConsent;
use App\Http\Controllers\Hydra\ConsentProvider;
use App\Http\Controllers\Hydra\Login;
use App\Http\Controllers\Hydra\LoginProvider;
use App\Http\Controllers\Hydra\LogoutProvider;
use App\Http\Controllers\Hydra\RejectConsent;
use App\Http\Controllers\Logout;
use App\Http\Controllers\LogoutCallback;
use App\Http\Controllers\Rp1\Callback as Rp1Callback;
use App\Http\Controllers\Rp1\Login as Rp1Login;
use App\Http\Controllers\Rp1\Logout as Rp1Logout;
use App\Http\Controllers\Rp2\Callback as Rp2Callback;
use App\Http\Controllers\Rp2\Login as Rp2Login;
use App\Http\Controllers\Rp2\Logout as Rp2Logout;
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


Route::get('/rp1/login', Rp1Login::class)->name('rp1.login');
Route::get('/rp1/callback', Rp1Callback::class)->name('rp1.callback');
Route::get('/rp1/logout', Rp1Logout::class)->name('rp1.logout');
Route::get('/rp1/logout/callback', LogoutCallback::class)->name('rp1.logout.callback');

Route::get('/rp2/login', Rp2Login::class)->name('rp2.login');
Route::get('/rp2/callback', Rp2Callback::class)->name('rp2.callback');
Route::get('/rp2/logout', Rp2Logout::class)->name('rp2.logout');
Route::get('/rp2/logout/callback', LogoutCallback::class)->name('rp2.logout.callback');

Route::get('/oauth2/login', LoginProvider::class)->name('oauth2.login');
Route::post('/oauth2/login', Login::class);

Route::get('/oauth2/consent', ConsentProvider::class)->name('oauth2.consent');
Route::post('/oauth2/consent/accept', AcceptConsent::class)->name('oauth2.consent.accept');
Route::post('/oauth2/consent/reject', RejectConsent::class)->name('oauth2.consent.reject');

// Logout Provider
Route::get('/oauth2/logout', LogoutProvider::class)->name('oauth2.logout');

// 啟動 Logout 與 callback
Route::get('/logout', Logout::class)->name('logout');
Route::get('/logout/callback', LogoutCallback::class)->name('logout.callback');


require __DIR__.'/auth.php';
