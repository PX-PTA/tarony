<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Gps;
use App\Models\Waktu;
use App\Models\Mesin;
use App\Models\Arus;
use Carbon\Carbon;

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
    $waktu = Waktu::get()->orderBy('created_at','desc')->take(50);
    return $waktu;
});

Route::get('/arus', function () {
    $arus = Arus::get()->orderBy('created_at','desc')->take(50);
    return $arus;
});

Route::get('/device/{device}', function (Mesin $device) {
    $arus = Arus::latest('created_at')->first();
    $mutable = Carbon::now();
    if($arus){
        if($mutable->add(-5,'minute') > $arus->created_at){
            $device->is_online = 0;
            $device->is_active = 0;
            $device->save();
        }
    }
    $waktu = Waktu::where('is_reset',false)->sum('detik');
    $device->waktu = $waktu;
    return $device;
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
    $device->is_active = true;
    $device->is_online = true;
    if(!is_null($request->detik)){
        $newWaktuData = new Waktu;
        $newWaktuData->alat = "Alat ".$device->id;
        $newWaktuData->detik = $request->detik;
        $newWaktuData->save();
    }
    if(!is_null($request->arus)){
        $newArusData = new Arus;
        $newArusData->alat = "Alat ".$device->id;
        $newArusData->arus = $request->arus;
        $newArusData->save();
    }
    if(!is_null($request->off)){
        $device->off_avaiable = $request->off;
    }
    $device->save();
    return [$newWaktuData,$newArusData];
});

Route::post('/onoff/{device}', function (Request $request, Mesin $device) {
    $device->is_on = $request->is_on;
    $device->save();
    return $device;
});

Route::post('/saveBatas/{device}', function (Request $request, Mesin $device) {
    $device->batas_on = $request->batas;
    $device->save();
    return $device;
});

Route::get('/reset/{device}', function (Mesin $device) {
    $waktu = Waktu::where('is_reset',false)->sum('detik');
    $device->waktu = $waktu;
    $updateWaktu = DB::table('waktu')->update(array('is_reset' => true));
    return $device;
});