<?php

use App\Http\Controllers\Admin\MenuController;
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
Route::prefix('auth')->controller(\App\Http\Controllers\Admin\AdminController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'saveAdmin');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');

});

Route::controller(MenuController::class)->group(function () {
    Route::group(['middleware' => 'jwt'], function ($router) {
        Route::get('getMenu', 'adminMenu');
        Route::post('addMenu', 'saveMenu');
        Route::post('deleteMenu', 'deleteMenu');
    });
});

Route::controller(\App\Http\Controllers\Admin\RoleController::class)->group(function () {
    Route::group(['middleware' => 'jwt'], function ($router) {
        Route::post('roleList', 'getList');
        Route::post('saveRole', 'saveRole')->middleware(['LogOperation']);
        Route::post('deleteRole', 'deleteRole')->middleware(['LogOperation']);
        Route::post('getAllRole', 'getAllRole');
    });
});
Route::controller(\App\Http\Controllers\Admin\AdminController::class)->group(function () {
    Route::group(['middleware' => 'jwt'], function ($router) {
        Route::post('getAdmin', 'adminList');
        Route::post('saveAdmin', 'saveAdmin')->middleware(['LogOperation']);
        Route::post('deleteAdmin', 'deleteAdmin')->middleware(['LogOperation']);
    });
});
Route::controller(\App\Http\Controllers\Admin\ConfigurationController::class)->group(function () {
    Route::group(['middleware' => 'jwt'], function ($router) {
        Route::get('getConfiguration', 'get');
        Route::post('saveConfiguration', 'saveConfiguration');
        Route::post('updateConfiguration', 'updateConfiguration');
        Route::post('deleteConfiguration', 'deleteConfiguration');
    });
});
Route::controller(\App\Http\Controllers\Admin\AdsController::class)->group(function () {
    Route::group(['middleware' => 'jwt'], function ($router) {
        Route::get('getAds', 'get');
        Route::post('saveAds', 'saveAds');
        Route::post('updateAds', 'updateAds');
        Route::post('deleteAds', 'deleteAds');
    });
});
Route::controller(\App\Http\Controllers\Admin\MenuItemController::class)->group(function () {
    Route::group(['middleware' => 'jwt'], function ($router) {
        Route::get('getMenuItem', 'get');
        Route::post('saveMenuItem', 'saveMenuItem');
        Route::post('updateMenuItem', 'updateMenuItem');
        Route::post('deleteMenuItem', 'deleteMenuItem');
    });
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
