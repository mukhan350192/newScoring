<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\xData;
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

Route::get('/xdata',[xData::class,'index']);
Route::get('/test',[xData::class,'create']);
Route::get('/susn',[xData::class,'susn']);
Route::get('/testQueue',[xData::class,'testQueue']);
Route::get('/garnetQueue',[xData::class,'garnetQueue']);
Route::get('/pdl',[xData::class,'pdl']);
Route::get('/testGarnet',[xData::class,'testGarnet']);
Route::get('/pdlTest',[xData::class,'pdlTest']);
Route::get('/pdlGarnet',[xData::class,'pdlGarnet']);
Route::get('/testtest',[xData::class,'testtest']);
Route::get('/sale',[xData::class,'sale']);
