<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Gps;
use App\Models\Waktu;
use App\Models\Mesin;
use App\Models\Arus;
use App\Models\DeviceOnOffHistory;
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
    $waktu = Waktu::all()->take(10)->orderBy('created_at','desc');
    return $waktu;
});

Route::get('/arus', function () {
    $arus = Arus::all()->take(10)->orderBy('created_at','desc');
    return $arus;
});

Route::get('export', function () {
    $mutable = Carbon::now();

    $arus = Arus::orderBy('created_at','desc')->limit(250)->get();
    $waktu =  Waktu::orderBy('created_at','desc')->limit(250)->get();
    
    $filename = "data-".$mutable->format("d-m-y h-i-s").".csv";
    $handle = fopen($filename, 'w+');
    fputcsv($handle, array('Nama Alat', 'Nilai Arus', 'Nilai Detik', 'Waktu Data Masuk'));

    for($i = 0; $i < $arus->count(); $i++){
        fputcsv($handle, array($arus[$i]['alat'], $arus[$i]['arus'],$waktu[$i]['detik'], $arus[$i]['created_at']));
    }

    fclose($handle);

    $headers = array(
        'Content-Type' => 'text/csv',
    );

    return Response::download($filename, 'data-'.$mutable->format("d-m-y h-i-s").'.csv', $headers);
});

Route::get('/device/{device}', function (Mesin $device) {
    $arus = Arus::latest('created_at')->first();
    $arusTinggi = Arus::where('arus','>',0.05)->orderBy('created_at','desc')->first();
    $mutable = Carbon::now();
    if($arus){
        if($mutable->add(-20,'second') > $arus->created_at){
            $device->is_online = 0;
        }
        if($mutable->add(-20,'second') > $arusTinggi->created_at){
            if($device->is_on == 1){
                $device->is_active = 0;
            }else{
                $device->is_active = 1;
            }
        }
        $device->save();
    }
    $waktu = Waktu::where('is_reset',false)->sum('detik');
    $device->waktu = $waktu;
    return $device;
});

Route::post('/waktu/{device}', function (Request $request,$device) {
    $newWaktuData = new Waktu;
    $newWaktuData->alat = "Mesin Polisher ".$device;
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
        $newWaktuData->alat = "Mesin Polisher ".$device->id;
        $newWaktuData->detik = ($request->detik - ($request->detik*0.3));
        $newWaktuData->save();
    }
    if(!is_null($request->arus)){
        $newArusData = new Arus;
        $newArusData->alat = "Mesin Polisher ".$device->id;
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
    // $newDeviceOnOffHistory = new DeviceOnOffHistory;
    // $newDeviceOnOffHistory->alat = "Mesin Polisher ".$device;
    // $newDeviceOnOffHistory->action = $request->is_on;
    // $newDeviceOnOffHistory->save();
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
    $device->is_on = 0;
    $device->save();
    $updateWaktu = DB::table('waktu')->update(array('is_reset' => true));

    // $newDeviceOnOffHistory = new DeviceOnOffHistory;
    // $newDeviceOnOffHistory->alat = "Mesin Polisher ".$device;
    // $newDeviceOnOffHistory->action = 3;
    // $newDeviceOnOffHistory->save();

    return $device;
});