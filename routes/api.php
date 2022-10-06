<?php

use App\Http\Controllers\Rp1\LogoutBackChannel as Rp1LogoutBackChannel;
use App\Http\Controllers\Rp2\LogoutBackChannel as Rp2LogoutBackChannel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/rp1/logout/backchannel', Rp1LogoutBackChannel::class)->name('rp1.logout.backchannel');
Route::post('/rp2/logout/backchannel', Rp2LogoutBackChannel::class)->name('rp2.logout.backchannel');
