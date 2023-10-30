<?php

use App\Http\Controllers\API\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::prefix('auth')->controller(\App\Http\Controllers\API\AdminController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'saveAdmin');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');

});

//Route::controller(\App\Http\Controllers\MonthlyController::class)->group(function () {
//    Route::post('getMonthly', 'get');
//    Route::post('storeMonthly', 'store');
//    Route::put('updateMonthly/{id}', 'update');
//});


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
