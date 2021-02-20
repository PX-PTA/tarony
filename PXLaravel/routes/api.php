<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Gps;
use App\Models\Waktu;
use App\Models\Mesin;
use App\Models\Arus;

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

Route::get('/gps', function () {
    $gps = Gps::all();
    return $gps;
});

Route::get('/onoff', function () {
    $mesin = Mesin::first();
    return $mesin;
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

Route::post('/data/{device}/add', function (Request $request,Mesin $device) {
    $newArusData = null;
    $newGpsData = null;
    $newWaktuData = null;
    if(!is_null($request->detik)){
        $newWaktuData = new Waktu;
        $newWaktuData->alat = "Alat ".$device->id;
        $newWaktuData->detik = $request->detik;
        $newWaktuData->save();
    }
    if(!is_null($request->lang) && !is_null($request->lang)){
        $newGpsData = new Gps;
        $newGpsData->alat = "Alat ".$device->id;
        $newGpsData->lang = $request->lang;
        $newGpsData->long = $request->long;
        $newGpsData->save();
    }
    if(!is_null($request->arus)){
        $newArusData = new Arus;
        $newArusData->alat = "Alat ".$device->id;
        $newArusData->arus = $request->arus;
        $newArusData->save();
    }
    if(!is_null($request->off)){
        $device->off_avaiable = $request->off;
        $device->save();
    }
    return [$newWaktuData,$newArusData,$newGpsData];
});

Route::post('/onoff/{device}', function (Request $request, Mesin $device) {
    $device->is_on = $request->is_on;
    $device->save();
    return $device;
});