<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Gps;
use App\Models\Waktu;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/waktu', function () {
    $waktu = Waktu::all();
    return $waktu;
});

Route::get('gps', function () {
    $gps = Gps::all();
    return $gps;
});

Route::post('/gps/{device}', function (Request $request,$device) {
    $newGpsData = new Gps;
    $newGpsData->alat = "Alat ".$device;
    $newGpsData->lang = $request->lang;
    $newGpsData->long = $request->long;
    $newGpsData->save();
    return $newGpsData;
});

Route::post('/waktu/{device}', function (Request $request,$device) {
    $newWaktuData = new Waktu;
    $newWaktuData->alat = "Alat ".$device;
    $newWaktuData->detik = $request->detik;
    $newWaktuData->save();
    return $newWaktuData;
});
