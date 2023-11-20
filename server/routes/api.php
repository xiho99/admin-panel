<?php

use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\API\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
Route::controller(\App\Http\Controllers\UploadController::class)->group(function () {
    Route::post('uploadFile', 'upload');
});
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
        Route::get('getAdsBanner', 'get');
        Route::post('saveAds', 'saveAds');
        Route::post('updateAds', 'updateAds');
        Route::post('deleteAds', 'deleteAds');
    });
});
Route::controller(\App\Http\Controllers\Admin\MenuIconController::class)->group(function () {
    Route::group(['middleware' => 'jwt'], function ($router) {
        Route::get('getMenuIcon', 'get');
        Route::post('saveMenuIcon', 'saveMenuIcon');
        Route::post('deleteMenuIcon', 'deleteMenuIcon');
    });
});
Route::controller(\App\Http\Controllers\Admin\MenuButtonController::class)->group(function () {
    Route::group(['middleware' => 'jwt'], function ($router) {
        Route::get('getMenuButton', 'get');
        Route::post('saveMenuButton', 'saveMenuButton');
        Route::post('deleteMenuButton', 'deleteMenuButton');
    });
});
Route::controller(\App\Http\Controllers\Admin\CategoryController::class)->group(function () {
    Route::group(['middleware' => 'jwt'], function () {
        Route::get('getCategory', 'get');
        Route::post('saveCategory', 'saveCategory');
        Route::post('updateCategory', 'updateCategory');
        Route::post('deleteCategory', 'deleteCategory');
    });
});

Route::controller(\App\Http\Controllers\Admin\GroupCategoryController::class)->group(function () {
    Route::group(['middleware' => 'jwt'], function () {
        Route::get('getGroupCategory ', 'get');
        Route::post('saveGroupCategory', 'saveGroupCategory');
        Route::post('updateGroupCategory', 'updateGroupCategory');
        Route::post('deleteGroupCategory', 'deleteGroupCategory');
    });
});
// todo ** API for front-site **
Route::controller(\App\Http\Controllers\API\FrontEndController::class)->group(function () {
    Route::get('getConfigurationList ', 'getConfigurations');
    Route::get('getMenuIconList', 'menuIconList');
    Route::get('getMenuButtonList', 'menuButtonList');
    Route::get('getMenuList', 'getMenuList');
    Route::get('getGroupList', 'getGroupList');
    Route::get('getAdsList', 'getAds');
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
